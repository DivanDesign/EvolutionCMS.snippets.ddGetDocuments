<?php
namespace ddGetDocuments\Outputter\Json;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter {
	/**
	 * parse
	 * @version 2.5.1 (2024-10-06)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {stringJsonArray}
	 */
	public function parse(Output $data){
		$result = [];
		
		$isFieldAliasesUsed = !\ddTools::isEmpty($this->fieldAliases);
		
		// Пройдемся по полученным данным
		foreach(
			$data->provider->items
			as $itemIndex
			=> $itemData
		){
			$result_item = [];
			
			// Result must contains only specified fields
			foreach(
				$this->docFields
				as $docField
			){
				// If aliases are used
				if (
					$isFieldAliasesUsed
					&& \DDTools\Tools\Objects::isPropExists([
						'object' => $this->fieldAliases,
						'propName' => $docField,
					])
				){
					$result_item[$this->fieldAliases->{$docField}] = $itemData[$docField];
					
					$docField = $this->fieldAliases->{$docField};
				}else{
					$result_item[$docField] = $itemData[$docField];
				}
				
				// If template for this field is set
				if (
					\DDTools\ObjectTools::isPropExists([
						'object' => $this->templates,
						'propName' => $docField,
					])
				){
					$result_item[$docField] =
						\ddTools::parseSource(
							\ddTools::parseText([
								'text' => $this->templates->{$docField},
								'data' => \DDTools\ObjectTools::extend([
									'objects' => [
										$itemData,
										[
											'value' => $result_item[$docField],
											'itemNumber' => $itemIndex + 1,
											'itemNumberZeroBased' => $itemIndex,
										]
									],
								]),
							])
						)
					;
				}
			}
			
			$result[] = $result_item;
		}
		
		return \DDTools\Tools\Objects::convertType([
			'object' => $result,
			'type' => 'stringJsonAuto',
		]);
	}
}