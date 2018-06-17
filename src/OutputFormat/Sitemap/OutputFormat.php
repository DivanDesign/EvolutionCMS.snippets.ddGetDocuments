<?php
namespace ddGetDocuments\OutputFormat\Sitemap;


use ddGetDocuments\Output;

class OutputFormat extends \ddGetDocuments\OutputFormat\OutputFormat
{
	protected 
		$priorityTVName = 'general_seo_sitemap_priority',
		$changefreqTVName = 'general_seo_sitemap_changefreq',
		$itemTpl = '<url><loc>[(site_url)][~[+id+]~]</loc><lastmod>[[ddGetDate?	&date=`[+editedon+]` &format=`Y-m-d`]]</lastmod><priority>[+[+priorityTVName+]+]</priority><changefreq>[+[+changefreqTVName+]+]</changefreq></url>',
		$wrapperTpl = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>';
	
	private
		$outputFormat_StringInstance;
	
	/**
	 * __construct
	 * @version 1.0 (2018-06-12)
	 * 
	 * @param $params {array_associative}
	 * @param $params['priorityTVName'] {string_TVName} — Name of TV which sets the relative priority of the document. Default: 'general_seo_sitemap_priority'.
	 * @param $params['changefreqTVName'] {string_TVName} — Name of TV which sets the change frequency. Default: 'general_seo_sitemap_changefreq'.
	 * @param $params['itemTpl'] {string_chunkName} — Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: ''.
	 * @param $params['wrapperTpl'] {string_chunkName} — Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+]. Default: '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>'.
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
		
		//We use the “String” OutputFormat as base
		$OutputFormat_StringClass = \ddGetDocuments\OutputFormat\OutputFormat::includeOutputFormatByName('String');
		$this->outputFormat_StringInstance = new $OutputFormat_StringClass([
			'itemTpl' => $this->itemTpl,
			'wrapperTpl' => $this->wrapperTpl
		]);
	}
	
	/**
	 * parse
	 * @version 1.0 (2018-06-12)
	 * 
	 * @param $data {Output}
	 * 
	 * @return {string}
	 */
	public function parse(Output $data){
		//TODO: Вывести дату в правильном формате без сниппета «ddGetDate»?
		
		//Just use the “String” class
		return $this->outputFormat_StringInstance->parse($data);
	}
}