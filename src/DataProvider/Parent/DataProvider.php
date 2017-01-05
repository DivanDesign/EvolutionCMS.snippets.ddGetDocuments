<?php
namespace ddGetDocuments\DataProvider\Parent;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	public $defaultParams = [
		'parentIds' => [0],
		'depth' => 1,
		'filter' => '`published` = 1 AND `deleted` = 0'
	];
	
	/**
	 * getDataFromSource
	 * @version 1.0.4 (2017-01-05)
	 * 
	 * @param $input {ddGetDocuments\Input}
	 * 
	 * @return {ddGetDocuments\DataProvider\Output}
	 */
	protected function getDataFromSource(Input $input){
		global $modx;
		$output = new Output([], 0);
		
		//TODO: эти проверки с дефолтами надо куда-то вынести
		$parentIds = $this->defaultParams['parentIds'];
		
		if(isset($input->providerParams['parentIds'])){
			$parentIds = $input->providerParams['parentIds'];
			
			//Comma separated strings
			if (!is_array($parentIds)){
				$parentIds = explode(',', $parentIds);
			}
		}
		
		$depth = $this->defaultParams['depth'];
		
		if(isset($input->providerParams['depth'])){
			$depth = $input->providerParams['depth'];
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
		
		$filter = $this->defaultParams['filter'];
		
		if(isset($input->snippetParams['filter'])){
			$filter = $input->snippetParams['filter'];
		}
		
		$fromAndFilterQueries = $this->prepareFromAndFilterQueries($filter);
		
		$fromQuery = $fromAndFilterQueries['from'];
		$filterQuery = $fromAndFilterQueries['filter'];
		if (!empty($filterQuery)){
			$filterQuery = 'AND '.$filterQuery;
		}
		
		$allChildrenIdsStr = implode(',', $this->getAllChildrenIds($parentIds, $depth));
		
		$orderByQuery = '';
		
		if(!empty($orderBy)){
			$orderByQuery = 'ORDER BY '.$orderBy;
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
		
		//Check if child documents were found
		if($allChildrenIdsStr !== ''){
			$data = $modx->db->makeArray($modx->db->query('
				SELECT SQL_CALC_FOUND_ROWS `documents`.`id`
				FROM '.$fromQuery.' AS `documents`
				WHERE `documents`.`id` IN ('.$allChildrenIdsStr.') '.$filterQuery.' '.$orderByQuery.' '.$limitQuery.'
			'));
			
			$totalFound = $modx->db->getValue('SELECT FOUND_ROWS()');
			
			if(is_array($data)){
				$output = new Output($data, $totalFound);
			}
		}
		
		return $output;
	}
	
	protected function getAllChildrenIds(array $parentIds, $depth){
		global $modx;
		$output = [];
		
		$parentIdsStr = implode(',', $parentIds);
		
		if($parentIdsStr !== ''){
			$outputArray = $modx->db->makeArray($modx->db->query('
				SELECT `id`
				FROM '.$this->siteContentTableName.'
				WHERE `parent` IN ('.$parentIdsStr.')
			'));
			
			if(is_array($outputArray) && !empty($outputArray)){
				foreach($outputArray as $document){
					$output[] = $document['id'];
				}
				
				if($depth > 1){
					$output = array_merge($output, $this->getAllChildrenIds($output, $depth - 1));
				}
			}
		}
		
		return $output;
	}
}