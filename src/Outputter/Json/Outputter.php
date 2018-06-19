<?php
namespace ddGetDocuments\Outputter\Json;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter
{
	/**
	 * parse
	 * @version 2.0 (2018-06-12)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string_json_array}
	 */
	public function parse(Output $data){
		$result = [];
		$dataArray = $data->toArray();
		
		//Пройдемся по полученным данным
		foreach($dataArray['provider']['items'] as $key => $value){
			//Для каждого найденого id найдем необходимые TV
			$result[] = \ddTools::getTemplateVarOutput(
				$this->docFields,
				$value['id']
			);
		}
		
		//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode || JSON_UNESCAPED_SLASHES — Не экранировать /
		return json_encode(
			$result,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
	}
}