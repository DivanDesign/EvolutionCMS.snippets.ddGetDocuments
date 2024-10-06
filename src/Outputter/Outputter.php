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
		 * @property $fieldAliases {stdClass} — Aliases of fields if used.
		 * @property $fieldAliases->{$fieldName} {string} — A key is an original field name, a value is an alias.
		 */
		$fieldAliases = [],
		
		/**
		 * @property $templates {stdClass}
		 * @property $templates->{$templateName} {string}
		 */
		$templates = []
	;
	
	/**
	 * __construct
	 * @version 1.6 (2024-10-05)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->dataProvider {\ddGetDocuments\DataProvider\DataProvider}
	 */
	public function __construct($params = []){
		$params = (object) $params;
		
		// Prepare templates
		$this->construct_prepareFields_templates($params);
		// Remove from params to prevent overwriting through `$this->setExistingProps`
		unset($params->templates);
		
		// Все параметры задают свойства объекта
		$this->setExistingProps($params);
		
		$this->fieldAliases = (object) $this->fieldAliases;
		
		// Comma separated strings
		if (!is_array($this->docFields)){
			$this->docFields = explode(
				',',
				$this->docFields
			);
		}
		
		if (empty($this->docFields)){
			// We need something
			$this->docFields = ['id'];
		}else{
			// Prepare field aliases
			foreach (
				$this->docFields
				as $fieldNameIndex
				=> $fieldName
			){
				// If alias is used
				if (
					strpos(
						$fieldName,
						'='
					)
					!== false
				){
					// E. g. 'pagetitle=title'
					$fieldName = explode(
						'=',
						$fieldName
					);
					
					// Remove alias from field name
					$this->docFields[$fieldNameIndex] = $fieldName[0];
					
					// Save alias
					$this->fieldAliases->{$this->docFields[$fieldNameIndex]} = $fieldName[1];
				}
			}
			
			if (isset($params->dataProvider)){
				// Ask dataProvider to get them
				$params->dataProvider->addResourcesFieldsToGet($this->docFields);
			}
		}
	}
	
	/**
	 * construct_prepareFields_templates
	 * @version 1.0.3 (2024-10-05)
	 * 
	 * @param $params {stdClass|arrayAssociative} — See __construct.
	 */
	protected function construct_prepareFields_templates($params){
		$this->templates = (object) $this->templates;
		
		// If parameter is passed
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $params,
				'propName' => 'templates',
			])
		){
			// Extend defaults
			$this->templates = \DDTools\ObjectTools::extend([
				'objects' => [
					$this->templates,
					$params->templates,
				],
			]);
		}
		
		foreach (
			$this->templates
			as $templateName
			=> $templateValue
		){
			// Exclude null values
			if (is_string($templateValue)){
				$this->templates->{$templateName} = \ddTools::getTpl($templateValue);
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