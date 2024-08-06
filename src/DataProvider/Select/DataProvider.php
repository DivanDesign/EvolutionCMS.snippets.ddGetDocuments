<?php
namespace ddGetDocuments\DataProvider\Select;


class DataProvider extends \ddGetDocuments\DataProvider\DataProvider {
	protected
		$filter = null,
		
		$ids = null
	;
	
	/**
	 * get
	 * @version 1.0.8 (2024-08-06)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		// Order by selected IDs sequence
		if (
			empty($this->orderBy) &&
			!empty($this->ids)
		){
			$this->orderBy =
				'FIELD (`resources`.`id`,' .
				$this->ids .
				')'
			;
		}
		
		return $this->getResourcesDataFromDb([
			'resourcesIds' => $this->ids
		]);
	}
}