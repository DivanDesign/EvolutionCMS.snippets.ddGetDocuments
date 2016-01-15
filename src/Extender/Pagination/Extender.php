<?php
namespace ddGetDocuments\Extender\Pagination;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		$input,
		$pageIndex,
		$pageGetParamName,
		$zeroBasedPageIndex;
	
	protected $defaultParams = array(
		'pageGetParamName' => 'page',
		'zeroBasedPageIndex' => false
	);
	
	/**
	 * applyToInput
	 * 
	 * @param Input $input
	 * 
	 * @return Input
	 */
	public function applyToInput(Input $input)
	{
		if(isset($input->extenderParams['pagination'])){
			$this->pageGetParamName =
				isset($input->extenderParams['pagination']['pageGetParamName'])?
				(string) $input->extenderParams['pagination']['pageGetParamName']:
				$this->pageGetParamName = $this->defaultParams['pageGetParamName'];
			
			$this->zeroBasedPageIndex =
				isset($input->extenderParams['pagination']['zeroBasedPageIndex'])?
				(bool) $input->extenderParams['pagination']['zeroBasedPageIndex']:
				$this->defaultParams['zeroBasedPageIndex'];
			
			$this->pageIndex =
				isset($_REQUEST[$this->pageGetParamName])?
				(int) $_REQUEST[$this->pageGetParamName]:
				($this->zeroBasedPageIndex? 0: 1);
			
			if(isset($input->snippetParams['total'])){
				$input->snippetParams['offset'] = 
					(
						$this->zeroBasedPageIndex?
						$this->pageIndex:
						$this->pageIndex - 1
					)
					* $input->snippetParams['total'];
			}
		}
		
		$this->input = $input;
		
		return $input;
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
		if($this->input->snippetParams['total'] != 0){
			$pagesTotal = ceil($outputArray['totalFound']/$this->input->snippetParams['total']);
			
			//If the current page index is greater than the total number of pages
			//then it has to be reset
			if($this->pageIndex > $pagesTotal){
				$this->pageIndex = ($this->zeroBasedPageIndex)? 0: 1;
			}
			
			//Iterating through pages
			for($pageIndex = 0; $pageIndex < $pagesTotal; $pageIndex++){
				$pageChunk = $this->input->extenderParams['pagination']['pageTpl'];
				
				//Check if the page we're iterating through is current
				if(
					($this->zeroBasedPageIndex && $pageIndex == $this->pageIndex) ||
					(!$this->zeroBasedPageIndex && $pageIndex == ($this->pageIndex - 1))
				){
					$pageChunk = $this->input->extenderParams['pagination']['currentPageTpl'];
				}
				
				$pagesOutputText .= \ddTools::parseSource(
					$modx->parseChunk(
						$pageChunk, array(
							'url' => "?{$this->pageGetParamName}=".($this->zeroBasedPageIndex? $pageIndex: $pageIndex + 1),
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
				$this->input->extenderParams['pagination']['previousOff']:
				$this->input->extenderParams['pagination']['previous'];
			
			$nextLinkChunk = (
					($this->zeroBasedPageIndex && $this->pageIndex == ($pagesTotal - 1)) ||
					(!$this->zeroBasedPageIndex && $this->pageIndex == $pagesTotal)
				)?
				$this->input->extenderParams['pagination']['nextOff']:
				$this->input->extenderParams['pagination']['next'];
			
			$outputText = \ddTools::parseSource(
				$modx->parseChunk(
					$this->input->extenderParams['pagination']['wrapperTpl'],
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