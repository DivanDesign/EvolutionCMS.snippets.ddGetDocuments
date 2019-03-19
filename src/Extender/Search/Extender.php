<?php
namespace ddGetDocuments\Extender\Search;


use ddGetDocuments\DataProvider\DataProviderOutput;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		$currentQuery = ''
	;
	
	protected
		$docFieldsToSearch = [
			'pagetitle',
			'content'
		]
	;
	
	/**
	 * __construct
	 * @version 1.1 (2018-06-12)
	 * 
	 * @param $params {array_associative}
	 * @param $params['docFieldsToSearch'] {array|string_commaSepareted} — Document fields to search in. Default: ['pagetitle', 'content'].
	 */
	public function __construct(array $params){
		//Call base constructor
		parent::__construct($params);
		
		if(!is_array($this->docFieldsToSearch)){
			$this->docFieldsToSearch = explode(
				',',
				(string) $this->docFieldsToSearch
			);
		}
		
		if (isset($_REQUEST['query'])){
			$this->currentQuery = trim(\ddTools::$modx->db->escape($_REQUEST['query']));
		}
	}
	
	/**
	 * applyToSnippetParams
	 * @version 1.0.2 (2018-03-19)
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
			
			foreach (
				$this->docFieldsToSearch as
				$docField
			){
				$searchQueries[] = '`' . trim($docField) . '` LIKE("%' . $this->currentQuery . '%")';
			}
			
			$snippetParams['filter'] .= ' (' . implode(
				' OR ',
				$searchQueries
			) . ')';
		}
		
		return $snippetParams;
	}
}