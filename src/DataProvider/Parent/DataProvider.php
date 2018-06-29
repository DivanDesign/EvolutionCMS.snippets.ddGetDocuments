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
	 * get
	 * @version 1.0 (2018-06-19)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		return $this->getSelectedDocsFromDb(['docIds' => implode(
			',',
			$this->getAllChildrenIds(
				$this->parentIds,
				$this->depth
			)
		)]);
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