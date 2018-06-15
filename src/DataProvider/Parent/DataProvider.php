<?php
namespace ddGetDocuments\DataProvider\Parent;


use ddGetDocuments\DataProvider\DataProviderOutput;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	protected
		$parentIds = [0],
		$depth = 1,
		$filter = '`published` = 1 AND `deleted` = 0';
	
	/**
	 * __construct
	 * @version 1.0 (2018-06-12)
	 * 
	 * @param $input {\ddGetDocuments\Input}
	 */
	public function __construct(Input $input){
		//Call base constructor
		parent::__construct($input);
		
		//Comma separated strings support
		if (!is_array($this->parentIds)){
			$this->parentIds = explode(
				',',
				$this->parentIds
			);
		}
	}
	
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
		if (!empty($filterQuery)){
			$filterQuery = 'AND '.$filterQuery;
		}
		
		$allChildrenIdsStr = implode(
			',',
			$this->getAllChildrenIds(
				$this->parentIds,
				$this->depth
			)
		);
		
		$orderByQuery = '';
		
		if(!empty($this->orderBy)){
			$orderByQuery = 'ORDER BY '.$this->orderBy;
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
		
		//Check if child documents were found
		if($allChildrenIdsStr !== ''){
			$data = \ddTools::$modx->db->makeArray(\ddTools::$modx->db->query('
				SELECT
					SQL_CALC_FOUND_ROWS `documents`.`id`
				FROM
					'.$fromQuery.' AS `documents`
				WHERE
					`documents`.`id` IN ('.$allChildrenIdsStr.')
					'.$filterQuery.' '.$orderByQuery.' '.$limitQuery.'
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
	
	/**
	 * getAllChildrenIds
	 * @version 1.0.5 (2018-06-12)
	 * 
	 * @return {array}
	 */
	protected function getAllChildrenIds(
		array $parentIds,
		$depth
	){
		$result = [];
		
		$parentIdsStr = implode(
			',',
			$parentIds
		);
		
		if($parentIdsStr !== ''){
			$resultArray = \ddTools::$modx->db->makeArray(\ddTools::$modx->db->query('
				SELECT `id`
				FROM '.\ddTools::$tables['site_content'].'
				WHERE `parent` IN ('.$parentIdsStr.')
			'));
			
			if(
				is_array($resultArray) &&
				!empty($resultArray)
			){
				foreach($resultArray as $document){
					$result[] = $document['id'];
				}
				
				if($depth > 1){
					$result = array_merge(
						$result,
						$this->getAllChildrenIds(
							$result,
							$depth - 1
						)
					);
				}
			}
		}
		
		return $result;
	}
}