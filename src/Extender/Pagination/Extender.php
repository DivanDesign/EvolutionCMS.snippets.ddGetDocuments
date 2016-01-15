<?php
namespace ddGetDocuments\Extender\Pagination;


use ddGetDocuments\DataProvider\Output;
use ddGetDocuments\Input;

class Extender extends \ddGetDocuments\Extender\Extender
{
	public function applyToInput(Input $input)
	{
		return $input;
	}
	
	public function applyToOutput(Output $output)
	{
		return 'test';
	}
}