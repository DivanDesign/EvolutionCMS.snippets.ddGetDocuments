<?php
/**
 * ddGetDocuments
 * @version 0.5 (2016-05-26)
 * 
 * A snippet for fetching and parsing resources from the document tree by a custom rule.
 * 
 * @param string $provider - Name of the provider that will be used to fetch documents.
 * @param string $providerParams - Parameters to be passed to the provider. The parameter must be set as a query string,
 * e.g. $providerParams = 'parentIds=1&depth=2'.
 * @param string $fieldDelimiter - The field delimiter to be used in order to distinct data base column names in those
 * parameters which can contain SQL queries directly, e. g. $orderBy and $filter. Default: '`'.
 * 
 * @param integer $total - The maximum number of the resources that will be returned.
 * @param string $filter - The filter condition in SQL-style to be applied while resource fetching. Default: '`published` = 1 AND `deleted` = 0';
 * Notice that all fields/tvs names specified in the filter parameter must be wrapped in $fieldDelimiter.
 * @param integer $offset - Resources offset.
 * @param string $orderBy - A string representing the sorting rule. Default: '`id` ASC'.
 * 
 * @param 'string'|'raw'|'json'  $outputFormat - Format of the output. Default: 'string'.
 * @param string $outputFormatParams - Parameters to be passed to the specified formatter. The parameter must be set as a query string,
 * e.g. $outputFormatParams = 'itemTpl=chunk_1&wrapperTpl=chunk_2&noResults=No items found'.
 * @param json $outputFormatParams - Parameters to be passed to the specified formatter. The parameter must be set as a query string,
 * e.g. $outputFormatParams = 'TVtoJson=pagetitle,introtext&wrapResult=[||]'.
 * TVtoJson - TV которые попадут в json массив. @Default: 'id'
 * wrapResult - Оборачивает результат в качестве разделителя используется «||». @Default: ''
 * 
 * @param string $extenders - Comma-separated string determining which extenders should be applied to the snippet.
 * Be aware that the order of extender names can affect the output.
 * @param string $extendersParams - Parameters to be passed to their corresponding extensions. The parameter must be set as a query string,
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
$outputFormat = isset($outputFormat) ? $outputFormat : 'string';
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
		case 'JSON':
			$parserClass = \ddGetDocuments\OutputFormat\OutputFormat::includeOutputFormatByName($outputFormat);
			$parser = new $parserClass;
			
			$output = $parser->parse($data, $outputFormatParamsArray);
		break;
	}
}

return $output;
?>
