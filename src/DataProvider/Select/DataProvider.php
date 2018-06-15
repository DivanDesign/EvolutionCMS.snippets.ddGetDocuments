<?php

namespace ddGetDocuments\DataProvider\Select;


use ddGetDocuments\DataProvider\DataProviderOutput;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	protected
		$ids = null,
		$filter = null;
	
	/**
	 * getDataFromSource
	 * @version 2.0 (2018-06-13)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	protected function getDataFromSource(){
		$dataProviderOutput = new DataProviderOutput(
			[],
			0
		);
		
		$fromAndFilterQueries = $this->prepareFromAndFilterQueries($this->filter);
		
		$fromQuery = $fromAndFilterQueries['from'];
		$filterQuery = $fromAndFilterQueries['filter'];
		
		$orderByQuery = '';
		
		if(!empty($this->orderBy)){
			$orderByQuery = 'ORDER BY '.$this->orderBy;
		//Order by selected IDs sequence
		}elseif(!empty($this->ids)){
			$orderByQuery = 'ORDER BY FIELD (`documents`.`id`,'.$this->ids.')';
		}
		
		$limitQuery = '';
		
		if(
			!empty($this->offset) &&
			!empty($this->total)
		){
			$limitQuery = 'LIMIT '.$this->offset.','.$this->total;
		}elseif(
			empty($this->offset) &&
			!empty($this->total)
		){
			$limitQuery = 'LIMIT '.$this->total;
		}elseif(
			!empty($this->offset) &&
			empty($this->total)
		){
			$limitQuery = 'LIMIT '.$this->offset.','.PHP_INT_MAX;
		}
		
		$idsWhereQuery = '';
		if(!empty($this->ids)){
			$idsWhereQuery = '`documents`.`id` IN ('.$this->ids.')';
		}
		
		if(
			!empty($idsWhereQuery) ||
			!empty($filterQuery)
		){
			$whereQuery = 'WHERE ';
			if(!empty($idsWhereQuery)){
				$whereQuery .= $idsWhereQuery;
				
				if(!empty($filterQuery)){
					$whereQuery .= ' AND '.$filterQuery;
				}
			}else{
				$whereQuery .= $filterQuery;
			}
			
			$data = \ddTools::$modx->db->makeArray(\ddTools::$modx->db->query('
				SELECT
					SQL_CALC_FOUND_ROWS `documents`.`id`
				FROM
					'.$fromQuery.' AS `documents`
				'.$whereQuery.' '.$orderByQuery.' '.$limitQuery.'
			'));
			
			$totalFound = \ddTools::$modx->db->getValue('SELECT FOUND_ROWS()');
			
			if(is_array($data)){
				$dataProviderOutput = new DataProviderOutput(
					$data,
					$totalFound
				);
			}
		}
		
		return $dataProviderOutput;
	}
}