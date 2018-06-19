<?php
namespace ddGetDocuments\DataProvider;


use ddGetDocuments\Input;

abstract class DataProvider
{
	protected
		$total,
		$filter,
		$offset,
		$orderBy;
	
	/**
	 * includeProviderByName
	 * @version 1.0.2 (2018-06-12)
	 * 
	 * @param $providerName
	 * @return string
	 * @throws \Exception
	 */
	public final static function includeProviderByName($providerName){
		$providerName = ucfirst(strtolower($providerName));
		$providerPath = $providerName.DIRECTORY_SEPARATOR.'DataProvider'.".php";
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$providerPath)){
			require_once($providerPath);
			
			return __NAMESPACE__.'\\'.$providerName.'\\'.'DataProvider';
		}else{
			throw new \Exception(
				'Data provider '.$providerName.' not found.',
				500
			);
		}
	}
	
	/**
	 * __construct
	 * @version 1.0 (2018-06-12)
	 * 
	 * @param $input {\ddGetDocuments\Input}
	 */
	function __construct(Input $input){
		//Params from the snippet first
		foreach (
			[
				'total',
				'filter',
				'offset',
				'orderBy'
			]
			as $paramName
		){
			if(isset($input->snippetParams[$paramName])){
				$this->{$paramName} = $input->snippetParams[$paramName];
			}
		}
		
		//Все параметры задают свойства объекта
		foreach ($input->providerParams as $paramName => $paramValue){
			//Validation
			if (property_exists(
				$this,
				$paramName
			)){
				$this->{$paramName} = $paramValue;
			}
		}
	}
	
	/**
	 * getSelectedDocsFromDb
	 * @version 1.0 (2018-06-19)
	 * 
	 * @param $params {array_associative|stdClass}
	 * @param $params['docIds'] — Document IDs to get. Default: ''.
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	protected function getSelectedDocsFromDb($params = []){
		//Defaults
		$params = (object) array_merge([
			'docIds' => ''
		], (array) $params);
		
		$dataProviderOutput = new DataProviderOutput(
			[],
			0
		);
		
		$fromAndFilterQueries = $this->prepareFromAndFilterQueries($this->filter);
		
		$queryData = (object) [
			'from' => $fromAndFilterQueries['from'],
			'where' => '',
			'where_filter' => $fromAndFilterQueries['filter'],
			'orderBy' => '',
			'limit' => '',
		];
		
		if(!empty($this->orderBy)){
			$queryData->orderBy = 'ORDER BY '.$this->orderBy;
		}
		
		if(
			!empty($this->offset) &&
			!empty($this->total)
		){
			$queryData->limit = 'LIMIT '.$this->offset.','.$this->total;
		}elseif(
			empty($this->offset) &&
			!empty($this->total)
		){
			$queryData->limit = 'LIMIT '.$this->total;
		}elseif(
			!empty($this->offset) &&
			empty($this->total)
		){
			$queryData->limit = 'LIMIT '.$this->offset.','.PHP_INT_MAX;
		}
		
		if(!empty($params->docIds)){
			$params->where .= '`documents`.`id` IN ('.$params->docIds.')';
		
			if(!empty($queryData->where_filter)){
				$params->where .= ' AND '.$queryData->where_filter;
			}
		}else{
			$params->where .= $queryData->where_filter;
		}
		
		if(!empty($params->where)){
			$data = \ddTools::$modx->db->makeArray(\ddTools::$modx->db->query('
				SELECT
					SQL_CALC_FOUND_ROWS `documents`.`id`
				FROM
					'.$queryData->from.' AS `documents`
				WHERE
					'.$params->where.' '.$queryData->orderBy.' '.$queryData->limit.'
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
	 * get
	 * @version 2.0.1 (2018-06-19)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		return $this->getSelectedDocsFromDb();
	}
	
	/**
	 * getUsedFieldsFromFilter
	 * @version 1.0.4 (2018-06-17)
	 * 
	 * @param $filterStr {string}
	 * 
	 * @return $result {array_associative}
	 * @return $result['fields'] {array_associative} — Document fields.
	 * @return $result['fields'][] {string} — Field name.
	 * @return $result['tvs'] {array_associative} — Template variables.
	 * @return $result['tvs'][] {array_associative} — TV name.
	 */
	public final function getUsedFieldsFromFilter($filterStr){
		$result = [];
		
		//Try to find all fields/tvs used in filter by the pattern
		preg_match_all(
			"/`(\w+)`/",
			$filterStr,
			$fields
		);
		
		if(!empty($fields[1])){
			//Sort out fields from tvs
			$fieldsArray = \ddTools::prepareDocData([
				'data' => array_flip($fields[1]),
				//Just something
				'tvAdditionalFieldsToGet' => ['name']
			]);
			
			if(!empty($fieldsArray->fieldsData)){
				$result['fields'] = array_keys($fieldsArray->fieldsData);
			}
			
			//If there were tv names in the passed filter string
			if(!empty($fieldsArray->tvsAdditionalData)){
				$result['tvs'] = [];
				
				//Check whether the current tv name is an actual tv name
				foreach($fieldsArray->tvsAdditionalData as $tvName => $tvData){
					//Pupulate the array with the current tv name
					$result['tvs'][] = $tvName;
				}
				
				if(empty($result['tvs'])){
					unset($result['tvs']);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * buildTVsSubQuery
	 * @version 1.0.3 (2018-06-12)
	 * 
	 * @desc A helper method to build subquery with joined TVS to make possible to use filter conditions for both fields and tvs.
	 * 
	 * @param $tvs {array}
	 * 
	 * @return {string}
	 */
	protected function buildTVsSubQuery(array $tvs){
		//Aliases:
		//c - site_content
		//tvt - site_tmplvar_templates
		//tv - site_tmplvars
		//tvcv - site_tmplvar_contentvalues
		
		//select query
		$selectTvsQuery = 'SELECT `c`.*,';
		$fromTvsQuery = 'FROM '.\ddTools::$tables['site_content'].' as `c`';
		//join query
		$joinTvsQuery = '';
		//where query
		$whereTvsQuery = '';
		
		$tvCounter = 1;
		
		foreach($tvs as $tvName){
			//alias of tmplvar_templates
			$tvtAlias = '`tvt_'.$tvCounter.'`';
			//alias of tmplvars
			$tvAlias = '`tv_'.$tvCounter.'`';
			//alias of tmplvar_contentvalues
			$tvcvAlias = '`tvcv_'.$tvCounter.'`';
			//select not null value from either the real value column or default
			$selectTvsQuery .= 'coalesce('.$tvcvAlias.'.`value`, '.$tvAlias.'.`default_text`) as `'.$tvName.'`,';
			
			$joinTvsQuery .=
				' LEFT JOIN '.\ddTools::$tables['site_tmplvar_templates'].' AS '.$tvtAlias.' ON '.$tvtAlias.'.`templateid` = `c`.`template`'.
				' LEFT JOIN '.\ddTools::$tables['site_tmplvars'].' AS '.$tvAlias.' ON '.$tvAlias.'.`id` = '.$tvtAlias.'.`tmplvarid`'.
				' LEFT JOIN '.\ddTools::$tables['site_tmplvar_contentvalues'].' AS '.$tvcvAlias.' ON '.$tvcvAlias.'.`contentid` = `c`.`id` AND '.$tvcvAlias.'.`tmplvarid` = '.$tvAlias.'.`id`';
			
			$whereTvsQuery .= $tvAlias.'.`name` = "'.$tvName.'" AND';
			
			$tvCounter++;
		}
		
		$selectTvsQuery = trim(
			$selectTvsQuery,
			','
		);
		$whereTvsQuery = 'WHERE '.trim(
			$whereTvsQuery,
			' AND'
		);
		
		//complete from query
		return $selectTvsQuery.' '.$fromTvsQuery.' '.$joinTvsQuery.' '.$whereTvsQuery;
	}
	
	/**
	 * prepareFromAndFilterQueries
	 * @version 1.0.2 (2018-06-12)
	 * 
	 * @param $filterStr {string} — Filter string. @required
	 * 
	 * @return $result {array_associative}
	 * @return $result['from'] {string}
	 * @return $result['filter'] {string}
	 */
	protected final function prepareFromAndFilterQueries($filterStr){
		$result = [
			//By default, the required data is just fetched from the site_content table
			'from' => \ddTools::$tables['site_content'],
			'filter' => ''
		];
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filterStr)){
			$usedFields = $this->getUsedFieldsFromFilter($filterStr);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//complete from query
				$result['from'] = '('.$this->buildTVsSubQuery($usedFields['tvs']).')';
			}
			
			$result['filter'] = '('.$filterStr.')';
		}
		
		return $result;
	}
}