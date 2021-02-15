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

$input = new \ddGetDocuments\Input($params);

$dataProviderClass = \ddGetDocuments\DataProvider\DataProvider::includeProviderByName($input->provider);

if(class_exists($dataProviderClass)){
	//Extenders storage
	$extendersStorage = [];
	
	//Iterate through all extenders to create their instances
	foreach(
		$input->extendersParams as
		$extenderName =>
		$extenderParams
	){
		$extenderObject = \ddGetDocuments\Extender\Extender::createChildInstance([
			'name' => $extenderName,
			'parentDir' =>
				$snippetPath_src .
				'Extender'
			,
			//Passing parameters into constructor
			'params' => $extenderParams
		]);
		//Passing a link to the storage
		$extendersStorage[$extenderName] = $extenderObject;
		
		//Overwrite the data provider parameters with the result of applying them to the current extender
		$input->providerParams = $extenderObject->applyToDataProviderParams($input->providerParams);
	}
	
	$dataProviderObject = new $dataProviderClass($input);
	
	if ($input->outputter != 'raw'){
		$input->outputterParams->dataProvider = $dataProviderObject;
		
		$outputterObject = \ddGetDocuments\Outputter\Outputter::createChildInstance([
			'name' => $input->outputter,
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
	
	if ($input->outputter == 'raw'){
		$snippetResult = $outputData;
	}else{
		$snippetResult = $outputterObject->parse($outputData);
	}
}

return $snippetResult;
?>