<?php
namespace ddGetDocuments\Format;


use ddGetDocuments\Output;

abstract class Format
{
	/**
	 * @param $parserName
	 * @return string
	 * @throws \Exception
	 */
	public final static function includeFormatByName($parserName){
		$parserName = ucfirst(strtolower($parserName));
		$parserPath = $parserName.DIRECTORY_SEPARATOR.'Format'.".php";
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$parserPath)){
			require_once($parserPath);
			return __NAMESPACE__.'\\'.$parserName.'\\'.'Format';
		}else{
			throw new \Exception("Parser $parserName not found.", 500);
		}
	}
	
	abstract function parse(Output $dataArray, array $formatParameters);
}