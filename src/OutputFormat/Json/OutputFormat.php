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
	 * $outputFormatParameters['docFields']. Document fields to output. @Default: 'id'
	 * 
	 * @return json array
	 */
	public function parse(Output $data, array $outputFormatParameters){
		global $modx;
		$output = array();
		$dataArray = $data->toArray();
		
		//Проверим заполнен ли параметр
		$docFields = (isset($outputFormatParameters['docFields']) && $outputFormatParameters['docFields'] != '') ? explode(',', $outputFormatParameters['docFields']) : 'id';
		//Пройдемся по полученным данным
		foreach($dataArray['provider']['items'] as $key => $value){
			//Для каждого найденого id найдем необходимые TV
			$output[] = \ddTools::getTemplateVarOutput($docFields, $value['id']);
		}
		
		//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode || JSON_UNESCAPED_SLASHES — Не экранировать /
		return json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
}