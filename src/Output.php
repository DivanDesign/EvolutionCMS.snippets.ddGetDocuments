<?php
namespace ddGetDocuments;


class Output
{
	private
		$provider;
	
	public $extenders = [];
	
	public final function __construct(\ddGetDocuments\DataProvider\Output $providerOutput){
		$this->provider = $providerOutput;
	}
	
	public final function toArray(){
		return [
			'provider' => $this->provider->toArray(),
			'extenders' => $this->extenders
		];
	}
}