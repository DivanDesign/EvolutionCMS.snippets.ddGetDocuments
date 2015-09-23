<?php
namespace ddGetDocuments\Extender\Pagination;


use ddGetDocuments\DataProvider\DataProvider;
use ddGetDocuments\Output;

class Extender extends \ddGetDocuments\Extender\Extender
{
	public function apply(DataProvider $dataProvider, array $providerParams, array $extenderParams, array $snippetParams){
		return new Output();
	}
}