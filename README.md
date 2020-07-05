# (MODX)EvolutionCMS.snippets.ddGetDocuments

A snippet for fetching and parsing resources from the document tree or custom DB table by a custom rule.


## Requires

* PHP >= 5.4
* MySQL >= 8 or MariaDB >= 10.3.10 (not tested in older versions).
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.40.1


## Documentation


### Installation


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddGetDocuments`.
2. Description: `<b>1.1</b> A snippet for fetching and parsing resources from the document tree or custom DB table by a custom rule.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetDocuments_snippet.php` file from the archive.


#### 2. Elements → Manage Files:

1. Create a new folder `assets/snippets/ddGetDocuments/`.
2. Extract the archive to the folder (except `ddGetDocuments_snippet.php`).


### Parameters description


#### Data provider parameters

* `provider`
	* Desctription: Name of the provider that will be used to fetch documents.
	* Valid values:
		* `'parent'`
		* `'select'`
	* Default value: `'parent'`
	
* `providerParams`
	* Desctription: Parameters to be passed to the provider.
	* Valid values:
		* `stirngJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: —


##### Providers → Parent (``&provider=`parent` ``)

* `providerParams->parentIds`
	* Desctription: Parent ID(s).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: `[0]`
	
* `providerParams->parentIds[i]`
	* Desctription: Document ID.
	* Valid values: `integer`
	* **Required**
	
* `providerParams->depth`
	* Desctription: Depth of children documents search.
	* Valid values: `integer`
	* Default value: `1`
	
* `providerParams->excludeIds`
	* Desctription: The document IDs which need to exclude.
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: —
	
* `providerParams->excludeIds[i]`
	* Desctription: Document ID.
	* Valid values: `integer`
	* **Required**


##### Providers → Select (``&provider=`select` ``)

* `providerParams->ids`
	* Desctription: Document IDs to output.
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* **Required**
	
* `providerParams->ids[i]`
	* Desctription: Document ID.
	* Valid values: `integer`
	* **Required**


##### Providers → Customdbtable (``&provider=`customdbtable` ``)

Get resources from custom DB table.

* `providerParams->resourcesTableName`
	* Desctription: DB table to get resources from.
	* Valid values: `string`
	* **Required**


#### Core parameters

* `fieldDelimiter`
	* Desctription: The field delimiter to be used in order to distinct data base column names in those parameters which can contain SQL queries directly, e. g. `orderBy` and `filter`.
	* Valid values: `string`
	* Default value: ``'`'``
	
* `filter`
	* Desctription: The filter condition in SQL-style to be applied while resource fetching.  
		Notice that all fields/tvs names specified in the filter parameter must be wrapped in `fieldDelimiter`.
	* Valid values: `string`
	* Default value: ``'`published` = 1 AND `deleted` = 0'``
	
* `orderBy`
	* Desctription: A string representing the sorting rule.
	* Valid values: `string`
	* Default value: ``'`id` ASC'``
	
* `total`
	* Desctription: The maximum number of the resources that will be returned.
	* Valid values: `integer`
	* Default value: — (all)
	
* `offset`
	* Desctription:  Resources offset.
	* Valid values: `integer`
	* Default value: `0`


#### Output format parameters

* `outputter`
	* Desctription: Format of the output.
	* Valid values:
		* `'string'`
		* `'json'`
		* `'sitemap'`
		* `'yandexmarket'`
		* `'raw'`
	* Default value: `'string'`
	
* `outputterParams`
	* Desctription: Parameters to be passed to the specified outputter.
	* Valid values:
		* `stirngJsonObject`
		* `stringQueryFormated`
	* Default value: —


##### Outputter → String (``&outputter=`string` ``)

* `outputterParams->itemTpl`
	* Desctription: Item template.  
		Available placeholders:
		* `[+`any document field or tv name`+]` — Any document field name or TV.
		* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
		* `[+`any placeholders from `placeholders` param`+]` — Any custom placeholders (see `outputterParams->placeholders` description below).
		* `[+itemNumber+]` — Item number started from 1.
		* `[+itemNumberZeroBased+]` Item number started from 0.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `outputterParams->itemTplFirst`
	* Desctription: Template for the first item. Has the same placeholders as `outputterParams->itemTpl`.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: == `outputterParams->itemTpl`.
	
* `outputterParams->itemTplLast`
	* Desctription: Template for the last item. Has the same placeholders as `outputterParams->itemTpl`.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: == `outputterParams->itemTpl`.
	
* `outputterParams->wrapperTpl`
	* Desctription: Wrapper template.  
		Available placeholders:
		* `[+`any document field or tv name`+]` — Any document field name or TV.
		* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
		* `[+`any placeholders from `placeholders` param`+]` — Any custom placeholders (see `outputterParams->placeholders` description below).
		* `[+ddGetDocuments_items+]`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —
	
* `outputterParams->placeholders`
	* Desctription:
		Additional data has to be passed into `itemTpl`, `itemTplFirst`, `itemTplLast` and `wrapperTpl`.  
		Arrays are supported too: `some[a]=one&some[b]=two` => `[+some.a+]`, `[+some.b+]`; `some[]=one&some[]=two` => `[+some.0+]`, `[some.1]`.
	* Valid values: `object`
	* Default value: —
	
* `outputterParams->placeholders->{$name}`
	* Desctription: Key for placeholder name and value for placeholder value.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->itemGlue`
	* Desctription: The string that combines items while rendering.
	* Valid values: `string`
	* Default value: `''`
	
* `outputterParams->noResults`
	* Desctription: A chunk or text to output when no items found.  Has the same placeholders as `outputterParams->wrapperTpl`.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —


##### Outputter → Json (``&outputter=`json` ``)

* `outputterParams->docFields`
	* Desctription: Document fields to output (including TVs).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: `'id'`
	
* `outputterParams->docFields[i]`
	* Desctription: Document field or TV.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* **Required**


##### Outputter → Sitemap (``&outputter=`sitemap` ``)

Output in [Sitemap XML format](https://en.wikipedia.org/wiki/Sitemaps).

* `outputterParams->priorityTVName`
	* Desctription: Name of TV which sets the relative priority of the document.
	* Valid values: `stringTvName`
	* Default value: `'general_seo_sitemap_priority'`
	
* `outputterParams->changefreqTVName`
	* Desctription: Name of TV which sets the change frequency.
	* Valid values: `stringTvName`
	* Default value: `'general_seo_sitemap_changefreq'`
	
* `outputterParams->itemTpl`
	* Desctription: Item template.  
		Available placeholders:
		* `[+`any document field or tv name`+]` — Any document field name or TV.
		* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
		* `[+itemNumber+]` — Item number started from 1.
		* `[+itemNumberZeroBased+]` Item number started from 0.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default:
		```xml
		<url>
			<loc>[(site_url)][~[+id+]~]</loc>
			<lastmod>[+editedon+]</lastmod>
			<priority>[+[+priorityTVName+]+]</priority>
			<changefreq>[+[+changefreqTVName+]+]</changefreq>
		</url>
		```
	
* `outputterParams->wrapperTpl`
	* Desctription: Wrapper template.  
		Available placeholders:
		* `[+`any document field or tv name`+]` — Any document field name or TV.
		* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
		* `[+ddGetDocuments_items+]`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```xml
		<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
			[+ddGetDocuments_items+]
		</urlset>
		```


##### Outputter → Yandexmarket (``&outputter=`yandexmarket` ``)

Output in [YML format](https://yandex.ru/support/partnermarket/export/yml.html).

* `outputterParams->shopData_shopName`
	* Desctription: Short shop name, length <= 20.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->shopData_companyName`
	* Desctription: Company legal name. Internal data that not be displayed but required by Yandex.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->shopData_agency`
	* Desctription: 	Web developers agency name.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->shopData_currencyId`
	* Desctription: Currency code (see [Yandex docs](https://yandex.ru/support/partnermarket/currencies.html)).
	* Valid values: `string`
	* Default value: `'RUR'`
	
* `outputterParams->shopData_platform`
	* Desctription: `<platform>` tag content.
	* Valid values: `string`
	* Default value: `'(MODX) Evolution CMS'`
	
* `outputterParams->shopData_version`
	* Desctription: `<version>` tag content.
	* Valid values: `string`
	* Default value: `'[(settings_version)]'`
	
* `outputterParams->categoryIds_last`
	* Desctription: Allows to add additional parent elements in the category section. If empty only immediate parents of goods will be returned.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `outputterParams->categoryIds_last[i]`
	* Desctription: Document ID.
	* Valid values: `integer`
	* **Required**
	
* `outputterParams->offerFields_price`
	* Desctription: A document field name, that contains offer price.
	* Valid values: `stringTvName`
	* **Required**
	
* `outputterParams->offerFields_priceOld`
	* Desctription: A document field name, that contains old offer price (must be less then `outputterParams->offerFields_price` or will not be used).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_picture`
	* Desctription: A document field name, that contains offer picture.
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_name`
	* Desctription: A document field name, that contains offer name.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: `'pagetitle'`
	
* `outputterParams->offerFields_model`
	* Desctription: A document field name, that contains offer model.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_model`
	* Desctription: A document field name, that contains offer vendor.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_available`
	* Desctription: A document field name, that contains offer availability status (`true`|`false`).
	* Valid values:
		* `stringTvName`
		* `''` — always display `'true'`.
	* Default value: `''`
	
* `outputterParams->offerFields_description`
	* Desctription: A document field name, that contains offer description (less than 3 000 chars).
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_salesNotes`
	* Desctription: A document field name, that contains offer <[sales_notes](https://yandex.ru/support/partnermarket/elements/sales_notes.html)>.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_manufacturerWarranty`
	* Desctription: A document field name, that contains offer manufacturer warraynty status (`true`|`false`).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_countryOfOrigin`
	* Desctription: A document field name, that contains offer country of origin.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_homeCourierDelivery`
	* Desctription: A document field name, that contains offer courier delivery status (`true`|`false`).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_dimensions`
	* Desctription: A document field name, that contains offer dimensions (length, width, height) including packaging.  
		Specify dimensions in centimeters. Format: three positive numbers with accuracy of 0.001, using a dot as the decimal separator.  
		The numbers must be separated by the slash character `/` without spaces.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_weight`
	* Desctription: Item weight in kilograms including packaging.  
		Some categories have limits on the minimum or maximum weight.  
		[Download a list of minimum and maximum weight values](https://download.cdn.yandex.net/support/ru/partnermarket/yandex.market-weight.xlsx).  
		In any category, the weight can be specified accurate to one thousandth (for example, 1.001, using a dot as a decimal point).  
		If the minimum value is set to 0, there is no minimum weight limit and it can be specified starting from one gram (0.001 kg).
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_additionalParams`
	* Desctription: A document field name, that contains offer <[param](https://yandex.ru/support/partnermarket/param.html)> elements.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields_customData`
	* Desctription: A document field name, that contains custom text that must be inserted before `</offer>`.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->templates_wrapper`
	* Desctription: Wrapper template.  
		Available placeholders:
		* `[+`any document field or tv name`+]` — Any document field name or TV.
		* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
		* `[+ddGetDocuments_items+]`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```xml
		<?xml version="1.0" encoding="utf-8"?>
			<yml_catalog date="[+generationDate+]">
				<shop>
					<name>[+shopData.shopName+]</name>
					<company>[+shopData.companyName+]</company>
					<url>[(site_url)]</url>
					<platform>[+shopData.platform+]</platform>
					<version>[+shopData.version+]</version>
					[+shopData.agency+]
					<currencies>
						<currency id="[+shopData.currencyId+]" rate="1" />
					</currencies>
					<categories>[+ddGetDocuments_categories+]</categories>
					<offers>[+ddGetDocuments_items+]</offers>
				</shop>
			</yml_catalog>
		```
	
* `outputterParams->templates_categories_item`
	* Desctription: Category item template.
		Available placeholders:
		* `[+id+]` — Category doc ID. 
		* `[+value+]` — Category name.
		* `[+parent+]` — Category parent ID.
		* `[+attrs+]` — Attributes string (e. g. `parentId="42"`).
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```xml
		<category id="[+id+]"[+attrs+]>
			[+value+]
		</category>
		```
	
* `outputterParams->templates_offers_item`
	* Desctription: Offer item template.  
		Available placeholders:
		* `[+`any document field or tv name`+]` — Any document field name or TV.
		* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
		* `[+itemNumber+]` — Item number started from 1.
		* `[+itemNumberZeroBased+]` Item number started from 0.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default:
		```xml
		<offer id="[+id+]" available="[+available+]">
			<url>[(site_url)][~[+id+]~]</url>
			[+name+]
			[+price+]
			[+priceOld+]
			[+shopData.currencyId+]
			[+categoryId+]
			[+picture+]
			[+vendor+]
			[+model+]
			[+description+]
			[+salesNotes+]
			[+manufacturerWarranty+]
			[+countryOfOrigin+]
			[+delivery+]
			[+dimensions+]
			[+weight+]
			[+additionalParams+]
			[+customData+]
		</offer>
		```
	
* `outputterParams->{'templates_offers_item_elem' . $FieldName}`
	* Desctription: You can set custom template for any offer element. Specify an element name in accordance with offerFields_ parameters, e. g. `outputterParams->templates_offers_item_elemCountryOfOrigin`.  
		Available placeholders:
		* `[+tagName+]` — Element tag name.
		* `[+value+]` — Element value.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default: —


#### Extenders parameters
	
* `extenders`
	* Desctription: Comma-separated string determining which extenders should be applied to the snippet.  
		Be aware that the order of extender names can affect the output.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `extenders[i]`
	* Desctription: Extender name.  
		Be aware that the order of extender names can affect the output.
	* Valid values:
		* `'pagination'`
		* `'tagging'`
		* `'search'`
	* **Required**
	
* `extendersParams`
	* Desctription: Parameters to be passed to their corresponding extensions.  
		You can avoid extender name if you are using only one extender (see examples below).
	* Valid values:
		* `stirngJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: —


##### Extenders → Pagination (``&extenders=`pagination` ``)

* `extendersParams->pagination->wrapperTpl`
	* Desctription: Chunk to be used to output the pagination.  
		Available placeholders:
		* `[+previous+]` — HTML code of navigation block to the previous page (see parameters description below).
		* `[+next+]` — HTML code of navigation block to the next page (see parameters description below).
		* `[+pages+]` — HTML code of pages navigalion block (see parameters description below).
		* `[+totalPages+]` — Total number of pages.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<div class="pagination_container">
			<div class="pagination clearfix">
				<div class="pagination_links">[+previous+]</div>
				<div class="pagination_pages">[+pages+]</div>
				<div class="pagination_links">[+next+]</div>
			</div>
		</div>
		```
	
* `extendersParams->pagination->pageTpl`
	* Desctription: Chunk to be used to output pages within the pagination.  
		Available placeholders:
		* `[+url+]` — Page URL.
		* `[+page+]` — Page number.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<a href="[~[*id*]~][+url+]" class="strl">[+page+]</a>
		```
	
* `extendersParams->pagination->currentPageTpl`
	* Desctription: Chunk to be used to output the current page within the pagination.  
		Available placeholders:
		* `[+url+]` — Page URL.
		* `[+page+]` — Page number.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<a href="[~[*id*]~][+url+]" class="strl active">[+page+]</a>
		```
	
* `extendersParams->pagination->nextTpl`
	* Desctription: Chunk to be used to output the navigation block to the next page.  
		Available placeholders:
		* `[+url+]` — Next page URL.
		* `[+totalPages+]` — Total number of pages.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<a href="[~[*id*]~][+url+]" class="pagination_next strl"><span>Следующая</span>&nbsp;→</a><br>
		<small><a href="[~[*id*]~]?page=[+totalPages+]" class="pagination_last strl"><span>Последняя</span>&nbsp;→</a></small>
		```
	
* `extendersParams->pagination->nextOffTpl`
	* Desctription: 	Chunk to be used to output the navigation block to the next page if there are no more pages after.  
		Available placeholders:
		* `[+totalPages+]` — Total number of pages.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<span class="pagination_next"><span>Следующая</span>&nbsp;→</span><br>
		<small><span class="pagination_last"><span>Последняя</span></span>&nbsp;→</small>
		```
	
* `extendersParams->pagination->previousTpl`
	* Desctription: Chunk to be used to output the navigation block to the previous page.  
		Available placeholders:
		* `[+url+]` — Next page URL.
		* `[+totalPages+]` — Total number of pages.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<a href="[~[*id*]~][+url+]" class="pagination_prev strl">←&nbsp;<span>Предыдущая</span></a><br>
		<small><a href="[~[*id*]~]" class="pagination_first strl">←&nbsp;<span>Первая</span></a></small>
		```
	
* `extendersParams->pagination->previousOffTpl`
	* Desctription: Chunk to be used to output the navigation block to the previous page if there are no more pages before.  
		Available placeholders:
		* `[+totalPages+]` — Total number of pages.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<span class="pagination_prev">←&nbsp;<span>Предыдущая</span></span><br>
		<small><span class="pagination_first">←&nbsp;<span>Первая</span></span></small>
		```


##### Extenders → Tagging (``&extenders=`tagging` ``)

* `extendersParams->tagging->tagsDocumentField`
	* Desctription: The document field (TV) contains tags.
	* Valid values: `stringTvName`
	* Default value: `'tags'`
	
* `extendersParams->tagging->tagsDelimiter`
	* Desctription: Tags delimiter in the document field.
	* Valid values: `string`
	* Default value: `', '`
	
* `extendersParams->tagging->tagsRequestParamName`
	* Desctription: The parameter in `$_REQUEST` to get the tags value from.
	* Valid values: `string`
	* Default value: `'tags'`


##### Extenders → Search (``&extenders=`search` ``)

* `extendersParams->search->docFieldsToSearch`
	* Desctription: Document fields to search in (including TVs).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: `'pagetitle,content'`
	
* `extendersParams->search->docFieldsToSearch[i]`
	* Desctription: Document fields or TV.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* **Required**


## [Home page →](https://code.divandesign.biz/modx/ddgetdocuments)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />