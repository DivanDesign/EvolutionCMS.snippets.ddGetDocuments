<?php
namespace ddGetDocuments\Outputter;


use ddGetDocuments\Output;

abstract class Outputter extends \DDTools\BaseClass {
	protected
		/**
		 * @property $docFields {array} — Document fields including TVs used in the output.
		 * @property $docFields[i] {string} — Field name.
		 */
		$docFields = ['id'],
		
		/**
		 * @property $templates {stdClass}
		 * @property $templates->{$templateName} {string}
		 */
		$templates = []
	;
	
	/**
	 * includeOutputterByName
	 * @version 1.0.3 (2019-03-11)
	 * 
	 * @TODO: Remove it, use `\DDTools\BaseClass::createChildInstance` instead
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
	 * @version 1.5 (2021-07-13)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->dataProvider {\ddGetDocuments\DataProvider\DataProvider}
	 */
	function __construct($params = []){
		$params = (object) $params;
		
		
		//# Prepare templates
		$this->templates = (object) $this->templates;
		
		//If parameter is passed
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $params,
				'propName' => 'templates'
			])
		){
			//Estend defaults
			$this->templates = \DDTools\ObjectTools::extend([
				'objects' => [
					$this->templates,
					$params->templates
				]
			]);
			
			//Remove from params to prevent overwriting through `$this->setExistingProps`
			unset($params->templates);
		}
		
		foreach (
			$this->templates as
			$templateName =>
			$templateValue
		){
			//Exclude null values
			if (is_string($templateValue)){
				$this->templates->{$templateName} = \ddTools::$modx->getTpl($templateValue);
			}
		}
		
		
		//Все параметры задают свойства объекта
		$this->setExistingProps($params);
		
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
		}elseif (isset($params->dataProvider)){
			//Ask dataProvider to get them
			$params->dataProvider->addResourcesFieldsToGet($this->docFields);
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