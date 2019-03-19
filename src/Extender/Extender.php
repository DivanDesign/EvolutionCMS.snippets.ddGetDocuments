<?php
/**
 * Created by phm
 */

namespace ddGetDocuments\Extender;

use ddGetDocuments\Input;

abstract class Extender
{
	/**
	 * includeExtenderByName
	 * @version 1.0.2 (2018-06-12)
	 * 
	 * @param $extenderName
	 * 
	 * @throws \Exception
	 * 
	 * @return {string}
	 */
	public final static function includeExtenderByName($extenderName){
		$extenderName = ucfirst(strtolower($extenderName));
		$extenderPath = $extenderName.DIRECTORY_SEPARATOR.'Extender'.'.php';
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$extenderPath)){
			require_once($extenderPath);
			
			return __NAMESPACE__.'\\'.$extenderName.'\\'.'Extender';
		}else{
			throw new \Exception(
				'Extender '.$extenderName.' not found.',
				500
			);
		}
	}
	
	/**
	 * __construct
	 * @version 1.0.1 (2018-03-19)
	 * 
	 * @param $params {array_associative}
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
	}
	
	/**
	 * applyToSnippetParams
	 * 
	 * @param array $snippetParams
	 * 
	 * @return array
	 */
	abstract public function applyToSnippetParams(array $snippetParams);
	
	/**
	 * applyToOutput
	 * @version 1.2.1 (2018-06-12)
	 * 
	 * @param $dataProviderOutput {\ddGetDocuments\DataProvider\DataProviderOutput}
	 * 
	 * @return {string|array}
	 */
	public function applyToOutput(\ddGetDocuments\DataProvider\DataProviderOutput $dataProviderOutput){
		return '';
	}
}