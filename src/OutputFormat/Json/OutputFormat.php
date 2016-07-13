<?php
namespace ddGetDocuments\OutputFormat\Json;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	/**
	 * parse
	 * 
	 * @param Output $data
	 * @param array $outputFormatParameters
	 * @param array|string_commaSeparated $outputFormatParameters['docFields'] — Document fields to output. Default: 'id'.
	 * 
	 * @return string_json_array
	 */
	public function parse(Output $data, array $outputFormatParameters){
		$output = array();
		$dataArray = $data->toArray();
		
		//Проверим заполнен ли параметр
		$docFields = isset($outputFormatParameters['docFields']) && $outputFormatParameters['docFields'] != '' ? $outputFormatParameters['docFields'] : array('id');
		
		//Comma separated strings
		if (!is_array($docFields)){
			$docFields = explode(',', $docFields);
		}
		
		//Пройдемся по полученным данным
		foreach($dataArray['provider']['items'] as $key => $value){
			//Для каждого найденого id найдем необходимые TV
			$output[] = \ddTools::getTemplateVarOutput($docFields, $value['id']);
		}
		
		//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode || JSON_UNESCAPED_SLASHES — Не экранировать /
		return json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
}