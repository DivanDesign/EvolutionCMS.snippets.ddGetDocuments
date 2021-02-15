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

if(!class_exists('\ddGetDocuments\Input')){
	require_once(
		$snippetPath .
		'require.php'
	);
}

$input = new \ddGetDocuments\Input($params);

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

$dataProviderObject = \ddGetDocuments\DataProvider\DataProvider::createChildInstance([
	'name' => $input->provider,
	'parentDir' =>
		$snippetPath_src .
		'DataProvider'
	,
	//Passing parameters into constructor
	'params' => $input->providerParams
]);

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

return $snippetResult;
?>