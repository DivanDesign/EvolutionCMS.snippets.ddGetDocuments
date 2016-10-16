<?php
/**
 * ddGetDocuments
 * @version 0.6 (2016-10-10)
 * 
 * A snippet for fetching and parsing resources from the document tree by a custom rule.
 * 
 * @param $provider {'parent'|'select'} — Name of the provider that will be used to fetch documents. Default: 'parent'.
 * @param $providerParams {string_query} — Parameters to be passed to the provider. The parameter must be set as a query string,
 * When $provider == 'parent' =>
 * @param $providerParams['parentIds'] {array|string_commaSepareted} — Parent IDs. Default: 0.
 * @param $providerParams['depth'] {integer} — Depth of children documents search. Default: 1.
 * e.g. $providerParams = 'parentIds=1&depth=2'.
 * @param $fieldDelimiter {string} — The field delimiter to be used in order to distinct data base column names in those parameters which can contain SQL queries directly, e. g. $orderBy and $filter. Default: '`'.
 * 
 * @param $total {integer} — The maximum number of the resources that will be returned. Default: —.
 * @param $filter {string} — The filter condition in SQL-style to be applied while resource fetching. Default: '`published` = 1 AND `deleted` = 0';
 * Notice that all fields/tvs names specified in the filter parameter must be wrapped in $fieldDelimiter.
 * @param $offset {integer} — Resources offset. Default: 0.
 * @param $orderBy {string} — A string representing the sorting rule. Default: '`id` ASC'.
 * 
 * @param $outputFormat {'string'|'json'|'raw'} — Format of the output. Default: 'string'.
 * @param $outputFormatParams {string_query} — Parameters to be passed to the specified formatter. The parameter must be set as a query string,
 * When $outputFormat == 'string' =>
 * @param $outputFormatParams['itemTpl'] {string_chunkName} — Item template. Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
 * @param $outputFormatParams['itemTplFirst'] {string_chunkName} — First item template. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
 * @param $outputFormatParams['itemTplLast'] {string_chunkName} — Last item template. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
 * @param $outputFormatParams['wrapperTpl'] {string_chunkName} — Wrapper template. Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
 * @param $outputFormatParams['noResults'] {string|string_chunkName} — A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+]. 
 * @param $outputFormatParams['resultToPlaceholder'] {string} - The name of the global MODX placeholder that holds the snippet result. The result will be returned in a regular manner if the parameter is empty. Default: ''.
 * e.g. 'itemTpl=chunk_1&wrapperTpl=chunk_2&noResults=No items found'.
 * When $outputFormat == 'json' =>
 * @param $outputFormatParams['docFields'] {array|string_commaSeparated} — Document fields to output (including TVs). Default: 'id'.
 * e.g. 'docFields=id,pagetitle,introtext'.
 * 
 * @param $extenders {string_commaSeparated} — Comma-separated string determining which extenders should be applied to the snippet.
 * Be aware that the order of extender names can affect the output.
 * @param $extendersParams {string_query} — Parameters to be passed to their corresponding extensions. The parameter must be set as a query string,
 * When $extenders == 'pagination' =>
 * @param $extendersParams['wrapperTpl'] {string_chunkName} — Chunk to be used to output the pagination. @required
 * @param $extendersParams['pageTpl'] {string_chunkName} — Chunk to be used to output pages within the pagination. @required
 * @param $extendersParams['currentPageTpl'] {string_chunkName} — Chunk to be used to output the current page within the pagination. @required
 * @param $extendersParams['nextTpl'] {string_chunkName} — Chunk to be used to output the navigation block to the next page. @required
 * @param $extendersParams['nextOffTpl'] {string_chunkName} — Chunk to be used to output the navigation block to the next page if there are no more pages after. @required
 * @param $extendersParams['previousTpl'] {string_chunkName} — Chunk to be used to output the navigation block to the previous page. @required
 * @param $extendersParams['previousOffTpl'] {string_chunkName} — Chunk to be used to output the navigation block to the previous page if there are no more pages before. @required
 * e.g. $extendersParams = 'wrapperTpl=pagination&nextTpl=pagination_next&previousTpl=pagination_previous&nextOffTpl=pagination_nextOff&previousOffTpl=pagination_previousOff&pageTpl=pagination_page&currentPageTpl=pagination_page_current'.
 **/

global $modx;
$output = false;

if(is_file($modx->config['base_path'].'vendor/autoload.php')){
	require_once($modx->config['base_path'].'vendor/autoload.php');
}

if(!class_exists('\ddTools')){
	require_once($modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php');
}

if(!class_exists('\ddGetDocuments\DataProvider\DataProvider')){
	require_once($modx->config['base_path'].'assets/snippets/ddGetDocuments/require.php');
}

//General
$total = isset($total) ? $total : null;
$offset = isset($offset) ? $offset : 0;
$orderBy = isset($orderBy) ? $orderBy : '`id` ASC';
$filter = isset($filter) ? $filter : null;
$fieldDelimiter = isset($fieldDelimiter) ? $fieldDelimiter : '`';

//Data provider
$provider = isset($provider) ? $provider : 'parent';
$providerClass = \ddGetDocuments\DataProvider\DataProvider::includeProviderByName($provider);
$providerParams = isset($providerParams) ? $providerParams : '';

//Output format
$outputFormat = isset($outputFormat) ? strtolower($outputFormat) : 'string';
$outputFormatParams = isset($outputFormatParams) ? $outputFormatParams : '';

//Extenders
$extenders = isset($extenders) ? $extenders : '';
$extendersParams = isset($extendersParams) ? $extendersParams : '';

if(class_exists($providerClass)){
	$dataProvider = new $providerClass;
	//Prepare provider params
	parse_str($providerParams, $providerParamsArray);
	
	$extendersNamesArray = array();
	
	if($extenders != ''){
		$extendersNamesArray = explode(',', $extenders);
	}
	//Prepare extender params
	parse_str($extendersParams, $extendersParamsArray);
	
	if(!empty($extendersNamesArray)){
		//If we have a single extender then make sure that extender params set as an array
		//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
		if(count($extendersNamesArray) === 1){
			if(!isset($extendersParamsArray[$extendersNamesArray[0]])){
				$extendersParamsArray = array(
					$extendersNamesArray[0] => $extendersParamsArray
				);
			}
		}else{
			//Make sure that for each extender there is an item in $extendersParamsArray 
			foreach($extendersNamesArray as $extenderName){
				if(!isset($extendersParamsArray[$extenderName])){
					$extendersParamsArray[$extenderName] = array();
				}
			}
		}
	}
	
	parse_str($outputFormatParams, $outputFormatParamsArray);
	
	//Make sure orderBy looks like SQL
	$orderBy = str_replace($fieldDelimiter, '`', $orderBy);
	
	$input = new \ddGetDocuments\Input(
		//Snippet params
		array(
			'offset' => $offset,
			'total' => $total,
			'orderBy' => $orderBy,
			'filter' => $filter,
			'fieldDelimiter' => $fieldDelimiter
		),
		$providerParamsArray,
		$extendersParamsArray,
		$outputFormatParamsArray
	);
	
	//Extenders storage
	$extendersStorage = array();
	
	//Iterate through all extenders to create their instances
	foreach($extendersNamesArray as $extenderName){
		$extenderClass = \ddGetDocuments\Extender\Extender::includeExtenderByName($extenderName);
		//Passing parameters to extender's constructor
		$extender = new $extenderClass($extendersParamsArray[$extenderName]);
		//Passing a link to the storage
		$extendersStorage[$extenderName] = $extender;
		
		//Overwrite the snippet parameters with the result of applying them to the current extender
		$input->snippetParams = $extender->applyToSnippetParams($input->snippetParams);
	}
	
	$providerResult = $dataProvider->get($input);
	$data = new \ddGetDocuments\Output($providerResult);
	
	//Iterate through all extenders again to apply them to the output
	foreach($extendersStorage as $extenderName => $extender){
		$data->extenders[$extenderName] = $extender->applyToOutput($providerResult);
	}
	
	switch($outputFormat){
		default:
			$parserClass = \ddGetDocuments\OutputFormat\OutputFormat::includeOutputFormatByName($outputFormat);
			$parser = new $parserClass;
			
			$output = $parser->parse($data, $outputFormatParamsArray);
		break;
		
		case 'raw':
			$output = $data;
		break;
	}
}

return $output;
?>
