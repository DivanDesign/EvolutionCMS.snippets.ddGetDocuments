<?php
/**
 * ddGetDocuments
 * @version 1.2 (2020-10-09)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddgetdocuments
 * 
 * @copyright 2015–2020 DD Group {@link https://DivanDesign.biz }
 */

//The snippet must return an empty string even if result is absent
$snippetResult = '';

global $modx;

$snippetPath =
	$modx->getConfig('base_path') .
	'assets/snippets/ddGetDocuments/'
;
$snippetPath_src =
	$snippetPath .
	'src' .
	DIRECTORY_SEPARATOR
;

//Include (MODX)EvolutionCMS.libraries.ddTools
if(!class_exists('\ddTools')){
	require_once(
		$modx->getConfig('base_path') .
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
extract(\ddTools::verifyRenamedParams([
	'params' => $params,
	'compliance' => [
		'outputter' => 'outputFormat',
		'outputterParams' => 'outputFormatParams'
	]
]));

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
$dataProviderClass = \ddGetDocuments\DataProvider\DataProvider::includeProviderByName($provider);
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

if(class_exists($dataProviderClass)){
	//Prepare provider params
	$providerParams = \ddTools::encodedStringToArray($providerParams);
	
	//Backward compatibility with <= 1.1
	if (isset($orderBy)){
		$providerParams['orderBy'] = $orderBy;
	}
	
	//Prepare extender params
	$extendersParams = (object) \ddTools::encodedStringToArray($extendersParams);
	//Prepare outputter params
	$outputterParams = \ddTools::encodedStringToArray($outputterParams);
	
	if(!empty($extenders)){
		//If we have a single extender then make sure that extender params set as an array
		//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
		if(count($extenders) === 1){
			if(!isset($extendersParams->{$extenders[0]})){
				$extendersParams = (object) [
					$extenders[0] => $extendersParams
				];
			}
		}else{
			//Make sure that for each extender there is an item in $extendersParams
			foreach(
				$extenders as
				$extenderName
			){
				if(!isset($extendersParams->{$extenderName})){
					$extendersParams->{$extenderName} = [];
				}
			}
		}
	}
	
	//Make sure orderBy and filter looks like SQL
	if (!empty($providerParams['orderBy'])){
		$providerParams['orderBy'] = str_replace(
			$fieldDelimiter,
			'`',
			$providerParams['orderBy']
		);
	}
	$filter = str_replace(
		$fieldDelimiter,
		'`',
		$filter
	);
	
	$input = new \ddGetDocuments\Input([
		'snippetParams' => [
			'offset' => $offset,
			'total' => $total,
			'filter' => $filter
		],
		'providerParams' => $providerParams,
		'extendersParams' => $extendersParams,
		'outputterParams' => $outputterParams
	]);
	
	//Extenders storage
	$extendersStorage = [];
	
	//Iterate through all extenders to create their instances
	foreach(
		$extenders as
		$extenderName
	){
		$extenderObject = \ddGetDocuments\Extender\Extender::createChildInstance([
			'name' => $extenderName,
			'parentDir' =>
				$snippetPath_src .
				'Extender'
			,
			//Passing parameters into constructor
			'params' => $input->extendersParams->{$extenderName}
		]);
		//Passing a link to the storage
		$extendersStorage[$extenderName] = $extenderObject;
		
		//Overwrite the snippet parameters with the result of applying them to the current extender
		$input->snippetParams = $extenderObject->applyToSnippetParams($input->snippetParams);
		
		//Overwrite the data provider parameters with the result of applying them to the current extender
		$input->providerParams = $extenderObject->applyToDataProviderParams($input->providerParams);
	}
	
	$dataProviderObject = new $dataProviderClass($input);
	
	if ($outputter != 'raw'){
		$input->outputterParams->dataProvider = $dataProviderObject;
		
		$outputterObject = \ddGetDocuments\Outputter\Outputter::createChildInstance([
			'name' => $outputter,
			'parentDir' =>
				$snippetPath_src .
				'Outputter'
			,
			//Passing parameters into constructor
			'params' => $input->outputterParams
		]);
	}
	
	$dataProviderResult = $dataProviderObject->get();
	
	$outputData = new \ddGetDocuments\Output($dataProviderResult);
	
	//Iterate through all extenders again to apply them to the output
	foreach(
		$extendersStorage as
		$extenderName =>
		$extenderObject
	){
		$outputData->extenders[$extenderName] = $extenderObject->applyToOutput($dataProviderResult);
	}
	
	if ($outputter == 'raw'){
		$snippetResult = $outputData;
	}else{
		$snippetResult = $outputterObject->parse($outputData);
	}
}

return $snippetResult;
?>