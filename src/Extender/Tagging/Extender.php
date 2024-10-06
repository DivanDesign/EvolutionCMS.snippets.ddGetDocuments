<?php
namespace ddGetDocuments\Extender\Tagging;


class Extender extends \ddGetDocuments\Extender\Extender {
	private
		// Current selected tags
		$currentTags = []
	;
	
	protected
		// The parameter in $_REQUEST to get the tags value from
		$tagsRequestParamName = 'tags',
		// A document field (TV) contains tags
		$tagsDocumentField = 'tags',
		// Tags delimiter
		$tagsDelimiter = ', '
	;
	
	/**
	 * __construct
	 * @version 1.1.4 (2024-10-05)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->tagsDocumentField {stringTvName} — The document field (TV) contains tags. Default: 'tags'.
	 * @param $params->tagsDelimiter {string} — Tags delimiter in the document field. Default: ', '.
	 * @param $params->tagsRequestParamName {string} — The parameter in $_REQUEST to get the tags value from. Default: 'tags'.
	 */
	public function __construct($params = []){
		// Call base constructor
		parent::__construct($params);
		
		if (isset($_REQUEST[$this->tagsRequestParamName])){
			$this->currentTags = $_REQUEST[$this->tagsRequestParamName];
			
			//?tags[]=someTag1&tags[]=someTag2
			// or
			//?tags=someTag1,someTag2
			if (!is_array($this->currentTags)){
				$this->currentTags = explode(
					',',
					trim($this->currentTags)
				);
			}
			
			foreach (
				$this->currentTags
				as $index
				=> $value
			){
				$this->currentTags[$index] = \ddTools::$modx->db->escape($value);
			}
		}
	}
	
	/**
	 * applyToDataProviderParams
	 * @version 1.0.2 (2024-10-05)
	 * 
	 * @param $dataProviderParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	public function applyToDataProviderParams($dataProviderParams){
		// If URL contains tags
		if (!empty($this->currentTags)){
			if(
				isset($dataProviderParams->filter)
				&& trim($dataProviderParams->filter) != ''
			){
				$dataProviderParams->filter .= ' AND';
			}else{
				$dataProviderParams->filter = '';
			}
			
			$tagQueries = [];
			
			foreach (
				$this->currentTags
				as $currentTag
			){
				$tagQueries[] =
					'`'
					. $this->tagsDocumentField
					. '` REGEXP "(^|'
					. $this->tagsDelimiter
					. ')'
					. $currentTag
					. '($|'
					. $this->tagsDelimiter
					. ')"'
				;
			}
			
			$dataProviderParams->filter .=
				' ('
				. implode(
					' OR ',
					$tagQueries
				)
				. ')'
			;
		}
		
		return $dataProviderParams;
	}
	
	/**
	 * applyToOutput
	 * @version 1.0.4 (2024-10-05)
	 * 
	 * @param $dataProviderOutput {\ddGetDocuments\DataProvider\DataProviderOutput}
	 * 
	 * @return {array}
	 */
	public function applyToOutput(\ddGetDocuments\DataProvider\DataProviderOutput $dataProviderOutput){
		return [
			'currentTags' => $this->currentTags,
		];
	}
}