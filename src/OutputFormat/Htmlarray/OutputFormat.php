<?php
namespace ddGetDocuments\OutputFormat\Htmlarray;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	/**
	 * parse
	 * 
	 * @param Output $data
	 * @param array $outputFormatParameters
	 * $outputFormatParameters['itemTpl']. Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
	 * $outputFormatParameters['itemTplFirst']. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * $outputFormatParameters['itemTplLast']. Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
	 * $outputFormatParameters['resultToPlaceholder']. The name of the global MODX placeholder that holds the snippet result. The result will be returned in a regular manner if the parameter is empty. 
	 * 
	 * @return string
	 */
	public function parse(Output $data, array $outputFormatParameters){
		global $modx;
		$output = array();
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
					$output[] = \ddTools::parseSource($modx->parseChunk($chunkName, array_merge(
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
		
		//Если надо, выводим в плэйсхолдер
		if (isset($outputFormatParameters['resultToPlaceholder'])){
			$modx->setPlaceholder($outputFormatParameters['resultToPlaceholder'], $output);
			
			$output = '';
		}
		
		return $output;
	}
}