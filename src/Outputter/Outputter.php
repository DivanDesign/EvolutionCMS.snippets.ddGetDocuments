<?php
namespace ddGetDocuments\Outputter;

use ddGetDocuments\Output;

abstract class Outputter extends \DDTools\Base\Base {
	use \DDTools\Base\AncestorTrait;
	
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
	 * __construct
	 * @version 1.5.2 (2023-05-02)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->dataProvider {\ddGetDocuments\DataProvider\DataProvider}
	 */
	public function __construct($params = []){
		$params = (object) $params;
		
		//Prepare templates
		$this->construct_prepareFields_templates($params);
		//Remove from params to prevent overwriting through `$this->setExistingProps`
		unset($params->templates);
		
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
	 * construct_prepareFields_templates
	 * @version 1.0 (2021-07-13)
	 * 
	 * @param $params {stdClass|arrayAssociative} — See __construct.
	 */
	protected function construct_prepareFields_templates($params){
		$this->templates = (object) $this->templates;
		
		//If parameter is passed
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $params,
				'propName' => 'templates'
			])
		){
			//Extend defaults
			$this->templates = \DDTools\ObjectTools::extend([
				'objects' => [
					$this->templates,
					$params->templates
				]
			]);
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
	}
	
	/**
	 * parse
	 * @version 2.0 (2018-06-13)
	 * 
	 * @param $dataArray {\ddGetDocuments\Output}
	 */
	abstract function parse(Output $dataArray);
}