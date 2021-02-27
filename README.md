# (MODX)EvolutionCMS.snippets.ddGetDocuments

A snippet for fetching and parsing resources from the document tree or custom DB table by a custom rule.


## Requires

* PHP >= 5.4
* MySQL >= 8 or MariaDB >= 10.3.10 (not tested in older versions).
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.42


## Documentation


### Installation


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddGetDocuments`.
2. Description: `<b>1.2</b> A snippet for fetching and parsing resources from the document tree or custom DB table by a custom rule.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetDocuments_snippet.php` file from the archive.


#### 2. Elements → Manage Files:

1. Create a new folder `assets/snippets/ddGetDocuments/`.
2. Extract the archive to the folder (except `ddGetDocuments_snippet.php`).


### Parameters description


#### Core parameters

* `fieldDelimiter`
	* Desctription: The field delimiter to be used in order to distinct data base column names in those parameters which can contain SQL queries directly, e. g. `providerParams->orderBy` and `providerParams->filter`.
	* Valid values: `string`
	* Default value: ``'`'``


#### Data provider parameters

* `provider`
	* Desctription: Name of the provider that will be used to fetch documents.  
		Data provider names are case insensitive (the following names are equal: `parent`, `Parent`, `pArEnT`, etc).
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
	
* `providerParams->filter`
	* Desctription: The filter condition in SQL-style to be applied while resource fetching.  
		Notice that all fields/tvs names specified in the filter parameter must be wrapped in `fieldDelimiter`.
	* Valid values: `string`
	* Default value: ``'`published` = 1 AND `deleted` = 0'``
	
* `providerParams->total`
	* Desctription: The maximum number of the resources that will be returned.
	* Valid values: `integer`
	* Default value: — (all)
	
* `providerParams->offset`
	* Desctription: Resources offset.
	* Valid values: `integer`
	* Default value: `0`
	
* `providerParams->orderBy`
	* Desctription: A string representing the sorting rule.  
		TV names also can be used.
	* Valid values: `string`
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


#### Output format parameters

* `outputter`
	* Desctription: Format of the output.  
		Outputter names are case insensitive (the following names are equal: `string`, `String`, `sTrInG`, etc).
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

* `outputterParams->shopData`
	* Desctription: Shop data.
	* Valid values: `object`
	* **Required**
	
* `outputterParams->shopData->shopName`
	* Desctription: Short shop name, length <= 20.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->shopData->companyName`
	* Desctription: Company legal name. Internal data that not be displayed but required by Yandex.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->shopData->agency`
	* Desctription: 	Web developers agency name.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->shopData->currencyId`
	* Desctription: Currency code (see [Yandex docs](https://yandex.ru/support/partnermarket/currencies.html)).
	* Valid values: `string`
	* Default value: `'RUR'`
	
* `outputterParams->shopData->platform`
	* Desctription: `<platform>` tag content.
	* Valid values: `string`
	* Default value: `'(MODX) Evolution CMS'`
	
* `outputterParams->shopData->version`
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
	
* `outputterParams->offerFields`
	* Desctription: Offer fields parameters.
	* Valid values: `object`
	* **Required**
	
* `outputterParams->offerFields->{$fieldName}`
	* Desctription: Offer field parameter.
	* Valid values:
		* `string` — the parameter can be set as a document field name
		* `object` — or as an object with additional params (see below)
	* Default value: —
	
* `outputterParams->offerFields->{$fieldName}->docFieldName`
	* Desctription: A document field name that contains offer field value.
	* Valid values: `stringTvName`
	* **Required**
	
* `outputterParams->offerFields->{$fieldName}->disableEscaping`
	* Desctription: You can disable escaping special characters (`'`, `"`, `&`, `<`, `>`) in the field value.
	* Valid values: `boolean`
	* Default value: `false`
	
* `outputterParams->offerFields->{$fieldName}->valuePrefix`
	* Desctription: You can set custom string that will be added before the field value.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->offerFields->{$fieldName}->valueSuffix`
	* Desctription: You can set custom string that will be added after the field value.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->offerFields->price`
	* Desctription: A document field name, that contains offer price.  
		If a document field value is empty, but `outputterParams->offerFields->priceOld` is set, the last will be used instead.
	* Valid values: `stringTvName`
	* **Required**
	
* `outputterParams->offerFields->priceOld`
	* Desctription: A document field name, that contains old offer price (must be less then `outputterParams->offerFields->price` or will not be used).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->picture`
	* Desctription: A document field name, that contains offer picture.
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->name`
	* Desctription: A document field name, that contains offer name.  
		If a document field value is empty, the `pagetitle` field will be used instead.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: `'pagetitle'`
	
* `outputterParams->offerFields->model`
	* Desctription: A document field name, that contains offer model.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->model`
	* Desctription: A document field name, that contains offer vendor.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->available`
	* Desctription: A document field name, that contains offer availability status (`true`|`false`).
	* Valid values:
		* `stringTvName`
		* `''` — always display `'true'`.
	* Default value: `''`
	
* `outputterParams->offerFields->description`
	* Desctription: A document field name, that contains offer description (less than 3 000 chars).
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->salesNotes`
	* Desctription: A document field name, that contains offer <[sales_notes](https://yandex.ru/support/partnermarket/elements/sales_notes.html)>.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->manufacturerWarranty`
	* Desctription: A document field name, that contains offer manufacturer warraynty status (`true`|`false`).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->countryOfOrigin`
	* Desctription: A document field name, that contains offer country of origin.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->homeCourierDelivery`
	* Desctription: A document field name, that contains offer courier delivery status (`true`|`false`).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->dimensions`
	* Desctription: A document field name, that contains offer dimensions (length, width, height) including packaging.  
		Specify dimensions in centimeters. Format: three positive numbers with accuracy of 0.001, using a dot as the decimal separator.  
		The numbers must be separated by the slash character `/` without spaces.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->weight`
	* Desctription: Item weight in kilograms including packaging.  
		Some categories have limits on the minimum or maximum weight.  
		[Download a list of minimum and maximum weight values](https://download.cdn.yandex.net/support/ru/partnermarket/yandex.market-weight.xlsx).  
		In any category, the weight can be specified accurate to one thousandth (for example, 1.001, using a dot as a decimal point).  
		If the minimum value is set to 0, there is no minimum weight limit and it can be specified starting from one gram (0.001 kg).
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->additionalParams`
	* Desctription: A document field name, that contains offer <[param](https://yandex.ru/support/partnermarket/param.html)> elements.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->customData`
	* Desctription: A document field name, that contains custom text that must be inserted before `</offer>`.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->templates`
	* Desctription: Templates.
	* Valid values: `object`
	* Default value: —
	
* `outputterParams->templates->wrapper`
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
	
* `outputterParams->templates->categories_item`
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
	
* `outputterParams->templates->offers_item`
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
	
* `outputterParams->templates->{'offers_item_elem' . $FieldName}`
	* Desctription: You can set custom template for any offer element. Specify an element name in accordance with `offerFields->` parameters, e. g. `outputterParams->templates->offers_item_elemCountryOfOrigin`.  
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
		Extender names are case insensitive (the following names are equal: `pagination`, `Pagination`, `pAgInAtIoN`, etc).
	* Valid values:
		* `'pagination'`
		* `'tagging'`
		* `'search'`
		* `'sortFromURL'`
	* **Required**
	
* `extendersParams`
	* Desctription: Parameters to be passed to their corresponding extensions.  
		You can avoid extender name if you are using only one extender (see examples below).
	* Valid values:
		* `stirngJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: —
	
* `extendersParams->{$extenderName}`
	* Desctription: Parameters of an extender, when the key is the extender name and the value is the extender parameters.
	* Valid values: `object
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


##### Extenders → SortFromURL (``&extenders=`sortFromURL` ``)

* `$_GET['orderBy']`
	* Desctription: A string representing the sorting rule similar to `providerParams->orderBy`.
	* Valid values: `string`
	* Default value: —


### Examples


#### Simple fetching child documents from a parent with ID = `1`

```html
[[ddGetDocuments?
	&providerParams=`{
		"parentIds": "1",
		"depth": 1
	}`
	&outputterParams=`{
		"itemTpl": "@CODE:<div><h2>[+pagetitle+]</h2><p>[+introtext+]</p>[+someTV+]</div>"
	}`
]]
```


#### Simple fetching child documents from a parent with ID = `1` with the `providerParams->filter`

Add a filter that would not output everything. Let's say we need only published documents.

_Don't forget about `fieldDelimiter`._

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		"parentIds": "1",
		"depth": 1,
		"filter": "#published# = 1"
	}`
	&outputterParams=`{
		"itemTpl": "documents_item"
	}`
]]
```

So we can filter as much as we like (we can use `AND` and `OR`, doucument fields and TVs):

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		"parentIds": "1",
		"depth": 1,
		"filter": "#published# = 1 AND #hidemenu# = 0 OR #SomeTVName# = 1"
	}`
	&outputterParams=`{
		"itemTpl": "documents_item"
	}`
]]
```


#### Sorting by TV with the `date` type (`providerParams->orderBy`)

Dates in DB stored in specific format (`01-02-2017 08:59:45`) and sorting works unexpectedly at first sight.
So, we can't just type:

```
&providerParams=`{
	"orderBy": "#TVName# DESC"
}`
```

For correct working we need to convert date from DB to Unixtime for sorting:

```
&providerParams=`{
	"orderBy": "STR_TO_DATE(#TVName#, '%d-%m-%Y %H:%i:%s') DESC"
}`
```

When `TVName` — TV name for sorting by.


#### Outputters → JSON (``&outputter=`json` ``): Fetch documents and output in JSON

```
[[ddGetDocuments?
	&providerParams=`{"parentIds": "1"}`
	&outputter=`json`
]]
```

Returns:

```json
[
	{"id": "2"},
	{"id": "3"},
	{"id": "4"}
]
```


#### Outputters → JSON (``&outputter=`json` ``): Set documents fields to output

```
[[ddGetDocuments?
	&providerParams=`{"parentIds": "1"}`
	&outputter=`json`
	&outputterParams=`{
		"docFields": "id,pagetitle,menuindex,someTV"
	}`
]]
```

Returns:

```json
[
	{
		"id": "2",
		"pagetitle": "About",
		"menuindex": "0",
		"someTV": "Some value"
	},
	{
		"id": "3",
		"pagetitle": "Partners",
		"menuindex": "1",
		"someTV": ""
	},
	{
		"id": "4",
		"pagetitle": "Contacts",
		"menuindex": "2",
		"someTV": ""
	}
]
```


#### Extenders → Pagination (``&extenders=`pagination` ``)

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		"parentIds": "[*id*]",
		"filter": "#published# = 1",
		"total": 3,
		"orderBy": "#pub_date# DESC`"
	}`
	&outputterParams=`{
		"itemTpl": "documents_item",
		"wrapperTpl": "@CODE:[+ddGetDocuments_items+][+extenders.pagination+]",
		"noResult": "@CODE:"
	}`
	&extenders=`pagination`
	&extendersParams=`{
		"pagination": {
			"wrapperTpl": "general_pagination",
			"nextTpl": "general_pagination_next",
			"previousTpl": "general_pagination_prev",
			"nextOffTpl": "general_pagination_nextOff",
			"previousOffTpl": "general_pagination_prevOff",
			"pageTpl": "general_pagination_page",
			"currentPageTpl": "general_pagination_pageCurrent"
		}
	}`
]]
```

* ``&providerParams=`{"parentIds": "[*id*]"}` `` — fetch current doc children.
* ``&providerParams=`{"filter": "#published# = 1"}` `` — only published.
* ``&providerParams=`{"total": 3}` `` — items per page.
* ``&providerParams=`{"orderBy": "#pub_date# DESC"} `` — sort by publish date, new first.
* ``&outputterParams=`{"itemTpl": "documents_item"}` `` — item template (chunk name).
* ``&outputterParams=`{"noResult": "@CODE:"}` `` — return nothing if nothing found.
* ``&extendersParams=`{"pagination": {}}` `` — pagination templates (see the parameters description).
* ``&wrapperTpl=`@CODE:[+ddGetDocuments_items+][+extenders.pagination+]` `` — we need set where pagination will being outputted.


#### Extenders → Search (``&extenders=`search` ``)

Call the snippet at the page with search results.
Let's specify where and how deep we will search.
Set up filter to get only necessary documets.

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		"parentIds": 1,
		"depth": 3,
		"filter": "#published# = 1 AND #deleted# = 0 AND #template# = 11"
	}`
	&extenders=`search`
	&extendersParams=`{
		"docFieldsToSearch": "pagetitle,content,someTv"
	}`
	&outputterParams=`{
		"itemTpl": "documents_item"
	}
]]
```

Then just add to the page URL `?query=Some query text` and the snippet returns only documents contain that string in the `pagetitle`, `content` or `someTv` fields.

We recommend to use cashed snippet calls and turn on document caching type with $_GET parameters in CMS configuration.


## Links

* [Home page](https://code.divandesign.biz/modx/ddgetdocuments)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddgetdocuments)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />