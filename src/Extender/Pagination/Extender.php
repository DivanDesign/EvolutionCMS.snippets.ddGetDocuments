<?php
namespace ddGetDocuments\Extender\Pagination;


use ddGetDocuments\DataProvider\DataProviderOutput;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		$snippetParams,
		//Current page index
		$pageIndex,
		//The parameter in $_REQUEST to get the current page index from
		$pageIndexRequestParamName = 'page';
	
	protected
		//Chunk to be used to output pages within the pagination
		$pageTpl = '',
		//Chunk to be used to output the current page within the pagination
		$currentPageTpl = '',
		//Chunk to be used to output the pagination
		$wrapperTpl = '',
		//Chunk to be used to output the navigation block to the next page
		$nextTpl = '',
		//Chunk to be used to output the navigation block to the next page if there are no more pages after
		$nextOffTpl = '',
		//Chunk to be used to output the navigation block to the previous page
		$previousTpl = '',
		//Chunk to be used to output the navigation block to the previous page if there are no more pages before
		$previousOffTpl = '';
	
	/**
	 * __construct
	 * @version 1.2 (2018-06-12)
	 * 
	 * @param $params {array_associative}
	 */
	public function __construct(array $params){
		//Call base constructor
		parent::__construct($params);
		
		if($this->pageTpl != ''){
			$this->pageTpl = \ddTools::$modx->getTpl((string) $this->pageTpl);
		}
		
		if($this->currentPageTpl != ''){
			$this->currentPageTpl = \ddTools::$modx->getTpl((string) $this->currentPageTpl);
		}
		
		if($this->wrapperTpl != ''){
			$this->wrapperTpl = \ddTools::$modx->getTpl((string) $this->wrapperTpl);
		}
		
		if($this->nextTpl != ''){
			$this->nextTpl = \ddTools::$modx->getTpl((string) $this->nextTpl);
		}
		
		if($this->nextOffTpl != ''){
			$this->nextOffTpl = \ddTools::$modx->getTpl((string) $this->nextOffTpl);
		}
		
		if($this->previousTpl != ''){
			$this->previousTpl = \ddTools::$modx->getTpl((string) $this->previousTpl);
		}
		
		if($this->previousOffTpl != ''){
			$this->previousOffTpl = \ddTools::$modx->getTpl((string) $this->previousOffTpl);
		}
		
		$this->pageIndex = isset($_REQUEST[$this->pageIndexRequestParamName]) ? (int) $_REQUEST[$this->pageIndexRequestParamName] :	1;
	}
	
	/**
	 * applyToSnippetParams
	 * 
	 * @param array $snippetParams
	 * 
	 * @return array
	 */
	public function applyToSnippetParams(array $snippetParams){
		//If “total” is set then we need to override “offset” according to the current page index
		if(isset($snippetParams['total'])){
			$snippetParams['offset'] = ($this->pageIndex - 1) * $snippetParams['total'];
		}
		
		$this->snippetParams = $snippetParams;
		
		return $snippetParams;
	}
	
	/**
	 * applyToOutput
	 * @version 1.1.6 (2018-06-19)
	 * 
	 * @param $dataProviderOutput {\ddGetDocuments\DataProvider\DataProviderOutput}
	 * 
	 * @return {string}
	 */
	public function applyToOutput(DataProviderOutput $dataProviderOutput){
		$result = '';
		
		//Check to prevent division by zero
		if($this->snippetParams['total'] != 0){
			$pagesTotal = ceil($dataProviderOutput->totalFound / $this->snippetParams['total']);
			
			if($pagesTotal > 1){
				$urlPrefix = '?';
				
				if (!empty($_SERVER['QUERY_STRING'])){
					parse_str(
						htmlspecialchars_decode($_SERVER['QUERY_STRING']),
						$currentQuery
					);
					
					//Remove MODX internal parameter
					if (isset($currentQuery['q'])){
						unset($currentQuery['q']);
					}
					
					//Remove the “page” parameter
					if (isset($currentQuery[$this->pageIndexRequestParamName])){
						unset($currentQuery[$this->pageIndexRequestParamName]);
					}
					
					if (count($currentQuery) > 0){
						$urlPrefix .= http_build_query($currentQuery).'&';
					}
				}
				
				$pagesOutputText = '';
				
				//If the current page index is greater than the total number of pages
				//then it has to be reset
				if($this->pageIndex > $pagesTotal){
					$this->pageIndex = 1;
				}
				
				//Iterating through pages
				for(
					$pageIndex = 1;
					$pageIndex <= $pagesTotal;
					$pageIndex++
				){
					$pageChunk = $this->pageTpl;
					
					//Check if the page we're iterating through is current
					if($pageIndex == $this->pageIndex){
						$pageChunk = $this->currentPageTpl;
					}
					
					$pagesOutputText .= \ddTools::parseSource(\ddTools::parseText([
						'text' => $pageChunk,
						'data' => [
							'url' => $urlPrefix.$this->pageIndexRequestParamName.'='.$pageIndex,
							'page' => $pageIndex
						]
					]));
				}
				
				$previousLinkChunk = $this->pageIndex == 1 ? $this->previousOffTpl : $this->previousTpl;
				
				$nextLinkChunk = $this->pageIndex == $pagesTotal ? $this->nextOffTpl : $this->nextTpl;
				
				$result = \ddTools::parseSource(\ddTools::parseText([
					'text' => $this->wrapperTpl,
					'data' => [
						'previous' => \ddTools::parseSource(\ddTools::parseText([
							'text' => $previousLinkChunk,
							'data' => [
								'url' => $this->pageIndex == 1 ? '' : $urlPrefix.$this->pageIndexRequestParamName.'='.($this->pageIndex - 1),
								'totalPages' => $pagesTotal
							]
						])),
						'pages' => $pagesOutputText,
						'next' => \ddTools::parseSource(\ddTools::parseText([
							'text' => $nextLinkChunk,
							'data' => [
								'url' => $this->pageIndex == $pagesTotal ? '' : $urlPrefix.$this->pageIndexRequestParamName.'='.($this->pageIndex + 1),
								'totalPages' => $pagesTotal
							]
						]))
					]
				]));
			}
		}
		
		return $result;
	}
}