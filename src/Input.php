<?php
namespace ddGetDocuments;


class Input extends \DDTools\BaseClass {
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
			'orderBy' => ''
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
	 * @version 4.1.1 (2021-02-14)
	 * 
	 * @param $snippetParams {stdClass} — The object of parameters. @required
	 * @param $snippetParams->providerParams {stdClass|arrayAssociative|stringJsonObject} — @required
	 * @param $snippetParams->extendersParams {stdClass|arrayAssociative|stringJsonObject} — @required
	 * @param $snippetParams->outputterParams {stdClass|arrayAssociative|stringJsonObject} — @required
	 */
	public function __construct($snippetParams){
		//Prepare provider, outputter and extender params
		foreach (
			[
				'providerParams',
				'outputterParams',
				'extendersParams'
			] as
			$paramName
		){
			$this->{$paramName} = \DDTools\ObjectTools::extend([
				'objects' => [
					//Defaults
					(object) $this->{$paramName},
					//Given parameters 
					\DDTools\ObjectTools::convertType([
						'object' => $snippetParams->{$paramName},
						'type' => 'objectStdClass'
					])
				]
			]);
			
			//No needed in snippet params
			unset($snippetParams->{$paramName});
		}
		
		
		//Backward compatibility
		$snippetParams = $this->paramsBackwardCompatibility($snippetParams);
		
		
		//Set object properties from snippet parameters
		$this->setExistingProps($snippetParams);
		
		
		$snippetParams = $this->prepareExtendersParams($snippetParams);
		
		
		//TODO: Is it needed?
		$this->outputter = strtolower($this->outputter);
		
		
		//Make sure orderBy and filter looks like SQL
		if (!empty($this->providerParams->orderBy)){
			$this->providerParams->orderBy = str_replace(
				$this->fieldDelimiter,
				'`',
				$this->providerParams->orderBy
			);
		}
		$this->providerParams->filter = str_replace(
			$this->fieldDelimiter,
			'`',
			$this->providerParams->filter
		);
	}
	
	/**
	 * prepareExtendersParams
	 * @version 2.0 (2021-02-15)
	 * 
	 * @desc Prepare extenders params.
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	private function prepareExtendersParams($snippetParams){
		//Prepare extenders
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
		
		//Prepare extenders params
		if(!empty($snippetParams->extenders)){
			//If we have a single extender then make sure that extender params set as an array
			//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
			if(count($snippetParams->extenders) === 1){
				if(
					!\DDTools\ObjectTools::isPropExists([
						'object' => $this->extendersParams,
						'propName' => $snippetParams->extenders[0]
					])
				){
					$this->extendersParams = (object) [
						$snippetParams->extenders[0] => $this->extendersParams
					];
				}
			}else{
				//Make sure that for each extender there is an item in $this->extendersParams
				foreach(
					$snippetParams->extenders as
					$extenderName
				){
					if(
						!\DDTools\ObjectTools::isPropExists([
							'object' => $this->extendersParams,
							'propName' => $extenderName
						])
					){
						$this->extendersParams->{$extenderName} = (object) [];
					}
				}
			}
		}
		
		//No needed anymore, all data was saved to $this->extendersParams
		unset($snippetParams->extenders);
		
		return $snippetParams;
	}
	
	/**
	 * paramsBackwardCompatibility
	 * @version 2.0 (2021-02-15)
	 * 
	 * @desc Prepare snippet params preserve backward compatibility.
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	private function paramsBackwardCompatibility($snippetParams){
		//Move parameters from snippetParams to providerParams
		foreach (
			[
				'filter',
				'offset',
				'total',
				'orderBy'
			] as
			$paramName
		){
			if (
				\DDTools\ObjectTools::isPropExists([
					'object' => $snippetParams,
					'propName' => $paramName
				])
			){
				$this->providerParams->{$paramName} = $snippetParams->{$paramName};
				
				unset($snippetParams->{$paramName});
			}
		}
		
		return $snippetParams;
	}
}