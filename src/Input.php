<?php
namespace ddGetDocuments;


class Input extends \DDTools\BaseClass {
	public
		/**
		 * @property $snippetParams {stdClass}
		 * @property $snippetParams->offset {integer}
		 * @property $snippetParams->total {integer}
		 * @property $snippetParams->filter {string}
		 * @property $snippetParams->fieldDelimiter {string}
		 */
		$snippetParams,
		
		/**
		 * @property $provider {string}
		 * @property $providerParams {stdClass}
		 * @property $providerParams->{$paramName} {mixed}
		 */
		$provider = 'parent',
		$providerParams,
		
		/**
		 * @property $outputter {string}
		 * @property $outputterParams {stdClass}
		 * @property $outputterParams->{$paramName} {mixed}
		 */
		$outputter = 'string',
		$outputterParams,
		
		/**
		 * @property $extendersParams {stdClass}
		 * @property $extendersParams->{$extenderName} {stdClass}
		 * @property $extendersParams->{$extenderName}->{$paramName} {mixed}
		 */
		$extendersParams
	;
	
	/**
	 * __construct
	 * @version 4.0 (2021-02-12)
	 * 
	 * @param $snippetParams {stdClass} — The object of parameters. @required
	 * @param $snippetParams->providerParams {stdClass|arrayAssociative|stringJsonObject} — @required
	 * @param $snippetParams->extendersParams {stdClass|arrayAssociative|stringJsonObject} — @required
	 * @param $snippetParams->outputterParams {stdClass|arrayAssociative|stringJsonObject} — @required
	 */
	public function __construct($snippetParams){
		//Save snippet parameters and prapare them later
		$this->snippetParams = $snippetParams;
		
		
		//Prepare provider, outputter and extender params
		foreach (
			[
				'providerParams',
				'outputterParams',
				'extendersParams'
			] as
			$paramName
		){
			$this->{$paramName} = \DDTools\ObjectTools::convertType([
				'object' => $this->snippetParams->{$paramName},
				'type' => 'objectStdClass'
			]);
			
			//No needed in snippet params
			unset($this->snippetParams->{$paramName});
		}
		
		
		//Backward compatibility
		$this->paramsBackwardCompatibility();
		
		
		//Set object properties from snippet parameters
		$this->setExistingProps($this->snippetParams);
		
		
		$this->prepareExtendersParams();
		
		
		//TODO: Is it needed?
		$this->outputter = strtolower($this->outputter);
		
		
		//Make sure orderBy and filter looks like SQL
		if (!empty($this->providerParams->orderBy)){
			$this->providerParams->orderBy = str_replace(
				$this->snippetParams->fieldDelimiter,
				'`',
				$this->providerParams->orderBy
			);
		}
		$this->snippetParams->filter = str_replace(
			$this->snippetParams->fieldDelimiter,
			'`',
			$this->snippetParams->filter
		);
	}
	
	/**
	 * prepareExtendersParams
	 * @version 1.0 (2021-02-12)
	 * 
	 * @desc Prepare extenders params.
	 * 
	 * @return {void}
	 */
	private function prepareExtendersParams(){
		//Prepare extenders
		if (is_string($this->snippetParams->extenders)){
			if (!empty($this->snippetParams->extenders)){
				$this->snippetParams->extenders = explode(
					',',
					trim($this->snippetParams->extenders)
				);
			}else{
				$this->snippetParams->extenders = [];
			}
		}
		
		//Prepare extenders params
		if(!empty($this->snippetParams->extenders)){
			//If we have a single extender then make sure that extender params set as an array
			//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
			if(count($this->snippetParams->extenders) === 1){
				if(
					!\DDTools\ObjectTools::isPropExists([
						'object' => $this->extendersParams,
						'propName' => $this->snippetParams->extenders[0]
					])
				){
					$this->extendersParams = (object) [
						$this->snippetParams->extenders[0] => $this->extendersParams
					];
				}
			}else{
				//Make sure that for each extender there is an item in $this->extendersParams
				foreach(
					$this->snippetParams->extenders as
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
		unset($this->snippetParams->extenders);
	}
	
	/**
	 * paramsBackwardCompatibility
	 * @version 1.0 (2021-02-12)
	 * 
	 * @desc Prepare snippet params preserve backward compatibility.
	 * 
	 * @return {void}
	 */
	private function paramsBackwardCompatibility(){
		//Backward compatibility with <= 1.1
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $this->snippetParams,
				'propName' => 'orderBy'
			])
		){
			$this->providerParams->orderBy = $this->snippetParams->orderBy;
			
			unset($this->snippetParams->orderBy);
		}
	}
}