<?php
namespace ddGetDocuments\OutputFormat\Json;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	/**
	 * parse
	 * @version 1.0.2 (2018-06-09)
	 * 
	 * @param $data {Output}
	 * @param $outputFormatParameters {array}
	 * @param $outputFormatParameters['docFields'] {array|string_commaSeparated} — Document fields to output. Default: 'id'.
	 * 
	 * @return {string_json_array}
	 */
	public function parse(
		Output $data,
		array $outputFormatParameters
	){
		$result = [];
		$dataArray = $data->toArray();
		
		//Проверим заполнен ли параметр
		$docFields = isset($outputFormatParameters['docFields']) && $outputFormatParameters['docFields'] != '' ? $outputFormatParameters['docFields'] : ['id'];
		
		//Comma separated strings
		if (!is_array($docFields)){
			$docFields = explode(
				',',
				$docFields
			);
		}
		
		//Пройдемся по полученным данным
		foreach($dataArray['provider']['items'] as $key => $value){
			//Для каждого найденого id найдем необходимые TV
			$result[] = \ddTools::getTemplateVarOutput(
				$docFields,
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