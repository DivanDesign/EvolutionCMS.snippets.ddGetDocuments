<?php
namespace ddGetDocuments\Extender\Search;


use ddGetDocuments\DataProvider\Output;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		//Templates ids to search in
		$currentQuery = '',
		$docFieldsToSearch = ['pagetitle', 'content'];
	
	public function __construct(array $extenderParams){
		global $modx;
		
		if(isset($extenderParams['docFieldsToSearch'])){
			$this->docFieldsToSearch = explode(',', (string) $extenderParams['docFieldsToSearch']);
		}
		
		if (isset($_REQUEST['query'])){
			$this->currentQuery = trim($modx->db->escape($_REQUEST['query']));
		}
	}
	
	/**
	 * applyToSnippetParams
	 * @version 1.0 (2017-04-09)
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
			
			$snippetParams['filter'] .= ' ('.implode(' OR ', $searchQueries).')';
		}
		
		return $snippetParams;
	}
}