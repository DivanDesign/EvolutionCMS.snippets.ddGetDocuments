<?php
/**
 * Created by PhpStorm.
 * User: phm
 * Date: 14-Sep-15
 * Time: 17:22
 */

namespace ddGetDocuments\Extender;

use ddGetDocuments\Input;

abstract class Extender
{
	/**
	 * includeExtenderByName
	 * @version 1.0.1 (2018-01-31)
	 * 
	 * @param $extenderName
	 * @return string
	 * @throws \Exception
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