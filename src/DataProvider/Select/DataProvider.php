<?php

namespace ddGetDocuments\DataProvider\Select;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	public $defaultParams = array(
		'ids' => null
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
		
		$ids = $this->defaultParams['ids'];
		
		if(isset($input->providerParams)){
			$ids = (string) $input->providerParams;
		}
		
		$filter = null;
		
		if(isset($input->snippetParams['filter'])){
			$filter = $input->snippetParams['filter'];
		}
		
		$filterFieldDelimiter = '`';
		
		if(isset($input->snippetParams['filterFieldDelimiter'])){
			$filterFieldDelimiter = $input->snippetParams['filterFieldDelimiter'];
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
		
		//By default, the required data is just fetched from the site_content table
		$fromQuery = "{$this->siteContentTableName}";
		$filterQuery = '';
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filter)){
			$usedFields = $this->getUsedFieldsFromFilter($filter, $filterFieldDelimiter);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//complete from query
				$fromQuery = "(".$this->buildTVsSubQuery($usedFields['tvs']).")";
			}
			
			$filterQuery = "$filter";
			$filterQuery = str_replace($filterFieldDelimiter, '`', $filterQuery);
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