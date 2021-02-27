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
	 * applyToDataProviderParams
	 * @version 1.0 (2021-02-12)
	 * 
	 * @param $dataProviderParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	public function applyToDataProviderParams($dataProviderParams){
		//If URL contains tags
		if (!empty($this->currentQuery)){
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
			
			if(
				isset($dataProviderParams->filter) &&
				trim($dataProviderParams->filter) != ''
			){
				$dataProviderParams->filter .= ' AND';
			}else{
				$dataProviderParams->filter = '';
			}
			
			$dataProviderParams->filter .=
				' (' .
				implode(
					' OR ',
					$searchQueries
				) .
				')'
			;
		}
		
		return $dataProviderParams;
	}
}