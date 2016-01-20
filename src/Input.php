<?php
namespace ddGetDocuments;


class Input
{
	public
		$snippetParams,
		$extenderParams,
		$providerParams,
		$formatParams;
	
	public function __construct(
		array $snippetParams = null,
		array $providerParams = null,
		array $extendersParams = null,
		array $formatParams = null
	){
		$this->snippetParams = $snippetParams;
		$this->providerParams = $providerParams;
		$this->extenderParams = $extendersParams;
		$this->formatParams = $formatParams;
	}
}