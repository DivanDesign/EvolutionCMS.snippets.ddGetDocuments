<?php
namespace ddGetDocuments\Extender\Sortfromurl;


class Extender extends \ddGetDocuments\Extender\Extender {
	private
		$currentOrderBy = ''
	;
	
	/**
	 * __construct
	 * @version 1.0.1 (2024-08-06)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 */
	public function __construct($params = []){
		// Call base constructor
		parent::__construct($params);
		
		if (isset($_REQUEST['orderBy'])){
			$this->currentOrderBy = trim(
				\ddTools::$modx->db->escape(
					rawurldecode($_REQUEST['orderBy'])
				)
			);
		}
	}
	
	/**
	 * applyToDataProviderParams
	 * @version 1.0.1 (2024-08-06)
	 * 
	 * @param $dataProviderParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	public function applyToDataProviderParams($dataProviderParams){
		// If URL contains sorting rules
		if (!empty($this->currentOrderBy)){
			$dataProviderParams->orderBy = $this->currentOrderBy;
		}
		
		return $dataProviderParams;
	}
}