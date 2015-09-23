<?php
namespace ddGetDocuments;


class Output
{
	private $items, $data;
	
	public final function __construct(array $items = array(), array $data = array()){
		$this->items = $items;
		$this->data = $data;
	}
	
	public final function toArray(){
		return array(
			'items' => $this->items,
			'data' => $this->data
		);
	}
}