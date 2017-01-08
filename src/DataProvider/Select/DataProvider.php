<?php

namespace ddGetDocuments\DataProvider\Select;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	public $defaultParams = [
		'ids' => null
	];
	
	/**
	 * getDataFromSource
	 * @version 1.0.5 (2017-01-05)
	 * 
	 * @param $input {ddGetDocuments\Input}
	 * 
	 * @return {ddGetDocuments\DataProvider\Output}
	 */
	protected function getDataFromSource(Input $input){
		global $modx;
		$output = new Output([], 0);
		
		$ids = $this->defaultParams['ids'];
		
		if(isset($input->providerParams)){
			$ids = (string) $input->providerParams['ids'];
		}
		
		$filter = null;
		
		if(isset($input->snippetParams['filter'])){
			$filter = $input->snippetParams['filter'];
		}
		
		if(isset($input->snippetParams['offset'])){
			$offset = $input->snippetParams['offset'];
		}
		
		if(isset($input->snippetParams['total'])){
			$total = $input->snippetParams['total'];
		}
		
		if(isset($input->snippetParams['orderBy'])){
			$orderBy = $input->snippetParams['orderBy'];
		}
		
		$fromAndFilterQueries = $this->prepareFromAndFilterQueries($filter);
		
		$fromQuery = $fromAndFilterQueries['from'];
		$filterQuery = $fromAndFilterQueries['filter'];
		
		$orderByQuery = '';
		
		if(!empty($orderBy)){
			$orderByQuery = 'ORDER BY '.$orderBy;
		//Order by selected IDs sequence
		}elseif(!empty($ids)){
			$orderByQuery = 'ORDER BY FIELD (`documents`.`id`,'.$ids.')';
		}
		
		$limitQuery = '';
		
		if(
			!empty($offset) &&
			!empty($total)
		){
			$limitQuery = 'LIMIT '.$offset.','.$total;
		}elseif(
			empty($offset) &&
			!empty($total)
		){
			$limitQuery = 'LIMIT '.$total;
		}elseif(
			!empty($offset) &&
			empty($total)
		){
			$limitQuery = 'LIMIT '.$offset.','.PHP_INT_MAX;
		}
		
		$idsWhereQuery = '';
		if(!empty($ids)){
			$idsWhereQuery = '`documents`.`id` IN ('.$ids.')';
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
			
			$data = $modx->db->makeArray($modx->db->query('
				SELECT
					SQL_CALC_FOUND_ROWS `documents`.`id`
				FROM '.$fromQuery.' AS `documents`
				'.$whereQuery.' '.$orderByQuery.' '.$limitQuery.'
			'));
			
			$totalFound = $modx->db->getValue('SELECT FOUND_ROWS()');
			
			if(is_array($data)){
				$output = new Output($data, $totalFound);
			}
		}
		
		return $output;
	}
}