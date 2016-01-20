<?php
namespace ddGetDocuments\Format\String;


use ddGetDocuments\Output;

class Format extends \ddGetDocuments\Format\Format
{
	/**
	 * parse
	 * 
	 * @param Output $data
	 * @param array $formatParameters
	 * $formatParameters['itemTpl']. Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * $formatParameters['itemTplFirst']. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * $formatParameters['itemTplLast']. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * $formatParameters['wrapperTpl']. Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+].
	 * $formatParameters['noResults']. A chunk or text to output when no items found. Available placeholders: [+any of extender placeholders+]. 
	 * 
	 * @return string
	 */
	public function parse(Output $data, array $formatParameters){
		global $modx;
		$output = '';
		$dataArray = $data->toArray();
		
		$total = count($dataArray['provider']['items']);
		$generalPlaceholders = array(
			'total' => $total,
			'totalFound' => $dataArray['provider']['totalFound']
		);
		
		if(isset($dataArray['extenders'])){
			$generalPlaceholders = array_merge(
				$generalPlaceholders,
				array(
					'extenders' => $dataArray['extenders']
				)
			);
			
			$generalPlaceholders = \ddTools::unfoldArray($generalPlaceholders);
		}
		
		if(is_array($dataArray['provider']['items']) && isset($formatParameters['itemTpl'])){
			$maxIndex = $total - 1;
			
			foreach($dataArray['provider']['items'] as $index => $item){
				if(isset($formatParameters['itemTplFirst']) && $index == 0){
					$chunkName = $formatParameters['itemTplFirst'];
				}elseif(isset($formatParameters['itemTplFirst']) && $index == $maxIndex){
					$chunkName = $formatParameters['itemTplLast'];
				}else{
					$chunkName = $formatParameters['itemTpl'];
				}
				
				$document = \ddTools::getTemplateVarOutput('*', $item['id']);
				
				if(!empty($document)){
					$output .= \ddTools::parseSource($modx->parseChunk($chunkName, array_merge(
						$document,
						$generalPlaceholders,
						array(
							'itemNumber' => $index + 1,
							'itemNumberZeroBased' => $index
						)
					), '[+', '+]'));
				}
			}
		}
		
		//If no items found and “noResults” is not empty
		if($total == 0 && isset($formatParameters['noResults']) && $formatParameters['noResults'] != ''){
			$chunkContent = $modx->getChunk($formatParameters['noResults']);
			
			if(!is_null($chunkContent)){
				$output = \ddTools::parseSource(
					$modx->parseChunk(
						$formatParameters['noResults'],
						$generalPlaceholders,
						'[+', '+]'
					)
				);
			}else{
				$output = $formatParameters['noResults'];
			}
		}elseif(isset($formatParameters['wrapperTpl'])){
			$output = (string) $modx->parseChunk($formatParameters['wrapperTpl'],
				array_merge($generalPlaceholders, array(
					'ddGetDocuments_items' => $output
				)), '[+', '+]');
		}
		
		return $output;
	}
}