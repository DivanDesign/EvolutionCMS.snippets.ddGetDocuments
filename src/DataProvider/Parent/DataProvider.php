<?php
namespace ddGetDocuments\DataProvider\Parent;


class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	public $defaultParams = array(
		'parentId' => 0,
		'depth' => 1,
		'filter' => '`published` = 1 AND `deleted` = 0'
	);
	
	private 
		$siteContentTableName,
		$tmplvarTableName,
		$tmplvarContentvaluesTableName,
		$tmplvarTemplatesTableName;
	
	public function __construct(){
		global $modx;
		
		$this->siteContentTableName = $modx->getFullTableName('site_content');
		$this->tmplvarTableName = $modx->getFullTableName('site_tmplvars');
		$this->tmplvarContentvaluesTableName = $modx->getFullTableName('site_tmplvar_contentvalues');
		$this->tmplvarTemplatesTableName = $modx->getFullTableName('site_tmplvar_templates');
	}
	
	/**
	 * @param array $providerParams
	 * @param array $snippetParams
	 * @return array
	 */
	protected function getDataFromSource(array $providerParams, array $snippetParams){
		global $modx;
		$output = array();
		
		//TODO: эти проверки с дефолтами надо куда-то вынести
		$parentId = $this->defaultParams['parentId'];
		
		if(isset($providerParams['parentId'])){
			$parentId = $providerParams['parentId'];
		}
		
		$depth = $this->defaultParams['depth'];
		
		if(isset($providerParams['depth'])){
			$depth = $providerParams['depth'];
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
		
		$filter = $this->defaultParams['filter'];
		
		if(isset($snippetParams['filter'])){
			$filter = $snippetParams['filter'];
		}
		
		//Start to make query to fetch the required documents
		//Aliases:
		//c - site_content
		//tvt - site_tmplvar_templates
		//tv - site_tmplvars
		//tvcv - site_tmplvar_contentvalues
		
		//By default, the required data is just fetched from the site_content table
		$fromQuery = "{$this->siteContentTableName}";
		$filterQuery = '';
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filter)){
			$usedFields = $this->getUsedFieldsFromFilter($filter);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//select query
				$selectTvsQuery = "SELECT `c`.*,";
				$fromTvsQuery = "FROM {$this->siteContentTableName} as `c`";
				//join query
				$joinTvsQuery = '';
				//where query
				$whereTvsQuery = '';
				
				$tvCounter = 1;
				
				foreach($usedFields['tvs'] as $tvName){
					//alias of tmplvar_templates
					$tvtAlias = "`tvt_$tvCounter`";
					//alias of tmplvars
					$tvAlias = "`tv_$tvCounter`";
					//alias of tmplvar_contentvalues
					$tvcvAlias = "`tvcv_$tvCounter`";
					//select not null value from either the real value column or default
					$selectTvsQuery .= "coalesce($tvcvAlias.`value`, $tvAlias.`default_text`) as `$tvName`,";
					$joinTvsQuery .=
						" LEFT JOIN {$this->tmplvarTemplatesTableName} AS $tvtAlias ON $tvtAlias.`templateid` = `c`.`template`".
						" LEFT JOIN {$this->tmplvarTableName} AS $tvAlias ON $tvAlias.`id` = $tvtAlias.`tmplvarid`".
						" LEFT JOIN {$this->tmplvarContentvaluesTableName} AS $tvcvAlias ON $tvcvAlias.`contentid` = `c`.`id` AND $tvcvAlias.`tmplvarid` = $tvAlias.`id`";
					$whereTvsQuery .= "$tvAlias.`name` = '$tvName' and";
					
					$tvCounter++;
				}
				
				$selectTvsQuery = trim($selectTvsQuery, ',');
				$whereTvsQuery = "WHERE ".trim($whereTvsQuery, ' and');
				
				//complete from query
				$fromQuery = "($selectTvsQuery $fromTvsQuery $joinTvsQuery $whereTvsQuery)";
			}
			
			$filterQuery = "AND ($filter)";
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
		
		$data = $modx->db->makeArray($modx->db->query("
			SELECT `documents`.`id` FROM $fromQuery AS `documents`
			WHERE `documents`.`id` IN ($allChildrenIdsStr) $filterQuery $orderByQuery $limitQuery
		"));
		
		if(is_array($data)){
			$output = $data;
		}
		
		return $output;
	}
	
	protected function getAllChildrenIds(array $parentIds, $depth){
		global $modx;
		$output = array();
		
		$parentIdsStr = implode(',', $parentIds);
		
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
		
		return $output;
	}
}