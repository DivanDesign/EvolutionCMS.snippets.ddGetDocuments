<?php
namespace ddGetDocuments\DataProvider\Select;


class DataProvider extends \ddGetDocuments\DataProvider\DataProvider {
	protected
		$filter = null,
		
		$ids = null
	;
	
	/**
	 * get
	 * @version 1.0.9 (2024-10-05)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		// Order by selected IDs sequence
		if (
			empty($this->orderBy)
			&& !empty($this->ids)
		){
			$this->orderBy =
				'FIELD (`resources`.`id`,'
				. $this->ids
				. ')'
			;
		}
		
		return $this->getResourcesDataFromDb([
			'resourcesIds' => $this->ids
		]);
	}
	
	/**
	 * prepareQuery
	 * @version 1.0 (2024-10-06)
	 * 
	 * @param $params {arrayAssociative|stdClass}
	 * @param $params['resourcesIds'] — Document IDs to get ($this->filter will be used).
	 * 
	 * @return $result {string}
	 */
	protected function prepareQuery($params = []){
		return
			// resourcesIds is required
			!empty(
				\DDTools\Tools\Objects::getPropValue([
					'object' => $params,
					'propName' => 'resourcesIds',
				])
			)
			? parent::prepareQuery($params)
			: ''
		;
	}
}