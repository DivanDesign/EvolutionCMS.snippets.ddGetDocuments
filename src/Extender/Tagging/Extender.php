<?php
namespace ddGetDocuments\Extender\Tagging;


use ddGetDocuments\DataProvider\Output;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		//Current selected tags
		$currentTags = [],
		//The parameter in $_REQUEST to get the tags value from
		$tagsRequestParamName = 'tags',
		//A document field (TV) contains tags
		$tagsDocumentField = 'tags',
		//Tags delimiter
		$tagsDelimiter = ', ';
	
	public function __construct(array $extenderParams){
		global $modx;
		
		if(isset($extenderParams['tagsRequestParamName'])){
			$this->tagsRequestParamName = (string) $extenderParams['tagsRequestParamName'];
		}
		
		if(isset($extenderParams['tagsDocumentField'])){
			$this->tagsDocumentField = (string) $extenderParams['tagsDocumentField'];
		}
		
		if(isset($extenderParams['tagsDelimiter'])){
			$this->tagsDelimiter = (string) $extenderParams['tagsDelimiter'];
		}
		
		if (isset($_REQUEST[$this->tagsRequestParamName])){
			$this->currentTags = $_REQUEST[$this->tagsRequestParamName];
			
			//?tags[]=someTag1&tags[]=someTag2
			//or
			//?tags=someTag1,someTag2
			if (!is_array($this->currentTags)){
				$this->currentTags = explode(',', trim($this->currentTags));
			}
			
			foreach ($this->currentTags as $index => $value){
				$this->currentTags[$index] = $modx->db->escape($value);
			}
		}
	}
	
	/**
	 * applyToSnippetParams
	 * @version 1.0 (2017-01-05)
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
				$tagQueries[] = '`'.$this->tagsDocumentField.'` REGEXP "(^|'.$this->tagsDelimiter.')'.$currentTag.'($|'.$this->tagsDelimiter.')"';
			}
			
			$snippetParams['filter'] .= ' ('.implode(' OR ', $tagQueries).')';
		}
		
		return $snippetParams;
	}
}