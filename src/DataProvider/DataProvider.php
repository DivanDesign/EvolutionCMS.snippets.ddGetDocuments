<?php
namespace ddGetDocuments\DataProvider;


use ddGetDocuments\Input;

abstract class DataProvider
{
	protected
		/**
		 * @property $resourcesTableName {string} — Source DB table name. Default: \ddTools::$tables['site_content'].
		 */
		$resourcesTableName = 'site_content',
		/**
		 * @property $resourcesFieldsToGet {stdClass} — Document fields which need to get.
		 * @property $resourcesFieldsToGet->fields {array} — Common document fileds.
		 * @property $resourcesFieldsToGet->fields[i] {string} — Field name.
		 * @property $resourcesFieldsToGet->tvs {array} — TVs.
		 * @property $resourcesFieldsToGet->tvs[i] {string} — TV name.
		 */
		$resourcesFieldsToGet = [
			'fields' => ['id'],
			'tvs' => []
		],
		$total,
		$filter,
		$offset,
		$orderBy
	;
	
	/**
	 * @property $getResourcesDataFromDb_tvsSQL {string} — Temporary code for compatibility with MariaDB < 10.4. This code must be removed when MariaDB 10.4 will be released.
	 */
	private $getResourcesDataFromDb_tvsSQL = 'JSON_OBJECTAGG(
		`tvName`.`name`,
		`tvValue`.`value`
	)';
	
	/**
	 * includeProviderByName
	 * @version 1.0.5 (2020-03-10)
	 * 
	 * @param $providerName
	 * @return string
	 * @throws \Exception
	 */
	public final static function includeProviderByName($providerName){
		$providerName = ucfirst(strtolower($providerName));
		$providerPath =
			$providerName .
			DIRECTORY_SEPARATOR .
			'DataProvider' .
			'.php'
		;
		
		if(is_file(__DIR__.DIRECTORY_SEPARATOR.$providerPath)){
			require_once($providerPath);
			
			return
				__NAMESPACE__ .
				'\\' .
				$providerName .
				'\\' .
				'DataProvider'
			;
		}else{
			throw new \Exception(
				(
					'Data provider ' .
					$providerName .
					' not found.'
				),
				500
			);
		}
	}
	
	/**
	 * __construct
	 * @version 1.2.1 (2020-03-10)
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
		foreach (
			$input->providerParams as
			$paramName =>
			$paramValue
		){
			//Validation
			if (property_exists(
				$this,
				$paramName
			)){
				$this->{$paramName} = $paramValue;
			}
		}
		
		//Init source DB table name
		$this->resourcesTableName =
			isset(\ddTools::$tables[$this->resourcesTableName]) ?
			\ddTools::$tables[$this->resourcesTableName] :
			\ddTools::$modx->getFullTableName($this->resourcesTableName)
		;
		
		//Init needed resources fields
		$this->resourcesFieldsToGet = (object) $this->resourcesFieldsToGet;
		
		//TODO: Temporary code for compatibility with MariaDB < 10.4. This code must be removed when MariaDB 10.4 will be released.
		$dbVersion = \ddTools::$modx->db->getValue(\ddTools::$modx->db->query('SELECT VERSION()'));
		
		if (
			//MariaDB is used
			stripos(
				$dbVersion,
				'mariadb'
			) !== false &&
			//And version < 10.4
			version_compare(
				$dbVersion,
				'10.4',
				'<'
			)
		){
			$this->getResourcesDataFromDb_tvsSQL = 'CONCAT(
				"{",
				GROUP_CONCAT(
					TRIM(
						LEADING "{" FROM TRIM(
							TRAILING "}" FROM JSON_OBJECT(
								`tvName`.`name`, 
								`tvValue`.`value`
							)
						)
					)
				),
				"}"
			)';
		}
	}
	
	/**
	 * addResourcesFieldsToGet
	 * @version 2.0.2 (2019-03-19)
	 * 
	 * @param $fields {array}
	 * @param $fields[i] {string} — Name of document field or TV.
	 * 
	 * @return {void}
	 */
	public function addResourcesFieldsToGet($fields){
		//Separate TVs and common document fields
		$fields = \ddTools::prepareDocData([
			'data' => array_flip($fields)
		]);
		
		//Save common fields
		if (!empty($fields->fieldsData)){
			$this->resourcesFieldsToGet->fields = array_unique(array_merge(
				$this->resourcesFieldsToGet->fields,
				array_keys($fields->fieldsData)
			));
		}
		//Save TVs
		if (!empty($fields->tvsData)){
			$this->resourcesFieldsToGet->tvs = array_unique(array_merge(
				$this->resourcesFieldsToGet->tvs,
				array_keys($fields->tvsData)
			));
		}
	}
	
	/**
	 * getResourcesDataFromDb
	 * @version 6.1.3 (2020-03-10)
	 * 
	 * @param $params {array_associative|stdClass}
	 * @param $params['resourcesIds'] — Document IDs to get ($this->filter will be used). Default: ''.
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	protected final function getResourcesDataFromDb($params = []){
		//Empty result by default
		$result = new DataProviderOutput(
			[],
			0
		);
		
		$query = $this->prepareQuery($params);
		
		//Invalid query — empty result
		if(!empty($query)){
			$data = \ddTools::$modx->db->makeArray(\ddTools::$modx->db->query($query));
			
			if(
				is_array($data) &&
				!empty($data)
			){
				$totalFound = \ddTools::$modx->db->getValue('SELECT FOUND_ROWS()');
				
				//If TVs exist
				if (!empty($this->resourcesFieldsToGet->tvs)){
					//Get TVs values
					foreach (
						$data as
						$docIndex =>
						$docValue
					){
						$docValue['TVs'] = json_decode(
							$docValue['TVs'],
							true
						);
						
						foreach (
							$this->resourcesFieldsToGet->tvs as
							$tvName
						){
							//If valid TV exist
							if(isset($docValue['TVs'][$tvName])){
								$data[$docIndex][$tvName] = $docValue['TVs'][$tvName];
							}
						}
						
						unset($data[$docIndex]['TVs']);
					}
				}
				
				$result = new DataProviderOutput(
					$data,
					$totalFound
				);
			}
		}
		
		return $result;
	}
	
	/**
	 * get
	 * @version 2.0.3 (2019-03-19)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		return $this->getResourcesDataFromDb();
	}
	
	/**
	 * getUsedFieldsFromSqlString
	 * @version 1.0 (2019-03-13)
	 * 
	 * @param $sqlString {string_sql}
	 * 
	 * @return $result {array}
	 * @return $result[i] {string} — Field name.
	 */
	protected final function getUsedFieldsFromSqlString($sqlString){
		$result = [];
		
		//Try to find all resources fields (including tvs) used in string (e. g. in “filter”) by the pattern
		preg_match_all(
			"/`(\w+)`/",
			$sqlString,
			$fields
		);
		
		if(!empty($fields[1])){
			$result = $fields[1];
		}
		
		return $result;
	}
	
	/**
	 * prepareUsedDocFieldsFromSqlString
	 * @version 3.0.3 (2020-03-10)
	 * 
	 * @param $sqlString {string_sql}
	 * 
	 * @return $result {stdClass}
	 * @return $result->fields {array} — Document fields. Default: —.
	 * @return $result->fields[] {string} — Field name. @required
	 * @return $result->tvs {array} — Template variables. Default: —.
	 * @return $result->tvs[] {string} — TV name. @required
	 */
	protected function prepareUsedDocFieldsFromSqlString($sqlString){
		$result = (object) [];
		
		$usedFields = $this->getUsedFieldsFromSqlString($sqlString);
		
		if(!empty($usedFields)){
			//Sort out fields from tvs
			$fieldsArray = \ddTools::prepareDocData([
				'data' => array_flip($usedFields),
				//Just something
				'tvAdditionalFieldsToGet' => [
					'name'
				]
			]);
			
			if(!empty($fieldsArray->fieldsData)){
				$result->fields = array_keys($fieldsArray->fieldsData);
			}
			
			//If there were tv names in the passed filter string
			if(!empty($fieldsArray->tvsAdditionalData)){
				$result->tvs = [];
				
				//Check whether the current tv name is an actual tv name
				foreach(
					$fieldsArray->tvsAdditionalData as
					$tvName =>
					$tvData
				){
					//Pupulate the array with the current tv name
					$result->tvs[] = $tvName;
				}
				
				if(empty($result->tvs)){
					unset($result->tvs);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * buildTVsSubQuery
	 * @version 1.0.6 (2020-03-10)
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
		$fromTvsQuery =
			'FROM ' .
			$this->resourcesTableName .
			' as `c`'
		;
		//join query
		$joinTvsQuery = '';
		//where query
		$whereTvsQuery = '';
		
		$tvCounter = 1;
		
		foreach(
			$tvs as
			$tvName
		){
			//alias of tmplvar_templates
			$tvtAlias =
				'`tvt_' .
				$tvCounter .
				'`'
			;
			//alias of tmplvars
			$tvAlias =
				'`tv_' .
				$tvCounter .
				'`'
			;
			//alias of tmplvar_contentvalues
			$tvcvAlias =
				'`tvcv_' .
				$tvCounter .
				'`'
			;
			//select not null value from either the real value column or default
			$selectTvsQuery .=
				'coalesce(' .
				$tvcvAlias .
				'.`value`, ' .
				$tvAlias .
				'.`default_text`) as `' .
				$tvName .
				'`,'
			;
			
			$joinTvsQuery .=
				' LEFT JOIN ' .
					\ddTools::$tables['site_tmplvar_templates'] .
					' AS ' .
					$tvtAlias .
					' ON ' .
					$tvtAlias .
					'.`templateid` = `c`.`template`' .
				' LEFT JOIN ' .
					\ddTools::$tables['site_tmplvars'] .
					' AS ' .
					$tvAlias .
					' ON ' .
					$tvAlias .
					'.`id` = ' .
					$tvtAlias .
					'.`tmplvarid`' .
				' LEFT JOIN ' .
					\ddTools::$tables['site_tmplvar_contentvalues'] .
					' AS ' .
					$tvcvAlias .
					' ON ' .
					$tvcvAlias .
					'.`contentid` = `c`.`id` AND ' .
					$tvcvAlias .
					'.`tmplvarid` = ' .
					$tvAlias .
					'.`id`'
			;
			
			$whereTvsQuery .=
				$tvAlias .
				'.`name` = "' .
				$tvName .
				'" AND'
			;
			
			$tvCounter++;
		}
		
		$selectTvsQuery = trim(
			$selectTvsQuery,
			','
		);
		$whereTvsQuery =
			'WHERE ' .
			trim(
				$whereTvsQuery,
				' AND'
			)
		;
		
		//complete from query
		return
			$selectTvsQuery .
			' ' .
			$fromTvsQuery .
			' ' .
			$joinTvsQuery .
			' ' .
			$whereTvsQuery
		;
	}
	
	/**
	 * prepareQueryData_fromAndFilter
	 * @version 3.0.1 (2020-03-10)
	 * 
	 * @param $filterStr {string} — Filter string. @required
	 * 
	 * @return $result {stdClass}
	 * @return $result->from {string}
	 * @return $result->filter {string}
	 */
	protected final function prepareQueryData_fromAndFilter($filterStr){
		$result = (object) [
			//By default, the required data is just fetched from the site_content table
			'from' => $this->resourcesTableName,
			'filter' => ''
		];
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filterStr)){
			$usedFields = $this->prepareUsedDocFieldsFromSqlString($filterStr);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields->tvs)){
				//complete from query
				$result->from =
					'(' .
					$this->buildTVsSubQuery($usedFields->tvs) .
					')'
				;
			}
			
			$result->filter =
				'(' .
				$filterStr .
				')'
			;
		}
		
		return $result;
	}
	
	/**
	 * prepareQueryData
	 * @version 2.1.1 (2020-03-10)
	 * 
	 * @param $params {array_associative|stdClass}
	 * @param $params['resourcesIds'] — Document IDs to get. Default: ''.
	 * 
	 * @return $result {stdClass}
	 * @return $result->from {string}
	 * @return $result->where {string}
	 * @return $result->orderBy {string}
	 * @return $result->limit {string}
	 */
	protected final function prepareQueryData($params = []){
		//Defaults
		$params = (object) array_merge(
			[
				'resourcesIds' => ''
			],
			(array) $params
		);
		
		$fromAndFilterQueries = $this->prepareQueryData_fromAndFilter($this->filter);
		
		$result = (object) [
			'from' => $fromAndFilterQueries->from,
			'where' => '',
			'orderBy' => '',
			'limit' => '',
		];
		
		if(!empty($this->orderBy)){
			$result->orderBy =
				'ORDER BY ' .
				$this->orderBy
			;
		}
		
		//If LIMIT needed
		if (
			!empty($this->offset) ||
			!empty($this->total)
		){
			$result->limit = 'LIMIT ';
			
			//Prepare offset
			if (!empty($this->offset)){
				$result->limit .=
					$this->offset .
					','
				;
			}
			
			//Prepare total rows
			if (!empty($this->total)){
				$result->limit .= $this->total;
			}else{
				//All rows
				$result->limit .= PHP_INT_MAX;
			}
		}
		
		if(!empty($params->resourcesIds)){
			$result->where .=
				'`resources`.`id` IN (' .
				$params->resourcesIds .
				')'
			;
				
			if(!empty($fromAndFilterQueries->filter)){
				$result->where .=
					' AND ' .
					$fromAndFilterQueries->filter
				;
			}
		}else{
			$result->where .= $fromAndFilterQueries->filter;
		}
		
		if (!empty($result->where)){
			$result->where =
				'WHERE ' .
				$result->where
			;
		}
		
		return $result;
	}
	
	/**
	 * prepareQuery
	 * @version 1.2.1 (2020-03-10)
	 * 
	 * @param $params {array_associative|stdClass}
	 * @param $params['resourcesIds'] — Document IDs to get ($this->filter will be used). Default: ''.
	 * 
	 * @return $result {string}
	 */
	protected function prepareQuery($params = []){
		//Defaults
		$params = (object) array_merge(
			[
				'resourcesIds' => ''
			],
			(array) $params
		);
		
		$result = '';
		
		$queryData = $this->prepareQueryData($params);
		
		//Invalid query data — empty result
		if(!empty($queryData->from)){
			$result = '
				SELECT
					SQL_CALC_FOUND_ROWS
					`resources`.`' . implode(
						'`, `resources`.`',
						$this->resourcesFieldsToGet->fields
					) . '`
			';
			
			//If TVs exist
			if (!empty($this->resourcesFieldsToGet->tvs)){
				$result .= '
					,
					(
						SELECT
							' . $this->getResourcesDataFromDb_tvsSQL . '
						FROM
							' . \ddTools::$tables['site_tmplvar_contentvalues'] . ' as `tvValue`,
							' . \ddTools::$tables['site_tmplvars'] . ' as `tvName`
						WHERE
							`tvName`.`id` = `tvValue`.`tmplvarid` AND
							`resources`.`id` = `tvValue`.`contentid`
					) as `TVs`
				';
			}
			
			$result .=
				'FROM ' .
				$queryData->from .
				' AS `resources` ' .
				$queryData->where .
				' ' .
				$queryData->orderBy .
				' ' .
				$queryData->limit .
				' '
			;
		}
		
		return $result;
	}
}