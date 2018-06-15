<?php
namespace ddGetDocuments\OutputFormat\String;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	protected
		$itemTpl = null,
		$itemTplFirst = null,
		$itemTplLast = null,
		$wrapperTpl = null,
		$noResults = null,
		$placeholders = [],
		$itemGlue = '';
	
	/**
	 * __construct
	 * @version 1.0 (2018-06-12)
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
		//Call base constructor
		parent::__construct($params);
		
		//Prepare item templates
		if (isset($this->itemTpl)){
			//All items
			$this->itemTpl = \ddTools::$modx->getTpl($this->itemTpl);
			//First item
			if (isset($this->itemTplFirst)){
				$this->itemTplFirst = \ddTools::$modx->getTpl($this->itemTplFirst);
			}else{
				$this->itemTplFirst = $this->itemTpl;
			}
			//Last item
			if (isset($this->itemTplLast)){
				$this->itemTplLast = \ddTools::$modx->getTpl($this->itemTplLast);
			}else{
				$this->itemTplLast = $this->itemTpl;
			}
		}
	}
	
	/**
	 * parse
	 * @version 2.0 (2018-06-12)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string}
	 */
	public function parse(Output $data){
		$result = '';
		$resultItems = [];
		$dataArray = $data->toArray();
		
		$total = count($dataArray['provider']['items']);
		
		$generalPlaceholders = [
			'total' => $total,
			'totalFound' => $dataArray['provider']['totalFound']
		];
		
		$generalPlaceholders = array_merge(
			$generalPlaceholders,
			$this->placeholders
		);
		
		if(isset($dataArray['extenders'])){
			$generalPlaceholders = array_merge(
				$generalPlaceholders,
				[
					'extenders' => $dataArray['extenders']
				]
			);
			
			$generalPlaceholders = \ddTools::unfoldArray($generalPlaceholders);
		}
		
		if(
			is_array($dataArray['provider']['items']) &&
			//Item template is set
			$this->itemTpl !== null
		){
			$maxIndex = $total - 1;
			//Foreach items
			foreach($dataArray['provider']['items'] as $index => $item){
				//Prepare item output template
				if($index == 0){
					$chunkName = $this->itemTplFirst;
				}elseif($index == $maxIndex){
					$chunkName = $this->itemTplLast;
				}else{
					$chunkName = $this->itemTpl;
				}
				
				//Get TV values
				$document = \ddTools::getTemplateVarOutput(
					'*',
					$item['id']
				);
				
				if(!empty($document)){
					$resultItems[] = \ddTools::parseSource(\ddTools::parseText([
						'text' => $chunkName,
						'data' => array_merge(
							$document,
							$generalPlaceholders,
							[
								'itemNumber' => $index + 1,
								'itemNumberZeroBased' => $index
							]
						)
					]));
				}
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