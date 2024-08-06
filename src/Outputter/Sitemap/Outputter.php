<?php
namespace ddGetDocuments\Outputter\Sitemap;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter {
	protected 
		$priorityTVName = 'general_seo_sitemap_priority',
		$changefreqTVName = 'general_seo_sitemap_changefreq',
		$templates = [
			'item' => '<url><loc>[(site_url)][~[+id+]~]</loc><lastmod>[+editedon+]</lastmod><priority>[+[+priorityTVName+]+]</priority><changefreq>[+[+changefreqTVName+]+]</changefreq></url>',
			'wrapper' => '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>'
		]
	;
	
	private
		$outputter_StringInstance
	;
	
	/**
	 * __construct
	 * @version 2.0.3 (2024-08-06)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->priorityTVName {stringTvName} — Name of TV which sets the relative priority of the document. Default: 'general_seo_sitemap_priority'.
	 * @param $params->changefreqTVName {stringTvName} — Name of TV which sets the change frequency. Default: 'general_seo_sitemap_changefreq'.
	 * @param $params->templates {stdClass|arrayAssociative} — Templates. Default: —.
	 * @param $params->templates->item {string|stringChunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: ''.
	 * @param $params->templates->wrapper {string|stringChunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+]. Default: '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>'.
	 */
	public function __construct($params = []){
		// Call base constructor
		parent::__construct($params);
		
		// Prepare item template
		$this->templates->item = \ddTools::parseText([
			'text' => $this->templates->item,
			'data' => [
				'priorityTVName' => $this->priorityTVName,
				'changefreqTVName' => $this->changefreqTVName
			],
			'isCompletelyParsingEnabled' => false
		]);
		
		// We use the “String” Outputter as base
		$outputter_StringParams = (object) [
			'templates' => $this->templates
		];
		// Transfer provider link
		if (isset($params->dataProvider)){
			$outputter_StringParams->dataProvider = $params->dataProvider;
		}
		$this->outputter_StringInstance = \ddGetDocuments\Outputter\Outputter::createChildInstance([
			'name' => 'String',
			'params' => $outputter_StringParams
		]);
	}
	
	/**
	 * parse
	 * @version 1.1.3 (2024-08-06)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string}
	 */
	public function parse(Output $data){
		foreach (
			$data->provider->items as
			$docIndex =>
			$docData
		){
			// Convert date to appropriate format
			if (isset($data->provider->items[$docIndex]['editedon'])){
				$data->provider->items[$docIndex]['editedon'] = date(
					'Y-m-d',
					$data->provider->items[$docIndex]['editedon']
				);
			}
		}
		
		// Just use the “String” class
		return $this->outputter_StringInstance->parse($data);
	}
}