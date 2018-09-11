<?php
namespace ddGetDocuments\DataProvider\Parent;


use ddGetDocuments\DataProvider\DataProviderOutput;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	protected
		$parentIds = [0],
		$depth = 1,
		$excludeIds = [],
		$filter = '`published` = 1 AND `deleted` = 0';
	
	/**
	 * __construct
	 * @version 1.1 (2018-08-02)
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
		if (!is_array($this->excludeIds)){
			$this->excludeIds = explode(
				',',
				$this->excludeIds
			);
		}
	}
	
	/**
	 * get
	 * @version 2.0 (2018-09-10)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		$parentIdsStr = implode(
			',',
			$this->parentIds
		);
		$excludeIdsStr = trim(implode(
			',',
			$this->excludeIds
		));
		
		$allChildrenIds = 'SELECT 
			`id`
		FROM 
			' . \ddTools::$tables['site_content'] . '
		WHERE 
			`parent` in (' . $parentIdsStr . ')
			' . ($excludeIdsStr !== '' ? 'AND `id` NOT IN (' . $excludeIdsStr . ')' : '');
		
		if($parentIdsStr !== ''){
			if($this->depth > 1){
				$allChildrenIds = 'WITH RECURSIVE `recursive_query` ( `id`, `parent`, `depth` ) AS (
					SELECT 
						`id`, `parent`, 1
					FROM 
						' . \ddTools::$tables['site_content'] . '
					WHERE 
						`parent` in (' . $parentIdsStr . ')
						' . ($excludeIdsStr !== '' ? 'AND `id` NOT IN (' . $excludeIdsStr . ')' : '') . '
					UNION ALL
					SELECT 
						`content`.`id`, `content`.`parent`, `recursive`.`depth`+1
					FROM 
						' . \ddTools::$tables['site_content'] . ' as `content` 
					JOIN 
						`recursive_query` as `recursive` 
					ON 
						`recursive`.`id` = `content`.`parent`
					WHERE 
						`recursive`.`depth` < ' . $this->depth . '
						' . ($excludeIdsStr !== '' ? 'AND `id` NOT IN (' . $excludeIdsStr . ')' : '') . '
				) SELECT 
					DISTINCT `id`
				FROM 
					`recursive_query`';
			}
		}
		return $this->getSelectedDocsFromDb(['docIds' => $allChildrenIds]);
	}
}