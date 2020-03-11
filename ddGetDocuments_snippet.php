<?php
/**
 * ddGetDocuments
 * @version 0.10.1 (2018-10-31)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddgetdocuments
 * 
 * @copyright 2015–2018 DivanDesign {@link http://www.DivanDesign.biz }
 */

//The snippet must return an empty string even if result is absent
$snippetResult = '';

global $modx;

$snippetPath =
	$modx->getConfig('base_path') .
	'assets/snippets/ddGetDocuments/'
;

//Include (MODX)EvolutionCMS.libraries.ddTools
if(!class_exists('\ddTools')){
	require_once(
		$modx->config['base_path'] .
		'assets/libs/ddTools/modx.ddtools.class.php'
	);
}

if(!class_exists('\ddGetDocuments\DataProvider\DataProvider')){
	require_once(
		$snippetPath .
		'require.php'
	);
}

//Backward compatibility
extract(\ddTools::verifyRenamedParams(
	$params,
	[
		'outputter' => 'outputFormat',
		'outputterParams' => 'outputFormatParams'
	]
));

//General
$total =
	isset($total) ?
	$total :
	null
;
$offset =
	isset($offset) ?
	$offset :
	0
;
$orderBy =
	isset($orderBy) ?
	$orderBy :
	'`id` ASC'
;
$filter =
	isset($filter) ?
	$filter :
	null
;
$fieldDelimiter =
	isset($fieldDelimiter) ?
	$fieldDelimiter :
	'`'
;

//Data provider
$provider =
	isset($provider) ?
	$provider :
	'parent'
;
$providerClass = \ddGetDocuments\DataProvider\DataProvider::includeProviderByName($provider);
$providerParams =
	isset($providerParams) ?
	$providerParams :
	''
;

//Output format
$outputter =
	isset($outputter) ?
	strtolower($outputter) :
	'string'
;
$outputterParams =
	isset($outputterParams) ?
	$outputterParams :
	''
;

//Extenders
$extenders =
	(
		isset($extenders) &&
		!empty($extenders)
	) ?
	explode(
		',',
		trim($extenders)
	) :
	[]
;
$extendersParams =
	isset($extendersParams) ?
	$extendersParams :
	''
;

if(class_exists($providerClass)){
	//Prepare provider params
	$providerParams = \ddTools::encodedStringToArray($providerParams);
	//Prepare extender params
	$extendersParams = \ddTools::encodedStringToArray($extendersParams);
	//Prepare output format params
	$outputterParams = \ddTools::encodedStringToArray($outputterParams);
	
	if(!empty($extenders)){
		//If we have a single extender then make sure that extender params set as an array
		//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
		if(count($extenders) === 1){
			if(!isset($extendersParams[$extenders[0]])){
				$extendersParams = [
					$extenders[0] => $extendersParams
				];
			}
		}else{
			//Make sure that for each extender there is an item in $extendersParams 
			foreach(
				$extenders as
				$extenderName
			){
				if(!isset($extendersParams[$extenderName])){
					$extendersParams[$extenderName] = [];
				}
			}
		}
	}
	
	//Make sure orderBy and filter looks like SQL
	$orderBy = str_replace(
		$fieldDelimiter,
		'`',
		$orderBy
	);
	$filter = str_replace(
		$fieldDelimiter,
		'`',
		$filter
	);
	
	$input = new \ddGetDocuments\Input(
		//Snippet params
		[
			'offset' => $offset,
			'total' => $total,
			'orderBy' => $orderBy,
			'filter' => $filter
		],
		$providerParams,
		$extendersParams,
		$outputterParams
	);
	
	//Extenders storage
	$extendersStorage = [];
	
	//Iterate through all extenders to create their instances
	foreach(
		$extenders as
		$extenderName
	){
		$extender = \ddGetDocuments\Extender\Extender::createChildInstance([
			'name' => $extenderName,
			'parentDir' =>
				$snippetPath .
				'src' .
				DIRECTORY_SEPARATOR .
				'Extender'
			,
			//Passing parameters into constructor
			'params' => $extendersParams[$extenderName]
		]);
		//Passing a link to the storage
		$extendersStorage[$extenderName] = $extender;
		
		//Overwrite the snippet parameters with the result of applying them to the current extender
		$input->snippetParams = $extender->applyToSnippetParams($input->snippetParams);
	}
	
	$dataProvider = new $providerClass($input);
	
	if ($outputter != 'raw'){
		$outputterParams['dataProvider'] = $dataProvider;
		
		$outputterObject = \ddGetDocuments\Outputter\Outputter::createChildInstance([
			'name' => $outputter,
			'parentDir' =>
				$snippetPath .
				'src' .
				DIRECTORY_SEPARATOR .
				'Outputter'
			,
			//Passing parameters into constructor
			'params' => $outputterParams
		]);
	}
	
	$providerResult = $dataProvider->get();
	
	$data = new \ddGetDocuments\Output($providerResult);
	
	//Iterate through all extenders again to apply them to the output
	foreach(
		$extendersStorage as
		$extenderName =>
		$extender
	){
		$data->extenders[$extenderName] = $extender->applyToOutput($providerResult);
	}
	
	if ($outputter == 'raw'){
		$snippetResult = $data;
	}else{
		$snippetResult = $outputterObject->parse($data);
	}
}

return $snippetResult;
?>