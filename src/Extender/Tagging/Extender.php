<?php
namespace ddGetDocuments\Extender\Tagging;


use ddGetDocuments\DataProvider\DataProviderOutput;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		//Current selected tags
		$currentTags = []
	;
	
	protected
		//The parameter in $_REQUEST to get the tags value from
		$tagsRequestParamName = 'tags',
		//A document field (TV) contains tags
		$tagsDocumentField = 'tags',
		//Tags delimiter
		$tagsDelimiter = ', '
	;
	
	/**
	 * __construct
	 * @version 1.1.1 (2019-03-19)
	 * 
	 * @param $params {array_associative}
	 * @param $params['tagsDocumentField'] {string_tvName} — The document field (TV) contains tags. Default: 'tags'.
	 * @param $params['tagsDelimiter'] {string} — Tags delimiter in the document field. Default: ', '.
	 * @param $params['tagsRequestParamName'] {string} — The parameter in $_REQUEST to get the tags value from. Default: 'tags'.
	 */
	public function __construct(array $params = []){
		//Call base constructor
		parent::__construct($params);
		
		if (isset($_REQUEST[$this->tagsRequestParamName])){
			$this->currentTags = $_REQUEST[$this->tagsRequestParamName];
			
			//?tags[]=someTag1&tags[]=someTag2
			//or
			//?tags=someTag1,someTag2
			if (!is_array($this->currentTags)){
				$this->currentTags = explode(
					',',
					trim($this->currentTags)
				);
			}
			
			foreach (
				$this->currentTags as
				$index => $value
			){
				$this->currentTags[$index] = \ddTools::$modx->db->escape($value);
			}
		}
	}
	
	/**
	 * applyToSnippetParams
	 * @version 1.0.2 (2018-03-19)
	 * 
	 * @param $snippetParams {array_associative}
	 * 
	 * @return {array_associative}
	 */
	public function applyToSnippetParams(array $snippetParams){
		//If URL contains tags
		if (!empty($this->currentTags)){
			if(
				isset($snippetParams['filter']) &&
				trim($snippetParams['filter']) != ''
			){
				$snippetParams['filter'] .= ' AND';
			}else{
				$snippetParams['filter'] = '';
			}
			
			$tagQueries = [];
			
			foreach ($this->currentTags as $currentTag){
				$tagQueries[] = '`' . $this->tagsDocumentField . '` REGEXP "(^|' . $this->tagsDelimiter . ')' . $currentTag . '($|' . $this->tagsDelimiter . ')"';
			}
			
			$snippetParams['filter'] .= ' (' . implode(
				' OR ',
				$tagQueries
			) . ')';
		}
		
		return $snippetParams;
	}
	
	/**
	 * applyToOutput
	 * @version 1.0.2 (2018-06-13)
	 * 
	 * @param $dataProviderOutput {\ddGetDocuments\DataProvider\DataProviderOutput}
	 * 
	 * @return {array}
	 */
	public function applyToOutput(DataProviderOutput $dataProviderOutput){
		return [
			'currentTags' => $this->currentTags
		];
	}
}