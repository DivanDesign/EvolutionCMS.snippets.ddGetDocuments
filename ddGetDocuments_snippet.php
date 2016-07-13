<?php
/**
 * ddGetDocuments
 * @version 0.5 (2016-05-26)
 * 
 * A snippet for fetching and parsing resources from the document tree by a custom rule.
 * 
 * @param 'parent'|'select' $provider - Name of the provider that will be used to fetch documents. Default: 'parent'.
 * @param string_query $providerParams - Parameters to be passed to the provider. The parameter must be set as a query string,
 * When $provider == 'parent' =>
 * @param array|string_commaSepareted $providerParams['parentIds'] - Parent IDs. Default: 0.
 * @param integer $providerParams['depth'] - Depth of children documents search. Default: 1.
 * e.g. $providerParams = 'parentIds=1&depth=2'.
 * @param string $fieldDelimiter - The field delimiter to be used in order to distinct data base column names in those
 * parameters which can contain SQL queries directly, e. g. $orderBy and $filter. Default: '`'.
 * 
 * @param integer $total - The maximum number of the resources that will be returned. Default: —.
 * @param string $filter - The filter condition in SQL-style to be applied while resource fetching. Default: '`published` = 1 AND `deleted` = 0';
 * Notice that all fields/tvs names specified in the filter parameter must be wrapped in $fieldDelimiter.
 * @param integer $offset - Resources offset. Default: 0.
 * @param string $orderBy - A string representing the sorting rule. Default: '`id` ASC'.
 * 
 * @param 'string'|'json'|'raw' $outputFormat - Format of the output. Default: 'string'.
 * @param string_query $outputFormatParams - Parameters to be passed to the specified formatter. The parameter must be set as a query string,
 * When $outputFormat == 'string' =>
 * @param string_chunkName $outputFormatParams['itemTpl'] - Item template. Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
 * @param string_chunkName $outputFormatParams['itemTplFirst'] - First item template. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
 * @param string_chunkName $outputFormatParams['itemTplLast'] - Last item template. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
 * @param string_chunkName $outputFormatParams['wrapperTpl'] - Wrapper template. Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
 * @param string|string_chunkName $outputFormatParams['noResults'] - A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+]. 
 * e.g. 'itemTpl=chunk_1&wrapperTpl=chunk_2&noResults=No items found'.
 * When $outputFormat == 'json' =>
 * @param array|string_commaSeparated $outputFormatParams['docFields'] - Document fields to output (including TVs). Default: 'id'.
 * e.g. 'docFields=id,pagetitle,introtext'.
 * 
 * @param string_commaSeparated $extenders - Comma-separated string determining which extenders should be applied to the snippet.
 * Be aware that the order of extender names can affect the output.
 * @param string_query $extendersParams - Parameters to be passed to their corresponding extensions. The parameter must be set as a query string,
 * When $extenders == 'pagination' =>
 * @param string_chunkName $extendersParams['wrapperTpl'] - Chunk to be used to output the pagination. @required
 * @param string_chunkName $extendersParams['pageTpl'] - Chunk to be used to output pages within the pagination. @required
 * @param string_chunkName $extendersParams['currentPageTpl'] - Chunk to be used to output the current page within the pagination. @required
 * @param string_chunkName $extendersParams['nextTpl'] - Chunk to be used to output the navigation block to the next page. @required
 * @param string_chunkName $extendersParams['nextOffTpl'] - Chunk to be used to output the navigation block to the next page if there are no more pages after. @required
 * @param string_chunkName $extendersParams['previousTpl'] - Chunk to be used to output the navigation block to the previous page. @required
 * @param string_chunkName $extendersParams['previousOffTpl'] - Chunk to be used to output the navigation block to the previous page if there are no more pages before. @required
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
