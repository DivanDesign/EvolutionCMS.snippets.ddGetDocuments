<?php
namespace ddGetDocuments\DataProvider;


class Output
{
	private $items, $totalFound;
	
	public function __construct(array $items, $totalFound = null){
		$this->items = $items;
		$this->totalFound = $totalFound;
	}
	
	public function toArray(){
		return array(
			'items' => $this->items,
			'totalFound' => $this->totalFound
		);
	}
}