<?php
namespace ddGetDocuments;


class Output
{
	public
		$provider,
		$extenders = []
	;
	
	/**
	 * __construct
	 * @version 1.0.1 (2018-06-12)
	 * 
	 * @param $providerOutput {\ddGetDocuments\DataProvider\DataProviderOutput}
	 */
	public final function __construct(\ddGetDocuments\DataProvider\DataProviderOutput $providerOutput){
		$this->provider = $providerOutput;
	}
	
	public final function toArray(){
		return [
			'provider' => $this->provider->toArray(),
			'extenders' => $this->extenders,
		];
	}
}