<?php
namespace ddGetDocuments\Outputter;


use ddGetDocuments\Output;

abstract class Outputter
{
	protected
		/**
		 * @property $docFields {array} — Document fields including TVs used in the output.
		 * @property $docFields[i] {string} — Field name.
		 */
		$docFields = ['id']
	;
	
	/**
	 * includeOutputterByName
	 * @version 1.0.3 (2019-03-11)
	 * 
	 * @param $parserName {string}
	 * 
	 * @throws \Exception
	 * 
	 * @return {string}
	 */
	public final static function includeOutputterByName($parserName){
		$parserName = ucfirst(strtolower($parserName));
		$parserPath = $parserName.DIRECTORY_SEPARATOR . 'Outputter' . '.php';
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR . $parserPath)){
			require_once($parserPath);
			
			return __NAMESPACE__ . '\\' . $parserName . '\\' . 'Outputter';
		}else{
			throw new \Exception(
				'Outputter “' . $parserName . '” not found.',
				500
			);
		}
	}
	
	/**
	 * __construct
	 * @version 1.2.2 (2019-03-13)
	 * 
	 * @param $params {array}
	 * @param $params['dataProvider'] {\ddGetDocuments\DataProvider\DataProvider}
	 */
	function __construct(array $params = []){
		//Все параметры задают свойства объекта
		foreach (
			$params as
			$paramName => $paramValue
		){
			//Validation
			if (property_exists(
				$this,
				$paramName
			)){
				$this->{$paramName} = $paramValue;
			}
		}
		
		//Comma separated strings
		if (!is_array($this->docFields)){
			$this->docFields = explode(
				',',
				$this->docFields
			);
		}
		
		if (empty($this->docFields)){
			//We need something
			$this->docFields = ['id'];
		}else if (isset($params['dataProvider'])){
			//Ask dataProvider to get them
			$params['dataProvider']->addResourcesFieldsToGet($this->docFields);
		}
	}
	
	/**
	 * parse
	 * @version 2.0 (2018-06-13)
	 * 
	 * @param $dataArray {\ddGetDocuments\Output}
	 */
	abstract function parse(Output $dataArray);
}