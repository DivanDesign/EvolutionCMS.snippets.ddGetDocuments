<?php
namespace ddGetDocuments\DataProvider;


class DataProviderOutput
{
	private
		$items,
		$totalFound;
	
	/**
	 * __construct
	 * @version 1.0.1 (2018-06-12)
	 * 
	 * @param $items {array}
	 * @param $totalFound {integer|null}
	 */
	public function __construct(
		array $items,
		$totalFound = null
	){
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