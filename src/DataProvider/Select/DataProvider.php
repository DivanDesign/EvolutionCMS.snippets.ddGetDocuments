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
	 * @version 1.0.2 (2019-03-13)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		//Order by selected IDs sequence
		if (
			empty($this->orderBy) &&
			!empty($this->ids)
		){
			$this->orderBy = 'FIELD (`documents`.`id`,'.$this->ids.')';
		}
		
		return $this->getSelectedResourcesFromDb(['docIds' => $this->ids]);
	}
}