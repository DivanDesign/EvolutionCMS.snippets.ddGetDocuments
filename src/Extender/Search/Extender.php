<?php
namespace ddGetDocuments\Extender\Search;


use ddGetDocuments\DataProvider\DataProviderOutput;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		//Templates ids to search in
		$currentQuery = '',
		$docFieldsToSearch = [
			'pagetitle',
			'content'
		];
	
	/**
	 * __construct
	 * @version 1.0.1 (2018-06-12)
	 * 
	 * @param $extenderParams {array_associative}
	 */
	public function __construct(array $extenderParams){
		if(isset($extenderParams['docFieldsToSearch'])){
			$this->docFieldsToSearch = explode(
				',',
				(string) $extenderParams['docFieldsToSearch']
			);
		}
		
		if (isset($_REQUEST['query'])){
			$this->currentQuery = trim(\ddTools::$modx->db->escape($_REQUEST['query']));
		}
	}
	
	/**
	 * applyToSnippetParams
	 * @version 1.0.1 (2018-06-12)
	 * 
	 * @param $snippetParams {array_associative}
	 * 
	 * @return {array_associative}
	 */
	public function applyToSnippetParams(array $snippetParams){
		//If URL contains tags
		if (!empty($this->currentQuery)){
			if(
				isset($snippetParams['filter']) &&
				trim($snippetParams['filter']) != ''
			){
				$snippetParams['filter'] .= ' AND';
			}else{
				$snippetParams['filter'] = '';
			}
			
			$searchQueries = [];
			
			foreach ($this->docFieldsToSearch as $docField){
				$searchQueries[] = '`'.trim($docField).'` LIKE("%'.$this->currentQuery.'%")';
			}
			
			$snippetParams['filter'] .= ' ('.implode(
				' OR ',
				$searchQueries
			).')';
		}
		
		return $snippetParams;
	}
}