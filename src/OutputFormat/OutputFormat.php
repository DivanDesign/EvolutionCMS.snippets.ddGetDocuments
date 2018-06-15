<?php
namespace ddGetDocuments\OutputFormat;


use ddGetDocuments\Output;

abstract class OutputFormat
{
	/**
	 * includeOutputFormatByName
	 * @version 1.0.1 (2018-01-31)
	 * 
	 * @param $parserName {string}
	 * 
	 * @throws \Exception
	 * 
	 * @return {string}
	 */
	public final static function includeOutputFormatByName($parserName){
		$parserName = ucfirst(strtolower($parserName));
		$parserPath = $parserName.DIRECTORY_SEPARATOR.'OutputFormat'.'.php';
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$parserPath)){
			require_once($parserPath);
			
			return __NAMESPACE__.'\\'.$parserName.'\\'.'OutputFormat';
		}else{
			throw new \Exception(
				'Parser “'.$parserName.'” not found.',
				500
			);
		}
	}
	
	/**
	 * __construct
	 * @version 1.0 (2018-06-12)
	 * 
	 * @param $params {array}
	 */
	function __construct(array $params = []){
		//Все параметры задают свойства объекта
		foreach ($params as $paramName => $paramValue){
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
	 * parse
	 * @version 2.0 (2018-06-13)
	 * 
	 * @param $dataArray {\ddGetDocuments\Output}
	 */
	abstract function parse(Output $dataArray);
}