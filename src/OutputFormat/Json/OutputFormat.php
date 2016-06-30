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
	 * $outputFormatParameters['TVtoJson']. TV из которых будет состоять json. @Default: 'id'
	 * $outputFormatParameters['wrapResult']. Оборачивает результат. В качестве разделителя используется «||». @Default: ''
	 * 
	 * @return json array
	 */
	public function parse(Output $data, array $outputFormatParameters){
		global $modx;
		$output = array();
		$dataArray = $data->toArray();
		//Проверим заполнен ли параметр
		$TVtoJson = (isset($outputFormatParameters['TVtoJson']) && $outputFormatParameters['TVtoJson'] != '') ? explode(',',$outputFormatParameters['TVtoJson']) : 'id';
		//Пройдемся по полученным данным
		foreach($dataArray['provider']['items'] as $key => $value){
			//Для каждого найденого id найдем необходимые TV
			$document = \ddTools::getTemplateVarOutput($TVtoJson, $value['id']);
			//JSON_UNESCAPED_UNICODE — Не кодировать многобайтные символы Unicode || JSON_UNESCAPED_SLASHES — Не экранировать /
			$output[] .= json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			//Добавим запятую после каждого элемента
			$result = implode(",", $output);
		}
		
		//Обернем если надо
		if (isset($outputFormatParameters['wrapResult']) && $outputFormatParameters['wrapResult'] != ''){
			$wrap = explode('||',$outputFormatParameters['wrapResult']);
			return $wrap[0] .$result. $wrap[1];
		}else{
			//Не обернем если не надо
			return $result;
		};
	}
}