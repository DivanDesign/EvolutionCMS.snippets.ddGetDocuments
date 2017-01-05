<?php
namespace ddGetDocuments\DataProvider;


use ddGetDocuments\Input;

abstract class DataProvider
{
	public $defaultParams = [];
	
	protected
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
			throw new \Exception('Data provider '.$providerName.' not found.', 500);
		}
	}
	
	/**
	 * getDataFromSource
	 * 
	 * @param Input $input
	 * 
	 * @return \ddGetDocuments\DataProvider\Output
	 */
	abstract protected function getDataFromSource(Input $input);
	
	/**
	 * get
	 * 
	 * @param Input $input
	 * 
	 * @return Output
	 */
	public final function get(Input $input){
		if(empty($input->providerParams)){
			$input->providerParams = $this->defaultParams;
		}
		
		return $this->getDataFromSource($input);
	}
	
	/**
	 * @param $filterStr
	 * @param string $filterFieldDelimiter
	 * @return array
	 */
	public final function getUsedFieldsFromFilter($filterStr, $filterFieldDelimiter = '`'){
		$output = [];
		//Try to find all fields/tvs used in filter by the pattern
		preg_match_all("/$filterFieldDelimiter(\w+)$filterFieldDelimiter/", $filterStr, $fields);
		
		if(!empty($fields[1])){
			//Sort out fields from tvs
			$fieldsArray = \ddTools::explodeFieldsArr(array_flip($fields[1]));
			
			if(!empty($fieldsArray[0])){
				$output['fields'] = array_keys($fieldsArray[0]);
			}
			
			//If there were tv names in the passed filter string
			if(is_array($fieldsArray[1])){
				$output['tvs'] = [];
				
				//Check whether the current tv name is an actual tv name
				foreach($fieldsArray[1] as $tvName => $tvData){
					if(isset($tvData['id'])){
						//Pupulate the array with the current tv name
						$output['tvs'][] = $tvName;
					}
				}
				
				if(empty($output['tvs'])){
					unset($output['tvs']);
				}
			}
		}
		
		return $output;
	}
	
	/**
	 * buildTVsSubQuery
	 * 
	 * A helper method to build subquery with joined TVS to make possible
	 * to use filter conditions for both fields and tvs.
	 * 
	 * @param array $tvs
	 * @return string
	 */
	protected function buildTVsSubQuery(array $tvs){
		//Aliases:
		//c - site_content
		//tvt - site_tmplvar_templates
		//tv - site_tmplvars
		//tvcv - site_tmplvar_contentvalues
		
		//select query
		$selectTvsQuery = "SELECT `c`.*,";
		$fromTvsQuery = "FROM {$this->siteContentTableName} as `c`";
		//join query
		$joinTvsQuery = '';
		//where query
		$whereTvsQuery = '';
		
		$tvCounter = 1;
		
		foreach($tvs as $tvName){
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
			$whereTvsQuery .= "$tvAlias.`name` = '$tvName' AND";
			
			$tvCounter++;
		}
		
		$selectTvsQuery = trim($selectTvsQuery, ',');
		$whereTvsQuery = "WHERE ".trim($whereTvsQuery, ' AND');
		
		//complete from query
		return "$selectTvsQuery $fromTvsQuery $joinTvsQuery $whereTvsQuery";
	}
	
	/**
	 * prepareFromAndFilterQueries
	 * @version 1.0 (2017-01-04)
	 * 
	 * @param $filterStr {string} — Filter string. @required
	 * @param $fieldDelimiter {string} — Field delimiter. Default: '`'.
	 * 
	 * @return $result {array_associative}
	 * @return $result['from'] {string}
	 * @return $result['filter'] {string}
	 */
	protected final function prepareFromAndFilterQueries($filterStr, $fieldDelimiter = '`'){
		$result = [
			//By default, the required data is just fetched from the site_content table
			'from' => $this->siteContentTableName,
			'filter' => ''
		];
		
		//If a filter is set, it is needed to check which TVs are used in the filter query
		if(!empty($filterStr)){
			$usedFields = $this->getUsedFieldsFromFilter($filterStr, $fieldDelimiter);
			
			//If there are some TV names in the filter query, make a temp table from which the required data will be fetched
			if(!empty($usedFields['tvs'])){
				//complete from query
				$result['from'] = '('.$this->buildTVsSubQuery($usedFields['tvs']).')';
			}
			
			$result['filter'] = '('.$filterStr.')';
			$result['filter'] = str_replace($fieldDelimiter, '`', $result['filter']);
		}
		
		return $result;
	}
}