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
		$pageIndexRequestParamName = 'page',
		
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
	 * @version 1.1 (2018-06-12)
	 * 
	 * @param $extenderParams {array_associative}
	 */
	public function __construct(array $extenderParams){
		$this->pageIndex = isset($_REQUEST[$this->pageIndexRequestParamName]) ? (int) $_REQUEST[$this->pageIndexRequestParamName] :	1;
		
		if(isset($extenderParams['pageTpl'])){
			$this->pageTpl = \ddTools::$modx->getTpl((string) $extenderParams['pageTpl']);
		}
		
		if(isset($extenderParams['currentPageTpl'])){
			$this->currentPageTpl = \ddTools::$modx->getTpl((string) $extenderParams['currentPageTpl']);
		}
		
		if(isset($extenderParams['wrapperTpl'])){
			$this->wrapperTpl = \ddTools::$modx->getTpl((string) $extenderParams['wrapperTpl']);
		}
		
		if(isset($extenderParams['nextTpl'])){
			$this->nextTpl = \ddTools::$modx->getTpl((string) $extenderParams['nextTpl']);
		}
		
		if(isset($extenderParams['nextOffTpl'])){
			$this->nextOffTpl = \ddTools::$modx->getTpl((string) $extenderParams['nextOffTpl']);
		}
		
		if(isset($extenderParams['previousTpl'])){
			$this->previousTpl = \ddTools::$modx->getTpl((string) $extenderParams['previousTpl']);
		}
		
		if(isset($extenderParams['previousOffTpl'])){
			$this->previousOffTpl = \ddTools::$modx->getTpl((string) $extenderParams['previousOffTpl']);
		}
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
	 * @version 1.1.4 (2018-06-12)
	 * 
	 * @param $dataProviderOutput {\ddGetDocuments\DataProvider\DataProviderOutput}
	 * 
	 * @return {string}
	 */
	public function applyToOutput(Output $dataProviderOutput){
		$dataProviderOutputArray = $dataProviderOutput->toArray();
		
		$result = '';
		
		//Check to prevent division by zero
		if($this->snippetParams['total'] != 0){
			$pagesTotal = ceil($dataProviderOutputArray['totalFound'] / $this->snippetParams['total']);
			
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