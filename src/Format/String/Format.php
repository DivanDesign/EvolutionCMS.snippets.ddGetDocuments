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
	 * $parserParameters['itemTpl']. Available placeholders: [+any field or tv name+]. @required
	 * $parserParameters['itemTplFirst']. Available placeholders: [+any field or tv name+].
	 * $parserParameters['itemTplLast']. Available placeholders: [+any field or tv name+].
	 * $parserParameters['wrapperTpl']. Available placeholders: [+ddGetDocuments_items+].
	 * 
	 * @return string
	 */
	public function parse(Output $data, array $formatParameters){
		global $modx;
		$output = '';
		$dataArray = $data->toArray();
		
		$total = count($dataArray['items']);
		$generalPlaceholders = array(
			'total' => $total
		);
		
		if(is_array($dataArray['items']) && isset($formatParameters['itemTpl'])){
			$maxIndex = $total - 1;
			
			foreach($dataArray['items'] as $index => $item){
				if(isset($formatParameters['itemTplFirst']) && $index == 0){
					$chunkName = $formatParameters['itemTplFirst'];
				}elseif(isset($formatParameters['itemTplFirst']) && $index == $maxIndex){
					$chunkName = $formatParameters['itemTplLast'];
				}else{
					$chunkName = $formatParameters['itemTpl'];
				}
				
				$document = \ddTools::getTemplateVarOutput('*', $item['id']);
				
				if(!empty($document)){
					$output .= \ddTools::parseSource($modx->parseChunk($chunkName, array_merge($document, $generalPlaceholders), '[+', '+]'));
				}
			}
		}
		
		if(isset($formatParameters['wrapperTpl'])){
			$output = (string) $modx->parseChunk($formatParameters['wrapperTpl'],
				array_merge($generalPlaceholders, array(
					'ddGetDocuments_items' => $output
				)), '[+', '+]');
		}
		
		return $output;
	}
}