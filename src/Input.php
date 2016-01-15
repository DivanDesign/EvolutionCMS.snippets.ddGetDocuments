<?php
namespace ddGetDocuments;


class Input
{
	public
		$snippetParams,
		$providerParams;
	
	public function __construct(array $snippetParams = null, array $providerParams = null){
		$this->snippetParams = $snippetParams;
		$this->providerParams = $providerParams;
	}
}