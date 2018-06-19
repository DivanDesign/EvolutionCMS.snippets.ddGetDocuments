<?php
namespace ddGetDocuments\DataProvider\Select;


use ddGetDocuments\DataProvider\DataProviderOutput;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	protected
		$ids = null,
		$filter = null;
	
	/**
	 * get
	 * @version 1.0 (2018-06-19)
	 * 
	 * @return {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public function get(){
		//Order by selected IDs sequence
		if (
			empty($this->orderBy) &&
			!empty($this->ids)
		){
			$this->orderBy = 'ORDER BY FIELD (`documents`.`id`,'.$this->ids.')';
		}
		
		return $this->getSelectedDocsFromDb(['docIds' => $this->ids]);
	}
}