<?php
namespace ddGetDocuments\Extender\Search;


class Extender extends \ddGetDocuments\Extender\Extender {
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
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->docFieldsToSearch {array|stringCommaSepareted} — Document fields to search in. Default: ['pagetitle', 'content'].
	 */
	public function __construct($params = []){
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
	 * @version 2.0 (2020-03-11)
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	public function applyToSnippetParams($snippetParams){
		//If URL contains tags
		if (!empty($this->currentQuery)){
			if(
				isset($snippetParams->filter) &&
				trim($snippetParams->filter) != ''
			){
				$snippetParams->filter .= ' AND';
			}else{
				$snippetParams->filter = '';
			}
			
			$searchQueries = [];
			
			foreach (
				$this->docFieldsToSearch as
				$docField
			){
				$searchQueries[] =
					'`' .
					trim($docField) .
					'` LIKE("%' .
					$this->currentQuery .
					'%")'
				;
			}
			
			$snippetParams->filter .=
				' (' .
				implode(
					' OR ',
					$searchQueries
				) .
				')'
			;
		}
		
		return $snippetParams;
	}
}