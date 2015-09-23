<?php
/**
 * Created by PhpStorm.
 * User: phm
 * Date: 14-Sep-15
 * Time: 17:22
 */

namespace ddGetDocuments\Extender;


use ddGetDocuments\DataProvider\DataProvider;
use ddGetDocuments\Output;

abstract class Extender
{
	/**
	 * @param $extenderName
	 * @return string
	 * @throws \Exception
	 */
	public final static function includeExtenderByName($extenderName){
		$extenderName = ucfirst(strtolower($extenderName));
		$extenderPath = $extenderName.DIRECTORY_SEPARATOR.'Extender'.".php";
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$extenderPath)){
			require_once($extenderPath);
			return __NAMESPACE__.'\\'.$extenderName.'\\'.'Extender';
		}else{
			throw new \Exception("Extender $extenderName not found.", 500);
		}
	}
	
	/**
	 * @param array $data
	 * @return Output
	 */
	abstract public function apply(DataProvider $dataProvider, array $providerParams, array $extenderParams, array $snippetParams);
}