<?php
namespace ddGetDocuments\Extender\Pagination;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class Extender extends \ddGetDocuments\Extender\Extender
{
	private
		$input,
		$pageNumber,
		$pageGetParamName,
		$zeroBasedPageIndex;
	
	protected $defaultParams = array(
		'pageGetParamName' => 'page',
		'zeroBasedPageIndex' => false
	);
	
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
			
			$this->pageNumber =
				isset($_REQUEST[$this->pageGetParamName])?
				(int) $_REQUEST[$this->pageGetParamName]:
				($this->zeroBasedPageIndex? 0: 1);
			
			if(isset($input->snippetParams['total'])){
				$input->snippetParams['offset'] = 
					(
						$this->zeroBasedPageIndex?
						$this->pageNumber:
						$this->pageNumber - 1
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
		
		if($this->input->snippetParams['total'] != 0){
			$pagesTotal = ceil($outputArray['totalFound']/$this->input->snippetParams['total']);
			
			if($this->pageNumber > $pagesTotal){
				$this->pageNumber = ($this->zeroBasedPageIndex)? 0: 1;
			}
			
			for($pageIndex = 0; $pageIndex < $pagesTotal; $pageIndex++){
				$pageChunk = $this->input->extenderParams['pagination']['pageTpl'];
				
				if(
					($this->zeroBasedPageIndex && $pageIndex == $this->pageNumber) ||
					(!$this->zeroBasedPageIndex && $pageIndex == ($this->pageNumber - 1))
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
					($this->zeroBasedPageIndex && $this->pageNumber == 0) ||
					(!$this->zeroBasedPageIndex && $this->pageNumber == 1)
				)?
				$this->input->extenderParams['pagination']['previousOff']:
				$this->input->extenderParams['pagination']['previous'];
			
			$nextLinkChunk = (
					($this->zeroBasedPageIndex && $this->pageNumber == ($pagesTotal - 1)) ||
					(!$this->zeroBasedPageIndex && $this->pageNumber == $pagesTotal)
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