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
$params = \ddTools::verifyRenamedParams([
	'params' => $params,
	'compliance' => [
		'outputter' => 'outputFormat',
		'outputterParams' => 'outputFormatParams'
	],
	'returnCorrectedOnly' => false
]);

//Defaults
$params = \DDTools\ObjectTools::extend([
	'objects' => [
		(object) [
			//General
			'total' => NULL,
			'offset' => 0,
			'filter' => NULL,
			'fieldDelimiter' => '',
			
			//Data provider
			'provider' => 'parent',
			'providerParams' => '',
			
			//Outputter
			'outputter' => 'string',
			'outputterParams' => '',
			
			//Extenders
			'extenders' => [],
			'extendersParams' => ''
		],
		$params
	]
]);

$dataProviderClass = \ddGetDocuments\DataProvider\DataProvider::includeProviderByName($params->provider);

//TODO: Is it needed?
$params->outputter = strtolower($params->outputter);

if (is_string($params->extenders)){
	if (!empty($params->extenders)){
		$params->extenders = explode(
			',',
			trim($params->extenders)
		);
	}else{
		$params->extenders = [];
	}
}

if(class_exists($dataProviderClass)){
	//Prepare provider params
	$params->providerParams = \DDTools\ObjectTools::convertType([
		'object' => $params->providerParams,
		'type' => 'objectStdClass'
	]);
	
	//Backward compatibility with <= 1.1
	if (isset($params->orderBy)){
		$params->providerParams->orderBy = $params->orderBy;
	}
	
	//Prepare extender params
	$params->extendersParams = \DDTools\ObjectTools::convertType([
		'object' => $params->extendersParams,
		'type' => 'objectStdClass'
	]);
	//Prepare outputter params
	$params->outputterParams = \DDTools\ObjectTools::convertType([
		'object' => $params->outputterParams,
		'type' => 'objectStdClass'
	]);
	
	if(!empty($params->extenders)){
		//If we have a single extender then make sure that extender params set as an array
		//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
		if(count($params->extenders) === 1){
			if(!isset($params->extendersParams->{$params->extenders[0]})){
				$params->extendersParams = (object) [
					$params->extenders[0] => $params->extendersParams
				];
			}
		}else{
			//Make sure that for each extender there is an item in $params->extendersParams
			foreach(
				$params->extenders as
				$extenderName
			){
				if(!isset($params->extendersParams->{$extenderName})){
					$params->extendersParams->{$extenderName} = [];
				}
			}
		}
	}
	
	//Make sure orderBy and filter looks like SQL
	if (!empty($params->providerParams->orderBy)){
		$params->providerParams->orderBy = str_replace(
			$params->fieldDelimiter,
			'`',
			$params->providerParams->orderBy
		);
	}
	$params->filter = str_replace(
		$params->fieldDelimiter,
		'`',
		$params->filter
	);
	
	$input = new \ddGetDocuments\Input([
		'snippetParams' => [
			'offset' => $params->offset,
			'total' => $params->total,
			'filter' => $params->filter
		],
		'providerParams' => $params->providerParams,
		'extendersParams' => $params->extendersParams,
		'outputterParams' => $params->outputterParams
	]);
	
	//Extenders storage
	$extendersStorage = [];
	
	//Iterate through all extenders to create their instances
	foreach(
		$params->extenders as
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
	
	if ($params->outputter != 'raw'){
		$input->outputterParams->dataProvider = $dataProviderObject;
		
		$outputterObject = \ddGetDocuments\Outputter\Outputter::createChildInstance([
			'name' => $params->outputter,
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
	
	if ($params->outputter == 'raw'){
		$snippetResult = $outputData;
	}else{
		$snippetResult = $outputterObject->parse($outputData);
	}
}

return $snippetResult;
?>