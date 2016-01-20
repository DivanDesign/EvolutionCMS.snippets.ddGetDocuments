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
		//Whether page index is zero based
		$zeroBasedPageIndex = false,
		
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
	
	public function __construct(array $extenderParams)
	{
		if(isset($extenderParams['pageIndexRequestParamName'])){
			$this->pageIndexRequestParamName = (string) $extenderParams['pageIndexRequestParamName'];
		}
		
		if(isset($extenderParams['zeroBasedPageIndex'])){
			$this->zeroBasedPageIndex = (bool) $extenderParams['zeroBasedPageIndex'];
		}
		
		$this->pageIndex =
			isset($_REQUEST[$this->pageIndexRequestParamName])?
				(int) $_REQUEST[$this->pageIndexRequestParamName]:
				($this->zeroBasedPageIndex? 0: 1);
		
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
	public function applyToSnippetParams(array $snippetParams)
	{
		//If “total” is set then we need to override “offset” according to the current page index
		if(isset($snippetParams['total'])){
			$snippetParams['offset'] =
				(
					$this->zeroBasedPageIndex?
						$this->pageIndex:
						$this->pageIndex - 1
				)
				* $snippetParams['total'];
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
	public function applyToOutput(Output $output)
	{
		global $modx;
		
		$outputArray = $output->toArray();
		
		$pagesOutputText = '';
		$outputText = '';
		
		//Check to prevent division by zero
		if($this->snippetParams['total'] != 0){
			$pagesTotal = ceil($outputArray['totalFound']/$this->snippetParams['total']);
			
			//If the current page index is greater than the total number of pages
			//then it has to be reset
			if($this->pageIndex > $pagesTotal){
				$this->pageIndex = ($this->zeroBasedPageIndex)? 0: 1;
			}
			
			//Iterating through pages
			for($pageIndex = 0; $pageIndex < $pagesTotal; $pageIndex++){
				$pageChunk = $this->pageTpl;
				
				//Check if the page we're iterating through is current
				if(
					($this->zeroBasedPageIndex && $pageIndex == $this->pageIndex) ||
					(!$this->zeroBasedPageIndex && $pageIndex == ($this->pageIndex - 1))
				){
					$pageChunk = $this->currentPageTpl;
				}
				
				$pagesOutputText .= \ddTools::parseSource(
					$modx->parseChunk(
						$pageChunk, array(
							'url' => "?{$this->pageIndexRequestParamName}=".($this->zeroBasedPageIndex? $pageIndex: $pageIndex + 1),
							'page' => $this->zeroBasedPageIndex? $pageIndex: $pageIndex + 1
						),
						'[+', '+]'
					)
				);
			}
			
			$previousLinkChunk = (
					($this->zeroBasedPageIndex && $this->pageIndex == 0) ||
					(!$this->zeroBasedPageIndex && $this->pageIndex == 1)
				)?
				$this->previousOffTpl:
				$this->previousTpl;
			
			$nextLinkChunk = (
					($this->zeroBasedPageIndex && $this->pageIndex == ($pagesTotal - 1)) ||
					(!$this->zeroBasedPageIndex && $this->pageIndex == $pagesTotal)
				)?
				$this->nextOffTpl:
				$this->nextTpl;
			
			$outputText = \ddTools::parseSource(
				$modx->parseChunk(
					$this->wrapperTpl,
					array(
						'previous' => \ddTools::parseSource(
							$modx->parseChunk(
								$previousLinkChunk,
								array(),
								'[+', '+]'
							)
						),
						'pages' => $pagesOutputText,
						'next' => \ddTools::parseSource(
							$modx->parseChunk(
								$nextLinkChunk,
								array(),
								'[+', '+]'
							)
						)
					),
					'[+', '+]'
				)
			);
		}
		
		return $outputText;
	}
}