<?php
namespace ddGetDocuments;


class Input
{
	public
		$snippetParams,
		$extenderParams,
		$providerParams,
		$outputterParams
	;
	
	public function __construct(
		array $snippetParams = null,
		array $providerParams = null,
		array $extendersParams = null,
		array $outputterParams = null
	){
		$this->snippetParams = $snippetParams;
		$this->providerParams = $providerParams;
		$this->extenderParams = $extendersParams;
		$this->outputterParams = $outputterParams;
	}
}