<?php
namespace ddGetDocuments\OutputFormat\String;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	/**
	 * parse
	 * 
	 * @param Output $data
	 * @param array $outputFormatParameters
	 * @param $outputFormatParameters['itemTpl'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * @param $outputFormatParameters['itemTplFirst'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * @param $outputFormatParameters['itemTplLast'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * @param $outputFormatParameters['wrapperTpl'] {string_chunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
	 * @param $outputFormatParameters['noResults'] {string_chunkName} — A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+]. 
	 * @param $outputFormatParameters['placeholders'] {array_associative}. Additional data has to be passed into “itemTpl”, “itemTplFirst”, “itemTplLast” and “wrapperTpl”. Default: [].
	 * @param $outputFormatParameters['placeholders'][name] {string} — Key for placeholder name and value for placeholder value. @required
	 * 
	 * @return string
	 */
	public function parse(Output $data, array $outputFormatParameters){
		global $modx;
		$output = '';
		$dataArray = $data->toArray();
		
		$total = count($dataArray['provider']['items']);
		$generalPlaceholders = [
			'total' => $total,
			'totalFound' => $dataArray['provider']['totalFound']
		];
		
		if(!empty($outputFormatParameters['placeholders'])){
			$generalPlaceholders = array_merge($generalPlaceholders, $outputFormatParameters['placeholders']);			
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
		
		if(is_array($dataArray['provider']['items']) && isset($outputFormatParameters['itemTpl'])){
			$maxIndex = $total - 1;
			
			foreach($dataArray['provider']['items'] as $index => $item){
				if(isset($outputFormatParameters['itemTplFirst']) && $index == 0){
					$chunkName = $outputFormatParameters['itemTplFirst'];
				}elseif(isset($outputFormatParameters['itemTplFirst']) && $index == $maxIndex){
					$chunkName = $outputFormatParameters['itemTplLast'];
				}else{
					$chunkName = $outputFormatParameters['itemTpl'];
				}
				
				$document = \ddTools::getTemplateVarOutput('*', $item['id']);
				
				if(!empty($document)){
					$output .= \ddTools::parseSource($modx->parseChunk($chunkName, array_merge(
						$document,
						$generalPlaceholders,
						[
							'itemNumber' => $index + 1,
							'itemNumberZeroBased' => $index
						]
					), '[+', '+]'));
				}
			}
		}
		
		//If no items found and “noResults” is not empty
		if($total == 0 && isset($outputFormatParameters['noResults']) && $outputFormatParameters['noResults'] != ''){
			$chunkContent = $modx->getChunk($outputFormatParameters['noResults']);
			
			if(!is_null($chunkContent)){
				$output = \ddTools::parseSource(
					$modx->parseChunk(
						$outputFormatParameters['noResults'],
						$generalPlaceholders,
						'[+', '+]'
					)
				);
			}else{
				$output = $outputFormatParameters['noResults'];
			}
		}elseif(isset($outputFormatParameters['wrapperTpl'])){
			$output = (string) $modx->parseChunk($outputFormatParameters['wrapperTpl'],
				array_merge($generalPlaceholders, [
					'ddGetDocuments_items' => $output
				]), '[+', '+]');
		}
		
		return $output;
	}
}