<?php
namespace ddGetDocuments;


class Input {
	/**
	 * @property $snippetParams {stdClass}
	 * @property $extendersParams {stdClass}
	 * @property $providerParams {stdClass}
	 * @property $outputterParams {stdClass}
	 */
	public
		$snippetParams,
		$extendersParams,
		$providerParams,
		$outputterParams
	;
	
	/**
	 * __construct
	 * @version 2.0 (2020.03.11)
	 * 
	 * @param $snippetParams {stdClass|arrayAssociative} — @required
	 * @param $providerParams {stdClass|arrayAssociative} — @required
	 * @param $extendersParams {stdClass|arrayAssociative} — @required
	 * @param $outputterParams {stdClass|arrayAssociative} — @required
	 */
	public function __construct(
		array $snippetParams,
		array $providerParams,
		array $extendersParams,
		array $outputterParams
	){
		$this->snippetParams = (object) $snippetParams;
		$this->providerParams = (object) $providerParams;
		$this->extendersParams = (object) $extendersParams;
		$this->outputterParams = (object) $outputterParams;
	}
}