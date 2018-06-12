<?php
namespace ddGetDocuments\DataProvider;


class DataProviderOutput
{
	private $items, $totalFound;
	
	public function __construct(array $items, $totalFound = null){
		$this->items = $items;
		$this->totalFound = $totalFound;
	}
	
	public function toArray(){
		return [
			'items' => $this->items,
			'totalFound' => $this->totalFound
		];
	}
}