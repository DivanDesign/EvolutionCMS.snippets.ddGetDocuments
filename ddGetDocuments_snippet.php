<?php
/**
 * ddGetDocuments
 * @version 0.8.1 (2017-11-17)
 * 
 * @desc A snippet for fetching and parsing resources from the document tree by a custom rule.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo >= 1.1.
 * @uses MODXEvo.libraries.ddTools >= 0.21.
 * 
 * @param $provider {'parent'|'select'} — Name of the provider that will be used to fetch documents. Default: 'parent'.
 * @param $providerParams {stirng_json|string_queryFormated} — Parameters to be passed to the provider. The parameter must be set as a query string,
 * When $provider == 'parent' =>
 * @param $providerParams['parentIds'] {array|string_commaSepareted} — Parent IDs. Default: 0.
 * @param $providerParams['depth'] {integer} — Depth of children documents search. Default: 1.
 * @example &providerParams=`{"parentIds": 1, "depth": 2}`
 * @example &providerParams=`parentIds=1&depth=2`
 * When $provider == 'select' =>
 * @param $providerParams['ids'] {string_commaSepareted} — Document IDs to output. @required
 * @example &providerParams=`{"ids": "1,2,3"}`
 * 
 * @param $fieldDelimiter {string} — The field delimiter to be used in order to distinct data base column names in those parameters which can contain SQL queries directly, e. g. $orderBy and $filter. Default: '`'.
 * @param $total {integer} — The maximum number of the resources that will be returned. Default: —.
 * @param $filter {string} — The filter condition in SQL-style to be applied while resource fetching. Default: '`published` = 1 AND `deleted` = 0';
 * Notice that all fields/tvs names specified in the filter parameter must be wrapped in $fieldDelimiter.
 * @param $offset {integer} — Resources offset. Default: 0.
 * @param $orderBy {string} — A string representing the sorting rule. Default: '`id` ASC'.
 * 
 * @param $outputter {'string'|'json'|'sitemap'|'raw'} — Format of the output. Default: 'string'.
 * @param $outputterParams {stirng_json|string_queryFormated} — Parameters to be passed to the specified formatter. The parameter must be set as a query string,
 * When $outputter == 'string' =>
 * @param $outputterParams['itemTpl'] {string_chunkName|string} — Item template (chunk name or code via “@CODE:” prefix). Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. @required
 * @param $outputterParams['itemTplFirst'] {string_chunkName|string} — First item template (chunk name or code via “@CODE:” prefix). Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
 * @param $outputterParams['itemTplLast'] {string_chunkName|string} — Last item template (chunk name or code via “@CODE:” prefix). Available placeholders: [+any field or tv name+], [+any of extender placeholders+].
 * @param $outputterParams['wrapperTpl'] {string_chunkName|string} — Wrapper template (chunk name or code via “@CODE:” prefix). Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+], [+any placeholders from “placeholders” param+].
 * @param $outputterParams['placeholders'] {array_associative} — Additional data has to be passed into “itemTpl”, “itemTplFirst”, “itemTplLast” and “wrapperTpl”. Е.g. 'placeholders[alias]=test&placeholders[pagetitle]=Some title'. Default: []. 
 * @param $outputterParams['placeholders'][name] {string} — Key for placeholder name and value for placeholder value. @required 
 * @param $outputterParams['itemGlue'] {string} — The string that combines items while rendering. Default: ''.
 * @param $outputterParams['noResults'] {string|string_chunkName|string} — A chunk or text to output when no items found (chunk name or code via “@CODE:” prefix). Available placeholders: [+any of extender placeholders+]. 
 * @example &outputterParams=`{"itemTpl": "chunk_1", "wrapperTpl": "chunk_2", "noResults": "No items found"}`
 * @example &outputterParams=`itemTpl=chunk_1&wrapperTpl=chunk_2&noResults=No items found`
 * When $outputter == 'json' =>
 * @param $outputterParams['docFields'] {array|string_commaSeparated} — Document fields to output (including TVs). Default: 'id'.
 * @example &outputterParams=`{"docFields": "id,pagetitle,introtext"}`
 * @example &outputterParams=`docFields=id,pagetitle,introtext`
 * When $outputter == 'sitemap' =>
 * @param $outputterParams['priorityTVName'] {string_TVName} — Name of TV which sets the relative priority of the document. Default: 'general_seo_sitemap_priority'.
 * @param $outputterParams['changefreqTVName'] {string_TVName} — Name of TV which sets the change frequency. Default: 'general_seo_sitemap_changefreq'.
 * @param $outputterParams['itemTpl'] {string_chunkName|string} — Item template (chunk name or code via “@CODE:” prefix). Available placeholders: [+any field or tv name+], [+any of extender placeholders+]. Default: '<url><loc>[(site_url)][~[+id+]~]</loc><lastmod>[[ddGetDate?	&date=`[+editedon+]` &format=`Y-m-d`]]</lastmod><priority>[+[+priorityTVName+]+]</priority><changefreq>[+[+changefreqTVName+]+]</changefreq></url>'.
 * @param $outputterParams['wrapperTpl'] {string_chunkName|string} — Wrapper template (chunk name or code via “@CODE:” prefix). Available placeholders: [+ddGetDocuments_items+], [+any of extender placeholders+], [+any placeholders from “placeholders” param+]. Default: '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">[+ddGetDocuments_items+]</urlset>'.
 * @example &outputterParams=`{"priorityTVName": "general_seo_sitemap_priority", "changefreqTVName": "general_seo_sitemap_changefreq"}`
 * 
 * @param $extenders {string_commaSeparated} — Comma-separated string determining which extenders should be applied to the snippet.
 * Be aware that the order of extender names can affect the output.
 * @param $extendersParams {stirng_json|string_queryFormated} — Parameters to be passed to their corresponding extensions. The parameter must be set as a query string,
 * When $extenders == 'pagination' =>
 * @param $extendersParams['wrapperTpl'] {string_chunkName|string} — Chunk to be used to output the pagination (chunk name or code via “@CODE:” prefix). @required
 * @param $extendersParams['pageTpl'] {string_chunkName|string} — Chunk to be used to output pages within the pagination (chunk name or code via “@CODE:” prefix). @required
 * @param $extendersParams['currentPageTpl'] {string_chunkName|string} — Chunk to be used to output the current page within the pagination (chunk name or code via “@CODE:” prefix). @required
 * @param $extendersParams['nextTpl'] {string_chunkName|string} — Chunk to be used to output the navigation block to the next page (chunk name or code via “@CODE:” prefix). Available placeholders: [+url+], [+totalPages+]. @required
 * @param $extendersParams['nextOffTpl'] {string_chunkName|string} — Chunk to be used to output the navigation block to the next page if there are no more pages after. Available placeholders: [+url+], [+totalPages+]. @required
 * @param $extendersParams['previousTpl'] {string_chunkName|string} — Chunk to be used to output the navigation block to the previous page (chunk name or code via “@CODE:” prefix). Available placeholders: [+url+], [+totalPages+]. @required
 * @param $extendersParams['previousOffTpl'] {string_chunkName|string} — Chunk to be used to output the navigation block to the previous page if there are no more pages before (chunk name or code via “@CODE:” prefix). Available placeholders: [+url+], [+totalPages+]. @required
 * When $extenders == 'tagging' =>
 * @param $extendersParams['tagsDocumentField'] {string_tvName} — The document field (TV) contains tags. Default: 'tags'.
 * @param $extendersParams['tagsDelimiter'] {string_tvName} — Tags delimiter in the document field. Default: ', '.
 * @param $extendersParams['tagsRequestParamName'] {string} — The parameter in $_REQUEST to get the tags value from. Default: 'tags'.
 * When $extenders == 'search' =>
 * @param $extendersParams['docFieldsToSearch'] {string_commaSepareted} — Document fields to search in. Default: 'pagetitle,content'.
 * @example &extendersParams=`{"pagination": {"wrapperTpl":"pagination", …}, "tagging": {"tagsDocumentField": "general_tags"}}`
 * 
 * @copyright 2015–2017 DivanDesign {@link http://www.DivanDesign.biz }
 **/

global $modx;
$result = false;

if(!class_exists('\ddTools')){
	require_once($modx->config['base_path'].'assets/libs/ddTools/modx.ddtools.class.php');
}

if(!class_exists('\ddGetDocuments\DataProvider\DataProvider')){
	require_once($modx->config['base_path'].'assets/snippets/ddGetDocuments/require.php');
}

//Backward compatibility
extract(ddTools::verifyRenamedParams(
	$params,
	[
		'outputter' => 'outputFormat',
		'outputterParams' => 'outputFormatParams'
	]
));

//General
$total = isset($total) ? $total : null;
$offset = isset($offset) ? $offset : 0;
$orderBy = isset($orderBy) ? $orderBy : '`id` ASC';
$filter = isset($filter) ? $filter : null;
$fieldDelimiter = isset($fieldDelimiter) ? $fieldDelimiter : '`';

//Data provider
$provider = isset($provider) ? $provider : 'parent';
$providerClass = \ddGetDocuments\DataProvider\DataProvider::includeProviderByName($provider);
$providerParams = isset($providerParams) ? $providerParams : '';

//Output format
$outputter = isset($outputter) ? strtolower($outputter) : 'string';
$outputterParams = isset($outputterParams) ? $outputterParams : '';

//Extenders
$extenders = isset($extenders) ? explode(
	',',
	trim($extenders)
) : [];
$extendersParams = isset($extendersParams) ? $extendersParams : '';

if(class_exists($providerClass)){
	//Prepare provider params
	$providerParams = ddTools::encodedStringToArray($providerParams);
	//Prepare extender params
	$extendersParams = ddTools::encodedStringToArray($extendersParams);
	//Prepare output format params
	$outputterParams = ddTools::encodedStringToArray($outputterParams);
	
	if(!empty($extenders)){
		//If we have a single extender then make sure that extender params set as an array
		//like [extenderName => [extenderParameter_1, extenderParameter_2, ...]]
		if(count($extenders) === 1){
			if(!isset($extendersParams[$extenders[0]])){
				$extendersParams = [
					$extenders[0] => $extendersParams
				];
			}
		}else{
			//Make sure that for each extender there is an item in $extendersParams 
			foreach($extenders as $extenderName){
				if(!isset($extendersParams[$extenderName])){
					$extendersParams[$extenderName] = [];
				}
			}
		}
	}
	
	//Make sure orderBy and filter looks like SQL
	$orderBy = str_replace(
		$fieldDelimiter,
		'`',
		$orderBy
	);
	$filter = str_replace(
		$fieldDelimiter,
		'`',
		$filter
	);
	
	$input = new \ddGetDocuments\Input(
		//Snippet params
		[
			'offset' => $offset,
			'total' => $total,
			'orderBy' => $orderBy,
			'filter' => $filter
		],
		$providerParams,
		$extendersParams,
		$outputterParams
	);
	
	//Extenders storage
	$extendersStorage = [];
	
	//Iterate through all extenders to create their instances
	foreach($extenders as $extenderName){
		$extenderClass = \ddGetDocuments\Extender\Extender::includeExtenderByName($extenderName);
		//Passing parameters to extender's constructor
		$extender = new $extenderClass($extendersParams[$extenderName]);
		//Passing a link to the storage
		$extendersStorage[$extenderName] = $extender;
		
		//Overwrite the snippet parameters with the result of applying them to the current extender
		$input->snippetParams = $extender->applyToSnippetParams($input->snippetParams);
	}
	
	$dataProvider = new $providerClass($input);
	$providerResult = $dataProvider->get();
	
	$data = new \ddGetDocuments\Output($providerResult);
	
	//Iterate through all extenders again to apply them to the output
	foreach($extendersStorage as $extenderName => $extender){
		$data->extenders[$extenderName] = $extender->applyToOutput($providerResult);
	}
	
	switch($outputter){
		default:
			$outputterClass = \ddGetDocuments\Outputter\Outputter::includeOutputterByName($outputter);
			$outputterObject = new $outputterClass($outputterParams);
			
			$result = $outputterObject->parse($data);
		break;
		
		case 'raw':
			$result = $data;
		break;
	}
}

return $result;
?>