<?php
namespace ddGetDocuments\DataProvider\Select;


use ddGetDocuments\DataProvider\DataProviderOutput;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	protected
		$filter = null,
		
		$ids = null
	;
	
	/**
	 * get
	 * @version 1.0.5 (2019-03-14)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		//Order by selected IDs sequence
		if (
			empty($this->orderBy) &&
			!empty($this->ids)
		){
			$this->orderBy = 'FIELD (`resources`.`id`,' . $this->ids . ')';
		}
		
		return $this->getSelectedResourcesFromDb([
			'resourcesIds' => $this->ids
		]);
	}
}