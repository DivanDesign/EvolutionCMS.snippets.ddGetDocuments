<?php
namespace ddGetDocuments\Outputter\String;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter
{
	public
		$placeholders = [];
		
	protected
		$itemTpl = null,
		$itemTplFirst = null,
		$itemTplLast = null,
		$wrapperTpl = null,
		$noResults = null,
		$itemGlue = '';
	
	/**
	 * __construct
	 * @version 1.1.1 (2018-06-19)
	 * 
	 * @param $params {array_associative}
	 * @param $params['itemTpl'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * @param $params['itemTplFirst'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: $params['itemTpl'];
	 * @param $params['itemTplLast'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: $params['itemTpl'];
	 * @param $params['wrapperTpl'] {string_chunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
	 * @param $params['noResults'] {string_chunkName} — A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+].
	 * @param $params['placeholders'] {array_associative}. Additional data has to be passed into “itemTpl”, “itemTplFirst”, “itemTplLast” and “wrapperTpl”. Default: [].
	 * @param $params['placeholders'][name] {string} — Key for placeholder name and value for placeholder value. @required
	 * @param $params['itemGlue'] {string} — The string that combines items while rendering. Default: ''.
	 */
	public function __construct(array $params){
		//Prepare item templates
		if (isset($params['itemTpl'])){
			//All items
			$params['itemTpl'] = \ddTools::$modx->getTpl($params['itemTpl']);
			
			$textToGetPlaceholdersFrom = $params['itemTpl'];
			
			//First item
			if (isset($params['itemTplFirst'])){
				$params['itemTplFirst'] = \ddTools::$modx->getTpl($params['itemTplFirst']);
				$textToGetPlaceholdersFrom .= $params['itemTplFirst'];
			}else{
				$params['itemTplFirst'] = $params['itemTpl'];
			}
			//Last item
			if (isset($params['itemTplLast'])){
				$params['itemTplLast'] = \ddTools::$modx->getTpl($params['itemTplLast']);
				$textToGetPlaceholdersFrom .= $params['itemTplFirst'];
			}else{
				$params['itemTplLast'] = $params['itemTpl'];
			}
			
			$params['docFields'] = \ddTools::getPlaceholdersFromText(['text' => $textToGetPlaceholdersFrom]);
		}
		
		//Call base constructor
		parent::__construct($params);
	}
	
	/**
	 * parse
	 * @version 2.1.1 (2018-06-19)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string}
	 */
	public function parse(Output $data){
		$result = '';
		$resultItems = [];
		
		$total = count($data->provider->items);
		
		$generalPlaceholders = [
			'total' => $total,
			'totalFound' => $data->provider->totalFound
		];
		
		$generalPlaceholders = array_merge(
			$generalPlaceholders,
			$this->placeholders
		);
		
		if(isset($data->extenders)){
			$generalPlaceholders = array_merge(
				$generalPlaceholders,
				[
					'extenders' => $data->extenders
				]
			);
			
			$generalPlaceholders = \ddTools::unfoldArray($generalPlaceholders);
		}
		
		if(
			is_array($data->provider->items) &&
			//Item template is set
			$this->itemTpl !== null
		){
			$maxIndex = $total - 1;
			//Foreach items
			foreach($data->provider->items as $index => $item){
				//Prepare item output template
				if($index == 0){
					$chunkName = $this->itemTplFirst;
				}elseif($index == $maxIndex){
					$chunkName = $this->itemTplLast;
				}else{
					$chunkName = $this->itemTpl;
				}
				
				$resultItems[] = \ddTools::parseSource(\ddTools::parseText([
					'text' => $chunkName,
					'data' => array_merge(
						$item,
						$generalPlaceholders,
						[
							'itemNumber' => $index + 1,
							'itemNumberZeroBased' => $index
						]
					)
				]));
			}
		}
		
		$result = implode(
			$this->itemGlue,
			$resultItems
		);
		
		//If no items found and “noResults” is not empty
		if(
			$total == 0 &&
			$this->noResults !== null &&
			$this->noResults != ''
		){
			if(!is_null($chunkContent)){
				$result = \ddTools::parseSource(\ddTools::parseText([
					'text' => \ddTools::$modx->getTpl($this->noResults),
					'data' => $generalPlaceholders
				]));
			}else{
				$result = $this->noResults;
			}
		}elseif($this->wrapperTpl !== null){
			$result = \ddTools::parseText([
				'text' => \ddTools::$modx->getTpl($this->wrapperTpl),
				'data' => array_merge(
					$generalPlaceholders,
					[
						'ddGetDocuments_items' => $result
					]
				)
			]);
		}
		
		return $result;
	}
}