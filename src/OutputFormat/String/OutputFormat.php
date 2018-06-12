<?php
namespace ddGetDocuments\OutputFormat\String;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	/**
	 * parse
	 * @version 1.1.4 (2018-06-12)
	 * 
	 * @param $data {Output}
	 * @param $outputFormatParameters {array_associative}
	 * @param $outputFormatParameters['itemTpl'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * @param $outputFormatParameters['itemTplFirst'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: $outputFormatParameters['itemTpl'];
	 * @param $outputFormatParameters['itemTplLast'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: $outputFormatParameters['itemTpl'];
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
		//TODO: эти проверки с дефолтами надо куда-то вынести
		//Defaults
		$outputFormatParameters = array_merge([
			'itemTpl' => null,
			'wrapperTpl' => null,
			'noResults' => null,
			'placeholders' => [],
			'itemGlue' => ''
		], $outputFormatParameters);
		
		//Prepare item templates
		if ($outputFormatParameters['itemTpl'] !== null){
			//All items
			$outputFormatParameters['itemTpl'] = \ddTools::$modx->getTpl($outputFormatParameters['itemTpl']);
			//First item
			if (isset($outputFormatParameters['itemTplFirst'])){
				$outputFormatParameters['itemTplFirst'] = \ddTools::$modx->getTpl($outputFormatParameters['itemTplFirst']);
			}else{
				$outputFormatParameters['itemTplFirst'] = $outputFormatParameters['itemTpl'];
			}
			//Last item
			if (isset($outputFormatParameters['itemTplLast'])){
				$outputFormatParameters['itemTplLast'] = \ddTools::$modx->getTpl($outputFormatParameters['itemTplLast']);
			}else{
				$outputFormatParameters['itemTplLast'] = $outputFormatParameters['itemTpl'];
			}
		}
		
		$result = '';
		$resultItems = [];
		$dataArray = $data->toArray();
		
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
			$outputFormatParameters['itemTpl'] !== null
		){
			$maxIndex = $total - 1;
			//Foreach items
			foreach($dataArray['provider']['items'] as $index => $item){
				//Prepare item output template
				if($index == 0){
					$chunkName = $outputFormatParameters['itemTplFirst'];
				}elseif($index == $maxIndex){
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
			$outputFormatParameters['itemGlue'],
			$resultItems
		);
		
		//If no items found and “noResults” is not empty
		if(
			$total == 0 &&
			$outputFormatParameters['noResults'] !== null &&
			$outputFormatParameters['noResults'] != ''
		){
			$chunkContent = \ddTools::$modx->getChunk($outputFormatParameters['noResults']);
			
			if(!is_null($chunkContent)){
				$result = \ddTools::parseSource(\ddTools::parseText([
					'text' => \ddTools::$modx->getTpl($outputFormatParameters['noResults']),
					'data' => $generalPlaceholders
				]));
			}else{
				$result = $outputFormatParameters['noResults'];
			}
		}elseif($outputFormatParameters['wrapperTpl'] !== null){
			$result = \ddTools::parseText([
				'text' => \ddTools::$modx->getTpl($outputFormatParameters['wrapperTpl']),
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