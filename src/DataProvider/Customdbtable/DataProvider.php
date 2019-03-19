<?php
namespace ddGetDocuments\DataProvider\Customdbtable;


use ddGetDocuments\DataProvider\DataProviderOutput;
use ddGetDocuments\Input;

class DataProvider extends \ddGetDocuments\DataProvider\DataProvider
{
	protected
		$resourcesTableName = ''
	;
	
	/**
	 * addResourcesFieldsToGet
	 * @version 1.0 (2019-03-19)
	 * 
	 * @param $fields {array}
	 * @param $fields[i] {string} — Name of table column to add.
	 * 
	 * @return {void}
	 */
	public function addResourcesFieldsToGet($fields){
		$this->resourcesFieldsToGet->fields = array_unique(array_merge(
			$this->resourcesFieldsToGet->fields,
			$fields
		));
	}
	
	/**
	 * prepareUsedDocFieldsFromSqlString
	 * @version 1.0 (2019-03-19)
	 * 
	 * @param $sqlString {string_sql}
	 * 
	 * @return $result {stdClass}
	 * @return $result->fields {array} — Document fields. Default: —.
	 * @return $result->fields[] {string} — Field name. @required
	 */
	protected function prepareUsedDocFieldsFromSqlString($sqlString){
		$result = (object) [];
		
		$usedFields = $this->getUsedFieldsFromSqlString($sqlString);
		
		if(!empty($usedFields)){
			$result->fields = $this->getUsedFieldsFromSqlString($sqlString);
		}
		
		return $result;
	}
}