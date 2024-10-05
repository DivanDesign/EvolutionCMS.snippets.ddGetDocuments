<?php
namespace ddGetDocuments;


class Input extends \DDTools\Base\Base {
	public
		/**
		 * @property $fieldDelimiter {string}
		 */
		$fieldDelimiter = '',
		
		/**
		 * @property $provider {string}
		 * @property $providerParams {stdClass}
		 * @property $providerParams->{$paramName} {mixed}
		 */
		$provider = 'parent',
		$providerParams = [
			'filter' => '',
			'offset' => 0,
			'total' => NULL,
			'orderBy' => '',
		],
		
		/**
		 * @property $outputter {string}
		 * @property $outputterParams {stdClass}
		 * @property $outputterParams->{$paramName} {mixed}
		 */
		$outputter = 'string',
		$outputterParams = [],
		
		/**
		 * @property $extendersParams {stdClass}
		 * @property $extendersParams->{$extenderName} {stdClass}
		 * @property $extendersParams->{$extenderName}->{$paramName} {mixed}
		 */
		$extendersParams = []
	;
	
	/**
	 * __construct
	 * @version 4.5.2 (2024-10-05)
	 * 
	 * @param $snippetParams {stdClass} — The object of parameters. @required
	 * @param $snippetParams->providerParams {stdClass|arrayAssociative|stringJsonObject}
	 * @param $snippetParams->extendersParams {stdClass|arrayAssociative|stringJsonObject}
	 * @param $snippetParams->outputterParams {stdClass|arrayAssociative|stringJsonObject}
	 */
	public function __construct($snippetParams){
		// Prepare provider, outputter and extender params
		foreach (
			[
				'providerParams',
				'outputterParams',
				'extendersParams',
			]
			as $paramName
		){
			// Convert to object
			$this->{$paramName} = (object) $this->{$paramName};
			
			if (
				\DDTools\ObjectTools::isPropExists([
					'object' => $snippetParams,
					'propName' => $paramName,
				])
			){
				$this->{$paramName} = \DDTools\ObjectTools::extend([
					'objects' => [
						// Defaults
						$this->{$paramName},
						// Given parameters 
						\DDTools\ObjectTools::convertType([
							'object' => $snippetParams->{$paramName},
							'type' => 'objectStdClass',
						]),
					],
				]);
			}
			
			// Remove it to prevent overwriting through `$this->setExistingProps`
			unset($snippetParams->{$paramName});
		}
		
		
		$this->outputter = strtolower($this->outputter);
		
		
		// Backward compatibility
		$this->backwardCompatibility_dataProviderParams($snippetParams);
		$this->backwardCompatibility_outputterParams();
		
		
		$this->prepareExtendersParams($snippetParams);
		
		
		// Set object properties from snippet parameters
		$this->setExistingProps($snippetParams);
		
		
		// Make sure groupBy, orderBy and filter looks like SQL
		foreach (
			[
				'filter',
				'groupBy',
				'orderBy',
			]
			as $paramName
		){
			$this->providerParams->{$paramName} = str_replace(
				$this->fieldDelimiter,
				'`',
				$this->providerParams->{$paramName}
			);
		}
	}
	
	/**
	 * prepareExtendersParams
	 * @version 3.0.2 (2024-10-05)
	 * 
	 * @desc Prepare extenders params.
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {void}
	 */
	private function prepareExtendersParams($snippetParams){
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $snippetParams,
				'propName' => 'extenders',
			])
		){
			// Prepare extenders
			if (is_string($snippetParams->extenders)){
				if (!empty($snippetParams->extenders)){
					$snippetParams->extenders = explode(
						',',
						trim($snippetParams->extenders)
					);
				}else{
					$snippetParams->extenders = [];
				}
			}
			
			// Prepare extenders params
			if(!empty($snippetParams->extenders)){
				// If we have a single extender then make sure that extender params set as an array
				// like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
				if(count($snippetParams->extenders) === 1){
					if(
						!\DDTools\ObjectTools::isPropExists([
							'object' => $this->extendersParams,
							'propName' => $snippetParams->extenders[0],
						])
					){
						$this->extendersParams = (object) [
							$snippetParams->extenders[0] => $this->extendersParams,
						];
					}
				}else{
					// Make sure that for each extender there is an item in $this->extendersParams
					foreach(
						$snippetParams->extenders
						as $extenderName
					){
						if(
							!\DDTools\ObjectTools::isPropExists([
								'object' => $this->extendersParams,
								'propName' => $extenderName,
							])
						){
							$this->extendersParams->{$extenderName} = (object) [];
						}
					}
				}
			}
		}
	}
	
	/**
	 * backwardCompatibility_dataProviderParams
	 * @version 2.0.2 (2024-10-05)
	 * 
	 * @desc Prepare data provider params preserve backward compatibility.
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {void}
	 */
	private function backwardCompatibility_dataProviderParams($snippetParams){
		// Move parameters from snippetParams to providerParams
		foreach (
			[
				'filter',
				'offset',
				'total',
				'orderBy',
			]
			as $paramName
		){
			if (
				\DDTools\ObjectTools::isPropExists([
					'object' => $snippetParams,
					'propName' => $paramName,
				])
			){
				$this->providerParams->{$paramName} = $snippetParams->{$paramName};
			}
		}
	}
	
	/**
	 * backwardCompatibility_outputterParams
	 * @version 1.1.1 (2024-10-05)
	 * 
	 * @desc Prepare data provider params preserve backward compatibility.
	 * 
	 * @return {void}
	 */
	private function backwardCompatibility_outputterParams(){
		switch ($this->outputter){
			case 'string':
				$this->backwardCompatibility_outputterParams_moveTemplates([
					'item' => 'itemTpl',
					'itemFirst' => 'itemTplFirst',
					'itemLast' => 'itemTplLast',
					'wrapper' => 'wrapperTpl',
					'noResults' => 'noResults',
				]);
			break;
			
			case 'sitemap':
				$this->backwardCompatibility_outputterParams_moveTemplates([
					'item' => 'itemTpl',
					'wrapper' => 'wrapperTpl',
				]);
			break;
			
			case 'yandexmarket':
				$this->backwardCompatibility_outputterParams_yandexmarket();
			break;
		}
	}
	
	/**
	 * backwardCompatibility_outputterParams_moveTemplates
	 * @version 1.0.2 (2024-10-05)
	 * 
	 * @desc Moves required templates from $this->outputterParams to $this->outputterParams->templates.
	 * 
	 * @param $complianceArray {arrayAssociative} — Compliance between new and old names. @required
	 * @param $complianceArray[$newName] {string} — Key is a new name, value is an old name. @required
	 * 
	 * @return {void}
	 */
	private function backwardCompatibility_outputterParams_moveTemplates($complianceArray){
		if (
			// If required templates is not set, then we need to provide backward compatibility
			!\DDTools\ObjectTools::isPropExists([
				'object' => $this->outputterParams,
				'propName' => 'templates',
			])
		){
			$this->outputterParams->templates = (object) [];
			
			foreach(
				$complianceArray
				as $newName
				=> $oldName
			){
				if (
					\DDTools\ObjectTools::isPropExists([
						'object' => $this->outputterParams,
						'propName' => $oldName,
					])
				){
					$this->outputterParams->templates->{$newName} = $this->outputterParams->{$oldName};
					
					unset($this->outputterParams->{$oldName});
				}
			}
		}
	}
	
	/**
	 * backwardCompatibility_outputterParams_yandexmarket
	 * @version 1.0.2 (2024-10-05)
	 * 
	 * @return {void}
	 */
	private function backwardCompatibility_outputterParams_yandexmarket(){
		if (
			// If required shopData is not set, then we need to provide backward compatibility
			!\DDTools\ObjectTools::isPropExists([
				'object' => $this->outputterParams,
				'propName' => 'shopData'
			])
		){
			// If shopData is not set, then offerFields and templates are not set too
			$this->outputterParams->shopData = (object) [];
			$this->outputterParams->offerFields = (object) [];
			$this->outputterParams->templates = (object) [];
			
			foreach (
				$this->outputterParams
				as $paramName
				=> $paramValue
			){
				$targetGroupName = null;
				$targetParamName = null;
				
				// $this->outputterParams->shopData
				if (
					substr(
						$paramName,
						0,
						9
					)
					== 'shopData_'
				){
					$targetGroupName = 'shopData';
					
					$targetParamName = substr(
						$paramName,
						9
					);
				// $this->outputterParams->offerFields
				}elseif (
					substr(
						$paramName,
						0,
						12
					)
					== 'offerFields_'
				){
					$targetGroupName = 'offerFields';
					
					$targetParamName = substr(
						$paramName,
						12
					);
				// $this->outputterParams->templates
				}elseif (
					substr(
						$paramName,
						0,
						10
					)
					== 'templates_'
				){
					$targetGroupName = 'offerFields';
					
					$targetParamName = substr(
						$paramName,
						10
					);
				}
				
				if (!is_null($targetGroupName)){
					$this->outputterParams->{$targetGroupName}->{$targetParamName} = $paramValue;
					
					unset($this->outputterParams->{$paramName});
				}
			}
		}
	}
}