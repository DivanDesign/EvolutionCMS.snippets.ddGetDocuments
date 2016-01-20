<?php
namespace ddGetDocuments;


class Output
{
	private
		$provider;
	
	public $extenders = array();
	
	public final function __construct(\ddGetDocuments\DataProvider\Output $providerOutput){
		$this->provider = $providerOutput;
	}
	
	public final function toArray(){
		return array(
			'provider' => $this->provider->toArray(),
			'extenders' => $this->extenders
		);
	}
}