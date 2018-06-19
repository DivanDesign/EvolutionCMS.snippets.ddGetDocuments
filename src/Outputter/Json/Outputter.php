<?php
namespace ddGetDocuments\Outputter\Json;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter
{
	/**
	 * parse
	 * @version 2.1 (2018-06-19)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string_json_array}
	 */
	public function parse(Output $data){
		$result = [];
		$dataArray = $data->toArray();
		
		//Пройдемся по полученным данным
		foreach($dataArray['provider']['items'] as $itemIndex => $itemData){
			$result_item = [];
			
			//Result must contains only specified fields
			foreach($this->docFields as $docField){
				$result_item[$docField] = $itemData[$docField];
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