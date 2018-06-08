<?php
namespace ddGetDocuments\OutputFormat\String;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	/**
	 * parse
	 * @version 1.1 (2018-06-08)
	 * 
	 * @param $data {Output}
	 * @param $outputFormatParameters {array_associative}
	 * @param $outputFormatParameters['itemTpl'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * @param $outputFormatParameters['itemTplFirst'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * @param $outputFormatParameters['itemTplLast'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * @param $outputFormatParameters['wrapperTpl'] {string_chunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
	 * @param $outputFormatParameters['noResults'] {string_chunkName} — A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+]. 
	 * @param $outputFormatParameters['placeholders'] {array_associative}. Additional data has to be passed into “itemTpl”, “itemTplFirst”, “itemTplLast” and “wrapperTpl”. Default: [].
	 * @param $outputFormatParameters['placeholders'][name] {string} — Key for placeholder name and value for placeholder value. @required
	 * @param $outputFormatParameters['itemGlue'] {string} — The string that combines items while rendering. Default: ''.
	 * 
	 * @return {string}
	 */
	public function parse(
		Output $data,
		array $outputFormatParameters
	){
		global $modx;
		
		$output = '';
		$outputItems = [];
		$dataArray = $data->toArray();
		
		$itemGlue = isset($outputFormatParameters['itemGlue']) ? $outputFormatParameters['itemGlue'] : '';
		
		$total = count($dataArray['provider']['items']);
		
		$generalPlaceholders = [
			'total' => $total,
			'totalFound' => $dataArray['provider']['totalFound']
		];
		
		if(!empty($outputFormatParameters['placeholders'])){
			$generalPlaceholders = array_merge(
				$generalPlaceholders,
				$outputFormatParameters['placeholders']
			);			
		}
		
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
			isset($outputFormatParameters['itemTpl'])
		){
			$maxIndex = $total - 1;
			//Foreach items
			foreach($dataArray['provider']['items'] as $index => $item){
				//Prepare item output template
				if(
					isset($outputFormatParameters['itemTplFirst']) &&
					$index == 0
				){
					$chunkName = $outputFormatParameters['itemTplFirst'];
				}elseif(
					isset($outputFormatParameters['itemTplFirst']) &&
					$index == $maxIndex
				){
					$chunkName = $outputFormatParameters['itemTplLast'];
				}else{
					$chunkName = $outputFormatParameters['itemTpl'];
				}
				
				//Get TV values
				$document = \ddTools::getTemplateVarOutput(
					'*',
					$item['id']
				);
				
				if(!empty($document)){
					$outputItems[] = \ddTools::parseSource(\ddTools::parseText([
						'text' => $modx->getTpl($chunkName),
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
		
		$output = implode($itemGlue, $outputItems);
		
		//If no items found and “noResults” is not empty
		if(
			$total == 0 &&
			isset($outputFormatParameters['noResults']) &&
			$outputFormatParameters['noResults'] != ''
		){
			$chunkContent = $modx->getChunk($outputFormatParameters['noResults']);
			
			if(!is_null($chunkContent)){
				$output = \ddTools::parseSource(\ddTools::parseText([
					'text' => $modx->getTpl($outputFormatParameters['noResults']),
					'data' => $generalPlaceholders
				]));
			}else{
				$output = $outputFormatParameters['noResults'];
			}
		}elseif(isset($outputFormatParameters['wrapperTpl'])){
			$output = \ddTools::parseText([
				'text' => $modx->getTpl($outputFormatParameters['wrapperTpl']),
				'data' => array_merge(
					$generalPlaceholders,
					[
						'ddGetDocuments_items' => $output
					]
				)
			]);
		}
		
		return $output;
	}
}