<?php
namespace ddGetDocuments\Extender;

abstract class Extender extends \DDTools\Base\Base {
	use \DDTools\Base\AncestorTrait;
	
	/**
	 * __construct
	 * @version 1.0.2 (2020-03-10)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 */
	function __construct($params = []){
		$this->setExistingProps($params);
	}
	
	/**
	 * applyToDataProviderParams
	 * @version 1.0 (2020-10-01)
	 * 
	 * @param $dataProviderParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	public function applyToDataProviderParams($dataProviderParams){
		return $dataProviderParams;
	}
	
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