<?php
namespace ddGetDocuments\DataProvider\Parent;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	public $defaultParams = array(
		'parentId' => 0,
		'depth' => 1,
		'filter' => '`published` = 1 AND `deleted` = 0'
	);
	
	/**
	 * getDataFromSource
	 * 
	 * @param Input $input
	 * 
	 * @return Output
	 */
	protected function getDataFromSource(Input $input){
		global $modx;
		$output = new Output(array(), 0);
		
		//TODO: эти проверки с дефолтами надо куда-то вынести
		$parentId = $this->defaultParams['parentId'];
		
		if(isset($input->providerParams['parentId'])){
			$parentId = $input->providerParams['parentId'];
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
		
		$fieldDelimiter = $input->snippetParams['fieldDelimiter'];
		
		//By default, the required data is just fetched from the site_content table
		$fromQuery = "{$this->siteContentTableName}";
		$filterQuery = '';
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filter)){
			$usedFields = $this->getUsedFieldsFromFilter($filter, $fieldDelimiter);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//complete from query
				$fromQuery = "(".$this->buildTVsSubQuery($usedFields['tvs']).")";
			}
			
			$filterQuery = "AND ($filter)";
			$filterQuery = str_replace($fieldDelimiter, '`', $filterQuery);
		}
		
		$allChildrenIdsStr = implode(',', $this->getAllChildrenIds(array($parentId), $depth));
		
		$orderByQuery = '';
		
		if(!empty($orderBy)){
			$orderByQuery = "ORDER BY $orderBy";
		}
		
		$limitQuery = '';
		
		if(!empty($offset) && !empty($total)){
			$limitQuery = "LIMIT $offset,$total";
		}elseif(empty($offset) && !empty($total)){
			$limitQuery = "LIMIT $total";
		}elseif(!empty($offset) && empty($total)){
			$limitQuery = "LIMIT $offset,".PHP_INT_MAX;
		}
		
		//Check if child documents were found
		if($allChildrenIdsStr !== ''){
			$data = $modx->db->makeArray($modx->db->query("
				SELECT SQL_CALC_FOUND_ROWS `documents`.`id` FROM $fromQuery AS `documents`
				WHERE `documents`.`id` IN ($allChildrenIdsStr) $filterQuery $orderByQuery $limitQuery
			"));
			
			$totalFoundArray = $modx->db->makeArray($modx->db->query("SELECT FOUND_ROWS() as `totalFound`"));
			$totalFound = $totalFoundArray[0]['totalFound'];
			
			if(is_array($data)){
				$output = new Output($data, $totalFound);
			}
		}
		
		return $output;
	}
	
	protected function getAllChildrenIds(array $parentIds, $depth){
		global $modx;
		$output = array();
		
		$parentIdsStr = implode(',', $parentIds);
		
		if($parentIdsStr !== ''){
			$outputArray = $modx->db->makeArray($modx->db->query("
				SELECT `id` FROM {$this->siteContentTableName}
				WHERE `parent` IN ($parentIdsStr)
			"));
			
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