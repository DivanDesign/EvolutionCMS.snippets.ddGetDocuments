<?php
namespace ddGetDocuments\Outputter\Json;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter {
	/**
	 * parse
	 * @version 2.2 (2021-07-12)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {stringJsonArray}
	 */
	public function parse(Output $data){
		$result = [];
		
		//Пройдемся по полученным данным
		foreach(
			$data->provider->items as
			$itemData
		){
			$result_item = [];
			
			//Result must contains only specified fields
			foreach(
				$this->docFields as
				$docField
			){
				$result_item[$docField] = $itemData[$docField];
				
				//If template for this field is set
				if (
					\DDTools\ObjectTools::isPropExists([
						'object' => $this->templates,
						'propName' => $docField
					])
				){
					$result_item[$docField] = \ddTools::parseSource(\ddTools::parseText([
						'text' => $this->templates->{$docField},
						'data' => [
							'value' => $result_item[$docField]
						]
					]));
				}
			}
			
			$result[] = $result_item;
		}
		
		//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode || JSON_UNESCAPED_SLASHES — Не экранировать /
		return json_encode(
			$result,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
	}
}