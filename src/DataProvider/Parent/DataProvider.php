<?php
namespace ddGetDocuments\DataProvider\Parent;


class DataProvider extends \ddGetDocuments\DataProvider\DataProvider {
	protected
		$filter = '`published` = 1 AND `deleted` = 0',
		
		$parentIds = [0],
		$depth = 1,
		$excludeIds = []
	;
	
	/**
	 * __construct
	 * @version 2.0 (2021-02-15)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 */
	public function __construct($params){
		//Call base constructor
			parent::__construct($params);
		
		//Comma separated strings support
		if (!is_array($this->parentIds)){
			$this->parentIds = explode(
				',',
				$this->parentIds
			);
		}
		if (!is_array($this->excludeIds)){
			$this->excludeIds = explode(
				',',
				$this->excludeIds
			);
		}
		
		//Parent IDs must be set.
		//TODO: Does we need to to this? People must set correct parameters. Or not? :)
		if (empty($this->parentIds)){
			$this->parentIds = [0];
		}
	}
	
	/**
	 * get
	 * @version 2.0.9 (2020-03-10)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		$parentIdsStr = implode(
			',',
			$this->parentIds
		);
		
		if (!empty($this->excludeIds)){
			$excludeIdsStr =
				'AND `id` NOT IN (' .
				trim(implode(
					',',
					$this->excludeIds
				)) .
				')'
			;
		}else{
			$excludeIdsStr = '';
		}
		
		//Need to get multiple levels
		if($this->depth > 1){
			$allChildrenIds = '
				WITH RECURSIVE `recursive_query` ( `id`, `parent`, `depth` ) AS (
					SELECT
						`id`,
						`parent`,
						1
					FROM
						' . $this->resourcesTableName . '
					WHERE
						`parent` in (' . $parentIdsStr . ')
						' . $excludeIdsStr . '
					UNION ALL
					SELECT
						`content`.`id`,
						`content`.`parent`,
						`recursive`.`depth`+1
					FROM
						' . $this->resourcesTableName . ' as `content`
					JOIN
						`recursive_query` as `recursive`
					ON
						`recursive`.`id` = `content`.`parent`
					WHERE
						`recursive`.`depth` < ' . $this->depth . '
						' . $excludeIdsStr . '
				) SELECT
					DISTINCT `id`
				FROM
					`recursive_query`
			';
		//Just single level
		}else{
			$allChildrenIds = '
				SELECT 
					`id`
				FROM 
					' . $this->resourcesTableName . '
				WHERE 
					`parent` in (' . $parentIdsStr . ')
					' . $excludeIdsStr
			;
		}
		
		return $this->getResourcesDataFromDb([
			'resourcesIds' => $allChildrenIds
		]);
	}
}