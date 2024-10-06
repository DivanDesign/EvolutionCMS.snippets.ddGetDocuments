<?php
namespace ddGetDocuments;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '1.8.0',
		
		$renamedParamsCompliance = [
			'outputter' => 'outputFormat',
			'outputterParams' => 'outputFormatParams',
		]
	;
	
	/**
	 * run
	 * @version 1.0.3 (2024-10-05)
	 */
	public function run(){
		$result = '';
		
		$input = new \ddGetDocuments\Input($this->params);
		
		// Extenders storage
		$extendersStorage = [];
		
		// Iterate through all extenders to create their instances
		foreach(
			$input->extendersParams
			as $extenderName
			=> $extenderParams
		){
			$extenderObject = \ddGetDocuments\Extender\Extender::createChildInstance([
				'name' => $extenderName,
				'params' => $extenderParams,
			]);
			// Passing a link to the storage
			$extendersStorage[$extenderName] = $extenderObject;
			
			// Overwrite the data provider parameters with the result of applying them to the current extender
			$input->providerParams = $extenderObject->applyToDataProviderParams($input->providerParams);
		}
		
		$dataProviderObject = \ddGetDocuments\DataProvider\DataProvider::createChildInstance([
			'name' => $input->provider,
			'params' => $input->providerParams,
		]);
		
		if ($input->outputter != 'raw'){
			$input->outputterParams->dataProvider = $dataProviderObject;
			
			$outputterObject = \ddGetDocuments\Outputter\Outputter::createChildInstance([
				'name' => $input->outputter,
				'params' => $input->outputterParams,
			]);
		}
		
		$dataProviderResult = $dataProviderObject->get();
		
		$outputData = new \ddGetDocuments\Output($dataProviderResult);
		
		// Iterate through all extenders again to apply them to the output
		foreach(
			$extendersStorage
			as $extenderName
			=> $extenderObject
		){
			$outputData->extenders[$extenderName] = $extenderObject->applyToOutput($dataProviderResult);
		}
		
		if ($input->outputter == 'raw'){
			$result = $outputData;
		}else{
			$result = $outputterObject->parse($outputData);
		}
		
		return $result;
	}
}