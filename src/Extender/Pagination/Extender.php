<?php
namespace ddGetDocuments\Extender\Pagination;


use ddGetDocuments\DataProvider\Output;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		$snippetParams,
		//Current page index
		$pageIndex,
		//The parameter in $_REQUEST to get the current page index from
		$pageIndexRequestParamName = 'page',
		
		//Chunk to be used to output pages within the pagination
		$pageTpl,
		//Chunk to be used to output the current page within the pagination
		$currentPageTpl,
		//Chunk to be used to output the pagination
		$wrapperTpl,
		//Chunk to be used to output the navigation block to the next page
		$nextTpl,
		//Chunk to be used to output the navigation block to the next page if there are no more pages after
		$nextOffTpl,
		//Chunk to be used to output the navigation block to the previous page
		$previousTpl,
		//Chunk to be used to output the navigation block to the previous page if there are no more pages before
		$previousOffTpl;
	
	public function __construct(array $extenderParams){
		$this->pageIndex = isset($_REQUEST[$this->pageIndexRequestParamName]) ? (int) $_REQUEST[$this->pageIndexRequestParamName] :	1;
		
		if(isset($extenderParams['pageTpl'])){
			$this->pageTpl = (string) $extenderParams['pageTpl'];
		}
		
		if(isset($extenderParams['currentPageTpl'])){
			$this->currentPageTpl = (string) $extenderParams['currentPageTpl'];
		}
		
		if(isset($extenderParams['wrapperTpl'])){
			$this->wrapperTpl = (string) $extenderParams['wrapperTpl'];
		}
		
		if(isset($extenderParams['nextTpl'])){
			$this->nextTpl = (string) $extenderParams['nextTpl'];
		}
		
		if(isset($extenderParams['nextOffTpl'])){
			$this->nextOffTpl = (string) $extenderParams['nextOffTpl'];
		}
		
		if(isset($extenderParams['previousTpl'])){
			$this->previousTpl = (string) $extenderParams['previousTpl'];
		}
		
		if(isset($extenderParams['previousOffTpl'])){
			$this->previousOffTpl = (string) $extenderParams['previousOffTpl'];
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
	 * 
	 * @param Output $output
	 * 
	 * @return string
	 */
	public function applyToOutput(Output $output){
		global $modx;
		
		$outputArray = $output->toArray();
		
		$pagesOutputText = '';
		$outputText = '';
		
		//Check to prevent division by zero
		if($this->snippetParams['total'] != 0){
			$pagesTotal = ceil($outputArray['totalFound'] / $this->snippetParams['total']);
			
			//If the current page index is greater than the total number of pages
			//then it has to be reset
			if($this->pageIndex > $pagesTotal){
				$this->pageIndex = 1;
			}
			
			//Iterating through pages
			for($pageIndex = 1; $pageIndex <= $pagesTotal; $pageIndex++){
				$pageChunk = $this->pageTpl;
				
				//Check if the page we're iterating through is current
				if($pageIndex == $this->pageIndex){
					$pageChunk = $this->currentPageTpl;
				}
				
				$pagesOutputText .= \ddTools::parseSource(
					$modx->parseChunk(
						$pageChunk, [
							'url' => '?'.$this->pageIndexRequestParamName.'='.$pageIndex,
							'page' => $pageIndex
						],
						'[+', '+]'
					)
				);
			}
			
			$previousLinkChunk = $this->pageIndex == 1 ? $this->previousOffTpl : $this->previousTpl;
			
			$nextLinkChunk = $this->pageIndex == $pagesTotal ? $this->nextOffTpl : $this->nextTpl;
			
			$outputText = \ddTools::parseSource(
				$modx->parseChunk(
					$this->wrapperTpl,
					[
						'previous' => \ddTools::parseSource(
							$modx->parseChunk(
								$previousLinkChunk,
								[
									'url' => $this->pageIndex == 1 ? '' : '?'.$this->pageIndexRequestParamName.'='.($this->pageIndex - 1),
									'totalPages' => $pagesTotal
								],
								'[+', '+]'
							)
						),
						'pages' => $pagesOutputText,
						'next' => \ddTools::parseSource(
							$modx->parseChunk(
								$nextLinkChunk,
								[
									'url' => $this->pageIndex == $pagesTotal ? '' : '?'.$this->pageIndexRequestParamName.'='.($this->pageIndex + 1),
									'totalPages' => $pagesTotal
								],
								'[+', '+]'
							)
						)
					],
					'[+', '+]'
				)
			);
		}
		
		return $outputText;
	}
}