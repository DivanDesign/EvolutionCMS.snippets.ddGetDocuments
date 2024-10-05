<?php
namespace ddGetDocuments\Outputter\String;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter {
	public
		$placeholders = []
	;
	
	protected
		$templates = [
			'item' =>  null,
			'itemFirst' =>  null,
			'itemLast' =>  null,
			'wrapper' =>  null,
			'noResults' => null,
		],
		$itemGlue = ''
	;
	
	/**
	 * construct_prepareFields_templates
	 * @version 1.0.2 (2024-10-05)
	 * 
	 * @param $params {stdClass|arrayAssociative} — @required
	 * @param $params->templates {stdClass|arrayAssociative} — Templates. @required
	 * @param $params->templates->item {string|stringChunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * @param $params->templates->itemFirst {string|stringChunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: $params->templates->item;
	 * @param $params->templates->itemLast {string|stringChunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: $params->templates->item;
	 * @param $params->templates->wrapper {string|stringChunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
	 * @param $params->templates->noResults {string|stringChunkName} — A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+].
	 * @param $params->placeholders {arrayAssociative}. Additional data has to be passed into “templates->item”, “templates->itemFirst”, “templates->itemLast” and “templates->wrapper”. Default: [].
	 * @param $params->placeholders[name] {string} — Key for placeholder name and value for placeholder value. @required
	 * @param $params->itemGlue {string} — The string that combines items while rendering. Default: ''.
	 */
	protected function construct_prepareFields_templates($params){
		// Call base method
		parent::construct_prepareFields_templates($params);
		
		// Prepare item templates
		if (is_string($this->templates->item)){
			$textToGetPlaceholdersFrom = $this->templates->item;
			
			// First item
			if (is_string($this->templates->itemFirst)){
				$textToGetPlaceholdersFrom .= $this->templates->itemFirst;
			}else{
				$this->templates->itemFirst = $this->templates->item;
			}
			// Last item
			if (is_string($this->templates->itemLast)){
				$textToGetPlaceholdersFrom .= $this->templates->itemLast;
			}else{
				$this->templates->itemLast = $this->templates->item;
			}
			
			$this->docFields = \ddTools::getPlaceholdersFromText([
				'text' => $textToGetPlaceholdersFrom,
			]);
		}
	}
	
	/**
	 * parse
	 * @version 2.1.6 (2024-10-05)
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
			'totalFound' => $data->provider->totalFound,
		];
		
		$generalPlaceholders = \DDTools\ObjectTools::extend([
			'objects' => [
				$generalPlaceholders,
				$this->placeholders,
			],
		]);
		
		if(isset($data->extenders)){
			$generalPlaceholders = \DDTools\ObjectTools::extend([
				'objects' => [
					$generalPlaceholders,
					[
						'extenders' => $data->extenders,
					],
				],
			]);
			
			$generalPlaceholders = \ddTools::unfoldArray($generalPlaceholders);
		}
		
		if(
			is_array($data->provider->items)
			// Item template is set
			&& $this->templates->item !== null
		){
			$maxIndex = $total - 1;
			// Foreach items
			foreach(
				$data->provider->items
				as $index
				=> $item
			){
				// Prepare item output template
				if($index == 0){
					$chunkName = $this->templates->itemFirst;
				}elseif($index == $maxIndex){
					$chunkName = $this->templates->itemLast;
				}else{
					$chunkName = $this->templates->item;
				}
				
				$resultItems[] = \ddTools::parseSource(
					\ddTools::parseText([
						'text' => $chunkName,
						'data' => \DDTools\ObjectTools::extend([
							'objects' => [
								$item,
								$generalPlaceholders,
								[
									'itemNumber' => $index + 1,
									'itemNumberZeroBased' => $index,
								],
							],
						]),
					])
				);
			}
		}
		
		$result = implode(
			$this->itemGlue,
			$resultItems
		);
		
		// If no items found and “noResults” is not empty
		if(
			$total == 0
			&& $this->templates->noResults !== null
			&& $this->templates->noResults != ''
		){
			$result = \ddTools::parseSource(
				\ddTools::parseText([
					'text' => $this->templates->noResults,
					'data' => $generalPlaceholders,
				])
			);
		}elseif($this->templates->wrapper !== null){
			$result = \ddTools::parseText([
				'text' => $this->templates->wrapper,
				'data' => \DDTools\ObjectTools::extend([
					'objects' => [
						$generalPlaceholders,
						[
							'ddGetDocuments_items' => $result,
						],
					],
				]),
			]);
		}
		
		return $result;
	}
}