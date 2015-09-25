<?php
namespace ddGetDocuments;


class Output
{
	private $provider, $data;
	
	public final function __construct(\ddGetDocuments\DataProvider\Output $providerOutput, array $data = array()){
		$this->provider = $providerOutput;
		$this->data = $data;
	}
	
	public final function toArray(){
		return array(
			'provider' => $this->provider->toArray(),
			'data' => $this->data
		);
	}
}