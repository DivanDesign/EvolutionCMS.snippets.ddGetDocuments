<?php
namespace ddGetDocuments\Outputter\Sitemap;


use ddGetDocuments\Output;

class Outputter extends \ddGetDocuments\Outputter\Outputter {
	protected 
		$priorityTVName = 'general_seo_sitemap_priority',
		$changefreqTVName = 'general_seo_sitemap_changefreq',
		$itemTpl = '<url><loc>[(site_url)][~[+id+]~]</loc><lastmod>[+editedon+]</lastmod><priority>[+[+priorityTVName+]+]</priority><changefreq>[+[+changefreqTVName+]+]</changefreq></url>',
		$wrapperTpl = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>'
	;
	
	private
		$outputter_StringInstance
	;
	
	/**
	 * __construct
	 * @version 1.1 (2020-03-10)
	 * 
	 * @param $params {arrayAssociative}
	 * @param $params->priorityTVName {stringTvName} — Name of TV which sets the relative priority of the document. Default: 'general_seo_sitemap_priority'.
	 * @param $params->changefreqTVName {stringTvName} — Name of TV which sets the change frequency. Default: 'general_seo_sitemap_changefreq'.
	 * @param $params->itemTpl {stringChunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: ''.
	 * @param $params->wrapperTpl {stringChunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+]. Default: '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>'.
	 */
	function __construct($params = []){
		//Call base constructor
		parent::__construct($params);
		
		//Prepare item template
		$this->itemTpl = \ddTools::parseText([
			'text' => $this->itemTpl,
			'data' => [
				'priorityTVName' => $this->priorityTVName,
				'changefreqTVName' => $this->changefreqTVName
			],
			'mergeAll' => false
		]);
		
		//We use the “String” Outputter as base
		$outputter_StringClass = \ddGetDocuments\Outputter\Outputter::includeOutputterByName('String');
		$outputter_StringParams = (object) [
			'itemTpl' => $this->itemTpl,
			'wrapperTpl' => $this->wrapperTpl
		];
		//Transfer provider link
		if (isset($params->dataProvider)){
			$outputter_StringParams->dataProvider = $params->dataProvider;
		}
		$this->outputter_StringInstance = new $outputter_StringClass($outputter_StringParams);
	}
	
	/**
	 * parse
	 * @version 1.1.2 (2020-04-30)
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
			//Convert date to appropriate format
			if (isset($data->provider->items[$docIndex]['editedon'])){
				$data->provider->items[$docIndex]['editedon'] = date(
					'Y-m-d',
					$data->provider->items[$docIndex]['editedon']
				);
			}
		}
		
		//Just use the “String” class
		return $this->outputter_StringInstance->parse($data);
	}
}