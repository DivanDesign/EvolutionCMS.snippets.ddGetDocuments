<?php

namespace ddGetDocuments\DataProvider\Select;


use ddGetDocuments\DataProvider\Output;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	public $defaultParams = array(
		'ids' => null
	);

	/**
	 * @param array $providerParams
	 * @param array $snippetParams
	 * @return Output
	 */
	protected function getDataFromSource(array $providerParams, array $snippetParams){
		global $modx;
		$output = new Output(array(), 0);
		
		$ids = $this->defaultParams['ids'];
		
		if(isset($providerParams['ids'])){
			$ids = (string) $providerParams['ids'];
		}
		
		$filter = null;
		
		if(isset($snippetParams['filter'])){
			$filter = $snippetParams['filter'];
		}
		
		if(isset($snippetParams['offset'])){
			$offset = $snippetParams['offset'];
		}
		
		if(isset($snippetParams['total'])){
			$total = $snippetParams['total'];
		}
		
		if(isset($snippetParams['orderBy'])){
			$orderBy = $snippetParams['orderBy'];
		}
		
		//By default, the required data is just fetched from the site_content table
		$fromQuery = "{$this->siteContentTableName}";
		$filterQuery = '';
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filter)){
			$usedFields = $this->getUsedFieldsFromFilter($filter);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//complete from query
				$fromQuery = "(".$this->buildTVsSubQuery($usedFields['tvs']).")";
			}
			
			$filterQuery = "$filter";
		}
		
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
		
		$idsWhereQuery = '';
		if(!empty($ids)){
			$idsWhereQuery = "`documents`.`id` IN ($ids)";
		}
		
		$whereQuery = '';
		if(!empty($idsWhereQuery) || !empty($filterQuery)){
			$whereQuery = "WHERE ";
			if(!empty($idsWhereQuery)){
				$whereQuery .= "$idsWhereQuery AND $filterQuery";
			}else{
				$whereQuery .= $filterQuery;
			}
		}
		
		$data = $modx->db->makeArray($modx->db->query("
			SELECT SQL_CALC_FOUND_ROWS `documents`.`id` FROM $fromQuery AS `documents`
			$whereQuery $orderByQuery $limitQuery
		"));
		
		$totalFoundArray = $modx->db->makeArray($modx->db->query("SELECT FOUND_ROWS() as `totalFound`"));
		$totalFound = $totalFoundArray[0]['totalFound'];
		
		if(is_array($data)){
			$output = new Output($data, $totalFound);
		}
		
		return $output;
	}
}