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
	 * @version 1.0.3 (2016-12-21)
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
		
		$fieldDelimiter = $input->snippetParams['fieldDelimiter'];
		
		if(isset($input->snippetParams['offset'])){
			$offset = $input->snippetParams['offset'];
		}
		
		if(isset($input->snippetParams['total'])){
			$total = $input->snippetParams['total'];
		}
		
		if(isset($input->snippetParams['orderBy'])){
			$orderBy = $input->snippetParams['orderBy'];
		}
		
		//By default, the required data is just fetched from the site_content table
		$fromQuery = $this->siteContentTableName;
		$filterQuery = '';
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filter)){
			$usedFields = $this->getUsedFieldsFromFilter($filter, $fieldDelimiter);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//complete from query
				$fromQuery = '('.$this->buildTVsSubQuery($usedFields['tvs']).')';
			}
			
			$filterQuery = $filter;
			$filterQuery = str_replace($fieldDelimiter, '`', $filterQuery);
		}
		
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
		
		$whereQuery = '';
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
		
		return $output;
	}
}