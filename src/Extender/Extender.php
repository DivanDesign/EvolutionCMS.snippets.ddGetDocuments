<?php
/**
 * Created by phm
 */

namespace ddGetDocuments\Extender;


abstract class Extender extends \DDTools\BaseClass {
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
	 * applyToSnippetParams
	 * @version 2.0 (2020-03-11)
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	abstract public function applyToSnippetParams($snippetParams);
	
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