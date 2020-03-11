<?php
namespace ddGetDocuments;


class Input extends \DDTools\BaseClass {
	/**
	 * @property $snippetParams {stdClass}
	 * @property $extendersParams {stdClass}
	 * @property $providerParams {stdClass}
	 * @property $outputterParams {stdClass}
	 */
	public
		$snippetParams,
		$extendersParams,
		$providerParams,
		$outputterParams
	;
	
	/**
	 * __construct
	 * @version 3.0 (2020-03-11)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The object of params. @required
	 * @param $params->snippetParams {stdClass|arrayAssociative} — @required
	 * @param $params->providerParams {stdClass|arrayAssociative} — @required
	 * @param $params->extendersParams {stdClass|arrayAssociative} — @required
	 * @param $params->outputterParams {stdClass|arrayAssociative} — @required
	 */
	public function __construct($params){
		//Set object properties from parameters
		$this->setExistingProps($params);
		
		//All property types must be stdClass
		foreach (
			$this as
			$propertyName =>
			$propertyValue
		){
			$this->{$propertyName} = (object) $propertyValue;
		}
	}
}