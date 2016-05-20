<?php
namespace ddGetDocuments;


class Input
{
	public
		$snippetParams,
		$extenderParams,
		$providerParams,
		$outputFormatParams;
	
	public function __construct(
		array $snippetParams = null,
		array $providerParams = null,
		array $extendersParams = null,
		array $outputFormatParams = null
	){
		$this->snippetParams = $snippetParams;
		$this->providerParams = $providerParams;
		$this->extenderParams = $extendersParams;
		$this->outputFormatParams = $outputFormatParams;
	}
}