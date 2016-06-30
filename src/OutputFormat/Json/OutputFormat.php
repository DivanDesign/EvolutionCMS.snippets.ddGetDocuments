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
	 * $outputFormatParameters['TVtoJson']. TV необходимые для json массива. @Default: 'id'
	 * $outputFormatParameters['wrapResult']. Оборачивает результат в качестве разделителя используется «||». @Default: ''
	 * 
	 * @return string
	 */
	public function parse(Output $data, array $outputFormatParameters){
		global $modx;
		$output = array();
		$dataArray = $data->toArray();
		
		$TVtoJson = (isset($outputFormatParameters['TVtoJson']) && $outputFormatParameters['TVtoJson'] != '') ? explode(',',$outputFormatParameters['TVtoJson']) : 'id';
		
		foreach($dataArray['provider']['items'] as $key => $value){
			
			$document = \ddTools::getTemplateVarOutput($TVtoJson, $value['id']);
			
			$output[] .= json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			$result = implode(",", $output);
		}
		
		if (isset($outputFormatParameters['wrapResult']) && $outputFormatParameters['wrapResult'] != ''){
			$wrap = explode('||',$outputFormatParameters['wrapResult']);
			return $wrap[0] .$result. $wrap[1];
		}else{
			return $result;
		};
	}
}