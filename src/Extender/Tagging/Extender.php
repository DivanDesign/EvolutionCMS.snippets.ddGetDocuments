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
	 * @version 1.1.2 (2020-03-10)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->tagsDocumentField {stringTvName} — The document field (TV) contains tags. Default: 'tags'.
	 * @param $params->tagsDelimiter {string} — Tags delimiter in the document field. Default: ', '.
	 * @param $params->tagsRequestParamName {string} — The parameter in $_REQUEST to get the tags value from. Default: 'tags'.
	 */
	public function __construct($params = []){
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
				$index =>
				$value
			){
				$this->currentTags[$index] = \ddTools::$modx->db->escape($value);
			}
		}
	}
	
	/**
	 * applyToSnippetParams
	 * @version 2.0 (2020-03-11)
	 * 
	 * @param $snippetParams {stdClass}
	 * 
	 * @return {stdClass}
	 */
	public function applyToSnippetParams($snippetParams){
		//If URL contains tags
		if (!empty($this->currentTags)){
			if(
				isset($snippetParams->filter) &&
				trim($snippetParams->filter) != ''
			){
				$snippetParams->filter .= ' AND';
			}else{
				$snippetParams->filter = '';
			}
			
			$tagQueries = [];
			
			foreach (
				$this->currentTags as
				$currentTag
			){
				$tagQueries[] =
					'`' .
					$this->tagsDocumentField .
					'` REGEXP "(^|' .
					$this->tagsDelimiter .
					')' .
					$currentTag .
					'($|' .
					$this->tagsDelimiter .
					')"'
				;
			}
			
			$snippetParams->filter .=
				' (' .
				implode(
					' OR ',
					$tagQueries
				) .
				')'
			;
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