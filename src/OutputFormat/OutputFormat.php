<?php
namespace ddGetDocuments\OutputFormat;


use ddGetDocuments\Output;

abstract class OutputFormat
{
	/**
	 * @param $parserName
	 * @return string
	 * @throws \Exception
	 */
	public final static function includeOutputFormatByName($parserName){
		$parserName = ucfirst(strtolower($parserName));
		$parserPath = $parserName.DIRECTORY_SEPARATOR.'OutputFormat'.'.php';
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$parserPath)){
			require_once($parserPath);
			return __NAMESPACE__.'\\'.$parserName.'\\'.'OutputFormat';
		}else{
			throw new \Exception('Parser “'.$parserName.'” not found.', 500);
		}
	}
	
	abstract function parse(Output $dataArray, array $outputFormatParameters);
}