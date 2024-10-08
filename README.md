# (MODX)EvolutionCMS.snippets.ddGetDocuments

A snippet for fetching and parsing resources from the document tree or custom DB table by a custom rule.


## Requires

* PHP >= 5.6
* MySQL >= 8 or MariaDB >= 10.3.10 (not tested in older versions).
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.62


## Installation


### Using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
// Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

// Install (MODX)EvolutionCMS.snippets.ddGetDocuments
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDocuments',
]);
```

* If `ddGetDocuments` is not exist on your site, `ddInstaller` will just install it.
* If `ddGetDocuments` is already exist on your site, `ddInstaller` will check it version and update it if needed.


### Manually


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddGetDocuments`.
2. Description: `<b>1.8</b> A snippet for fetching and parsing resources from the document tree or custom DB table by a custom rule.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetDocuments_snippet.php` file from the archive.


#### 2. Elements → Manage Files:

1. Create a new folder `assets/snippets/ddGetDocuments/`.
2. Extract the archive to the folder (except `ddGetDocuments_snippet.php`).


## Parameters description


### Core parameters

* `fieldDelimiter`
	* Description: The field delimiter to be used in order to distinct data base column names in those parameters which can contain SQL queries directly, e. g. `providerParams->groupBy`, `providerParams->orderBy` and `providerParams->filter`.
	* Valid values: `string`
	* Default value: ``'`'``


### Data provider parameters

* `provider`
	* Description: Name of the provider that will be used to fetch documents.
		* Data provider names are case insensitive (the following names are equal: `parent`, `Parent`, `pArEnT`, etc).
	* Valid values:
		* `'parent'`
		* `'select'`
	* Default value: `'parent'`
	
* `providerParams`
	* Description: Parameters to be passed to the provider.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `providerParams->filter`
	* Description: The filter condition in SQL-style to be applied while resource fetching.
		* Notice that all fields/tvs names specified in the filter parameter must be wrapped in `fieldDelimiter`.
	* Valid values: `string`
	* Default value: ``'`published` = 1 AND `deleted` = 0'``
	
* `providerParams->total`
	* Description: The maximum number of the resources that will be returned.
	* Valid values: `integer`
	* Default value: — (all)
	
* `providerParams->offset`
	* Description: Resources offset.
	* Valid values: `integer`
	* Default value: `0`
	
* `providerParams->groupBy`
	* Description: Group items that have the same values into summary item (like SQL `GROUP BY`).
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `providerParams->groupBy[$i]`
	* Description: Document field or TV by which the items will be grouped.
	* Valid values: `string`
	* **Required**
	
* `providerParams->orderBy`
	* Description: A string representing the sorting rule.
		* TV names also can be used.
	* Valid values: `string`
	* Default value: —


#### Providers → Parent (``&provider=`parent` ``)

* `providerParams->parentIds`
	* Description: Parent ID(s).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: `[0]`
	
* `providerParams->parentIds[i]`
	* Description: Document ID.
	* Valid values: `integer`
	* **Required**
	
* `providerParams->depth`
	* Description: Depth of children documents search.
	* Valid values: `integer`
	* Default value: `1`
	
* `providerParams->excludeIds`
	* Description: The document IDs which need to exclude.
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: —
	
* `providerParams->excludeIds[i]`
	* Description: Document ID.
	* Valid values: `integer`
	* **Required**


#### Providers → Select (``&provider=`select` ``)

* `providerParams->ids`
	* Description: Document IDs to output.
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* **Required**
	
* `providerParams->ids[i]`
	* Description: Document ID.
	* Valid values: `integer`
	* **Required**


#### Providers → Customdbtable (``&provider=`customdbtable` ``)

Get resources from custom DB table.

* `providerParams->resourcesTableName`
	* Description: DB table to get resources from.
	* Valid values: `string`
	* **Required**


### Output format parameters

* `outputter`
	* Description: Format of the output.
		* Outputter names are case insensitive (the following names are equal: `string`, `String`, `sTrInG`, etc).
	* Valid values:
		* `'string'`
		* `'json'`
		* `'sitemap'`
		* `'yandexmarket'`
		* `'raw'`
	* Default value: `'string'`
	
* `outputterParams`
	* Description: Parameters to be passed to the specified outputter.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `outputterParams->templates`
	* Description: Output templates.
	* Valid values: `object`
	* Default value: —


#### Outputter → String (``&outputter=`string` ``)

* `outputterParams->templates->item`
	* Description: Item template.
		* Available placeholders:
			* `[+`any document field or tv name`+]` — Any document field name or TV.
			* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
			* `[+`any placeholders from `placeholders` param`+]` — Any custom placeholders (see `outputterParams->placeholders` description below).
			* `[+itemNumber+]` — Item number started from 1.
			* `[+itemNumberZeroBased+]` Item number started from 0.
			* `[+total+]` — number of returned items
			* `[+totalFound+]` — number of found items
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* **Required**
	
* `outputterParams->templates->itemFirst`
	* Description: Template for the first item.
		* Has the same placeholders as `outputterParams->templates->item`.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: == `outputterParams->templates->item`.
	
* `outputterParams->templates->itemLast`
	* Description: Template for the last item.
		* Has the same placeholders as `outputterParams->templates->item`.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: == `outputterParams->templates->item`.
	
* `outputterParams->templates->wrapper`
	* Description: Wrapper template.
		* Available placeholders:
			* `[+`any document field or tv name`+]` — Any document field name or TV.
			* `[+`any of extender placeholders`+]` — Any extender placeholders (see extenders description below).
			* `[+`any placeholders from `placeholders` param`+]` — Any custom placeholders (see `outputterParams->placeholders` description below).
			* `[+ddGetDocuments_items+]`
			* `[+total+]` — number of returned items
			* `[+totalFound+]` — number of found items
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —
	
* `outputterParams->templates->noResults`
	* Description: A chunk or text to output when no items found.
		* Has the same placeholders as `outputterParams->templates->wrapper`.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —
	
* `outputterParams->placeholders`
	* Description: Additional data has to be passed into `templates->item`, `templates->itemFirst`, `templates->itemLast` and `templates->wrapper`.
		* Arrays are supported too: `some[a]=one&some[b]=two` => `[+some.a+]`, `[+some.b+]`; `some[]=one&some[]=two` => `[+some.0+]`, `[some.1]`.
	* Valid values: `object`
	* Default value: —
	
* `outputterParams->placeholders->{$name}`
	* Description: Key for placeholder name and value for placeholder value.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->itemGlue`
	* Description: The string that combines items while rendering.
	* Valid values: `string`
	* Default value: `''`


#### Outputter → Json (``&outputter=`json` ``)

* `outputterParams->docFields`
	* Description: Document fields to output (including TVs).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: `'id'`
	
* `outputterParams->docFields[i]`
	* Description: Document field or TV.
		* You can use custom aliases instead of field names for output using the `'='` separator (for example: `'pagetitle=title'`, `'content=text'`, etc).
	* Valid values:
		* `string` — document field
		* `stringSeparated` — document field and it's alias separated by `'='`
	* **Required**
	
* `outputterParams->templates->{$docFieldName}`
	* Description: You can use templates for some document fields.
		* Templates will be used before JSON conversion of results. So you don't need to care about characters escaping.
		* It is useful for manipulations with doc field values through running snippets.  
		* Available placeholders:
			* `[+value+]` — the field value
			* `[+`any document field or tv name`+]` — Any document field name or TV specified in `outputterParams->docFields`
			* `[+itemNumber+]` — item number started from 1
			* `[+itemNumberZeroBased+]` — item number started from 0
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —
	
* `outputterParams->templates->noResults`
	* Description: A chunk or text to output when no items found.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: `'[]'`
	
* `outputterParams->templates->wrapper`
	* Description: Wrapper template.
		* Available placeholders:
			* `[+ddGetDocuments_items+]`
			* `[+total+]` — number of returned items
			* `[+totalFound+]` — number of found items
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: `'[+ddGetDocuments_items+]'`


#### Outputter → Sitemap (``&outputter=`sitemap` ``)

Output in [Sitemap XML format](https://en.wikipedia.org/wiki/Sitemaps).

* `outputterParams->priorityTVName`
	* Description: Name of TV which sets the relative priority of the document.
	* Valid values: `stringTvName`
	* Default value: `'general_seo_sitemap_priority'`
	
* `outputterParams->changefreqTVName`
	* Description: Name of TV which sets the change frequency.
	* Valid values: `stringTvName`
	* Default value: `'general_seo_sitemap_changefreq'`
	
* `outputterParams->templates->item`
	* Description: Item template.  
		* Available placeholders:
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
	
* `outputterParams->templates->wrapper`
	* Description: Wrapper template.
		* Available placeholders:
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


#### Outputter → Yandexmarket (``&outputter=`yandexmarket` ``)

Output in [YML format](https://yandex.ru/support/partnermarket/export/yml.html).

* `outputterParams->shopData`
	* Description: Shop data.
	* Valid values: `object`
	* **Required**
	
* `outputterParams->shopData->shopName`
	* Description: Short shop name, length <= 20.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->shopData->companyName`
	* Description: Company legal name. Internal data that not be displayed but required by Yandex.
	* Valid values: `string`
	* **Required**
	
* `outputterParams->shopData->agency`
	* Description: 	Web developers agency name.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->shopData->currencyId`
	* Description: Currency code (see [Yandex docs](https://yandex.ru/support/partnermarket/currencies.html)).
	* Valid values: `string`
	* Default value: `'RUR'`
	
* `outputterParams->shopData->platform`
	* Description: `<platform>` tag content.
	* Valid values: `string`
	* Default value: `'(MODX) Evolution CMS'`
	
* `outputterParams->shopData->version`
	* Description: `<version>` tag content.
	* Valid values: `string`
	* Default value: `'[(settings_version)]'`
	
* `outputterParams->categoryIds_last`
	* Description: Allows to add additional parent elements in the category section. If empty only immediate parents of goods will be returned.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `outputterParams->categoryIds_last[i]`
	* Description: Document ID.
	* Valid values: `integer`
	* **Required**
	
* `outputterParams->offerFields`
	* Description: Offer fields parameters.
	* Valid values: `object`
	* **Required**
	
* `outputterParams->offerFields->{$fieldName}`
	* Description: Offer field parameter.
	* Valid values:
		* `string` — the parameter can be set as a document field name
		* `object` — or as an object with additional params (see below)
	* Default value: —
	
* `outputterParams->offerFields->{$fieldName}->docFieldName`
	* Description: A document field name that contains offer field value.
	* Valid values: `stringTvName`
	* **Required**
	
* `outputterParams->offerFields->{$fieldName}->disableEscaping`
	* Description: You can disable escaping special characters (`'`, `"`, `&`, `<`, `>`) in the field value.
	* Valid values: `boolean`
	* Default value: `false`
	
* `outputterParams->offerFields->{$fieldName}->valuePrefix`
	* Description: You can set custom string that will be added before the field value.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->offerFields->{$fieldName}->valueSuffix`
	* Description: You can set custom string that will be added after the field value.
	* Valid values: `string`
	* Default value: —
	
* `outputterParams->offerFields->price`
	* Description: A document field name, that contains offer price.
		* If a document field value is empty, but `outputterParams->offerFields->priceOld` is set, the last will be used instead.
	* Valid values: `stringTvName`
	* **Required**
	
* `outputterParams->offerFields->priceOld`
	* Description: A document field name, that contains old offer price (must be less then `outputterParams->offerFields->price` or will not be used).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->picture`
	* Description: A document field name, that contains offer picture.
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->name`
	* Description: A document field name, that contains offer name.
		* If a document field value is empty, the `pagetitle` field will be used instead.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: `'pagetitle'`
	
* `outputterParams->offerFields->model`
	* Description: A document field name, that contains offer model.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->model`
	* Description: A document field name, that contains offer vendor.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->available`
	* Description: A document field name, that contains offer availability status (`true`|`false`).
	* Valid values:
		* `stringTvName`
		* `''` — always display `'true'`.
	* Default value: `''`
	
* `outputterParams->offerFields->description`
	* Description: A document field name, that contains offer description (less than 3 000 chars).
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->salesNotes`
	* Description: A document field name, that contains offer <[sales_notes](https://yandex.ru/support/partnermarket/elements/sales_notes.html)>.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->manufacturerWarranty`
	* Description: A document field name, that contains offer manufacturer warraynty status (`true`|`false`).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->countryOfOrigin`
	* Description: A document field name, that contains offer country of origin.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->homeCourierDelivery`
	* Description: A document field name, that contains offer courier delivery status (`true`|`false`).
	* Valid values: `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->dimensions`
	* Description: A document field name, that contains offer dimensions (length, width, height) including packaging.
		* Specify dimensions in centimeters. Format: three positive numbers with accuracy of 0.001, using a dot as the decimal separator.
		* The numbers must be separated by the slash character `/` without spaces.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->weight`
	* Description: Item weight in kilograms including packaging.
		* Some categories have limits on the minimum or maximum weight.
		* [Download a list of minimum and maximum weight values](https://download.cdn.yandex.net/support/ru/partnermarket/yandex.market-weight.xlsx).
		* In any category, the weight can be specified accurate to one thousandth (for example, 1.001, using a dot as a decimal point).
		* If the minimum value is set to `0`, there is no minimum weight limit and it can be specified starting from one gram (0.001 kg).
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->additionalParams`
	* Description: A document field name, that contains offer <[param](https://yandex.ru/support/partnermarket/param.html)> elements.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->offerFields->customData`
	* Description: A document field name, that contains custom text that must be inserted before `</offer>`.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* Default value: —
	
* `outputterParams->templates->wrapper`
	* Description: Wrapper template.
		* Available placeholders:
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
	* Description: Category item template.
		* Available placeholders:
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
	* Description: Offer item template.
		* Available placeholders:
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
	* Description: You can set custom template for any offer element.
		* Specify an element name in accordance with `offerFields->` parameters, e. g. `outputterParams->templates->offers_item_elemCountryOfOrigin`.
		* Available placeholders:
			* `[+tagName+]` — Element tag name.
			* `[+value+]` — Element value.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default: —


### Extenders parameters
	
* `extenders`
	* Description: Comma-separated string determining which extenders should be applied to the snippet.
		* Be aware that the order of extender names can affect the output.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `extenders[i]`
	* Description: Extender name.
		* Be aware that the order of extender names can affect the output.
		* Extender names are case insensitive (the following names are equal: `pagination`, `Pagination`, `pAgInAtIoN`, etc).
	* Valid values:
		* `'pagination'`
		* `'tagging'`
		* `'search'`
		* `'sortFromURL'`
	* **Required**
	
* `extendersParams`
	* Description: Parameters to be passed to their corresponding extensions.
		* You can avoid extender name if you are using only one extender (see examples below).
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `extendersParams->{$extenderName}`
	* Description: Parameters of an extender, when the key is the extender name and the value is the extender parameters.
	* Valid values: `object
	* Default value: —


#### Extenders → Pagination (``&extenders=`pagination` ``)

* `extendersParams->pagination->wrapperTpl`
	* Description: Chunk to be used to output the pagination.
		* Available placeholders:
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
	* Description: Chunk to be used to output pages within the pagination.
		* Available placeholders:
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
	* Description: Chunk to be used to output the current page within the pagination.
		* Available placeholders:
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
	* Description: Chunk to be used to output the navigation block to the next page.
		* Available placeholders:
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
	* Description: 	Chunk to be used to output the navigation block to the next page if there are no more pages after.
		* Available placeholders:
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
	* Description: Chunk to be used to output the navigation block to the previous page.
		* Available placeholders:
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
	* Description: Chunk to be used to output the navigation block to the previous page if there are no more pages before.
		* Available placeholders:
			* `[+totalPages+]` — Total number of pages.
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value:
		```html
		<span class="pagination_prev">←&nbsp;<span>Предыдущая</span></span><br>
		<small><span class="pagination_first">←&nbsp;<span>Первая</span></span></small>
		```


#### Extenders → Tagging (``&extenders=`tagging` ``)

* `extendersParams->tagging->tagsDocumentField`
	* Description: The document field (TV) contains tags.
	* Valid values: `stringTvName`
	* Default value: `'tags'`
	
* `extendersParams->tagging->tagsDelimiter`
	* Description: Tags delimiter in the document field.
	* Valid values: `string`
	* Default value: `', '`
	
* `extendersParams->tagging->tagsRequestParamName`
	* Description: The parameter in `$_REQUEST` to get the tags value from.
	* Valid values: `string`
	* Default value: `'tags'`


#### Extenders → Search (``&extenders=`search` ``)

* `extendersParams->search->docFieldsToSearch`
	* Description: Document fields to search in (including TVs).
	* Valid values:
		* `array`
		* `stringCommaSeparated`
	* Default value: `'pagetitle,content'`
	
* `extendersParams->search->docFieldsToSearch[i]`
	* Description: Document fields or TV.
	* Valid values:
		* `stringDocFieldName`
		* `stringTvName`
	* **Required**


#### Extenders → SortFromURL (``&extenders=`sortFromURL` ``)

* `$_GET['orderBy']`
	* Description: A string representing the sorting rule similar to `providerParams->orderBy`.
	* Valid values: `string`
	* Default value: —


## Examples

All examples are written using [HJSON](https://hjson.github.io/), but if you want you can use vanilla JSON instead.


### Simple fetching child documents from a parent with ID = `1`

```html
[[ddGetDocuments?
	&providerParams=`{
		parentIds: 1
		depth: 1
	}`
	&outputterParams=`{
		templates: {
			item:
				'''
				@CODE:<div>
					<h2>[+pagetitle+]</h2>
					<p>[+introtext+]</p>
					[+someTV+]
				</div>
				'''
		}
	}`
]]
```


### Simple fetching child documents from a parent with ID = `1` with the `providerParams->filter`

Add a filter that would not output everything. Let's say we need only published documents.

_Don't forget about `fieldDelimiter`._

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		parentIds: 1
		depth: 1
		filter: "#published# = 1"
	}`
	&outputterParams=`{
		templates: {
			item: documents_item
		}
	}`
]]
```

So we can filter as much as we like (we can use `AND` and `OR`, doucument fields and TVs):

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		parentIds: 1
		depth: 1
		filter:
			'''
			#published# = 1 AND
			#hidemenu# = 0 OR
			#SomeTVName# = 1
			'''
	}`
	&outputterParams=`{
		templates: {
			item: documents_item
		}
	}`
]]
```


### Sorting by TV with the `date` type (`providerParams->orderBy`)

Dates in DB stored in specific format (`01-02-2017 08:59:45`) and sorting works unexpectedly at first sight.
So, we can't just type:

```
&providerParams=`{
	orderBy: "#TVName# DESC"
}`
```

For correct working we need to convert date from DB to Unixtime for sorting:

```
&providerParams=`{
	orderBy: "STR_TO_DATE(#TVName#, '%d-%m-%Y %H:%i:%s') DESC"
}`
```

When `TVName` — TV name for sorting by.


### Outputters → JSON (``&outputter=`json` ``): Fetch documents and output in JSON

```
[[ddGetDocuments?
	&providerParams=`{parentIds: 1}`
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


### Outputters → JSON (``&outputter=`json` ``): Set documents fields to output

```
[[ddGetDocuments?
	&providerParams=`{parentIds: 1}`
	&outputter=`json`
	&outputterParams=`{
		docFields: id,pagetitle,menuindex,someTV
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


### Outputters → JSON (``&outputter=`json` ``): Use custom aliases instead of field names

```
[[ddGetDocuments?
	&providerParams=`{
		parentIds: 1
	}`
	&outputter=`json`
	&outputterParams=`{
		docFields: pagetitle=name,menuindex=position
	}`
]]
```

Returns:

```json
[
	{
		"name": "Denial",
		"position": "0",
	},
	{
		"name": "Anger",
		"position": "1",
	},
	{
		"name": "Bargaining",
		"position": "2",
	},
	{
		"name": "Depression",
		"position": "3",
	},
	{
		"name": "Acceptance",
		"position": "4",
	}
]
```


### Group items that have the same field values into summary item (`providerParams->orderBy`)

For example we have the following documents with TV `gender`:

* Mary Teresa, female
* Mahatma Gandhi, male
* Tenzin Gyatso, male
* Dmitry Muratov, male
* ICAN, none

And we want to make a gender list with unique items: 

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		// The parent of our documents
		parentIds: 42
		// The field by which the items will be grouped
		groupBy: "#gender#"
	}`
	&outputter=`json`
	&outputterParams=`{
		docFields: gender
	}`
]]
```

Returns:

```json
[
	{"gender": "female"},
	{"gender": "male"},
	{"gender": "none"}
]
```


### Extenders → Pagination (``&extenders=`pagination` ``)

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		parentIds: "[*id*]"
		filter: "#published# = 1"
		total: 3
		orderBy: "#pub_date# DESC`"
	}`
	&outputterParams=`{
		templates: {
			item: documents_item
			wrapper:
				''''
				@CODE:[+ddGetDocuments_items+]
				[+extenders.pagination+]
				'''
			noResults: "@CODE:"
		}
	}`
	&extenders=`pagination`
	&extendersParams=`{
		pagination: {
			wrapperTpl: general_pagination
			nextTpl: general_pagination_next
			previousTpl: general_pagination_prev
			nextOffTpl: general_pagination_nextOff
			previousOffTpl: general_pagination_prevOff
			pageTpl: general_pagination_page
			currentPageTpl: general_pagination_pageCurrent
		}
	}`
]]
```

* ``&providerParams=`{parentIds: "[*id*]"}` `` — fetch current doc children.
* ``&providerParams=`{filter: "#published# = 1"}` `` — only published.
* ``&providerParams=`{total: 3}` `` — items per page.
* ``&providerParams=`{orderBy: "#pub_date# DESC"} `` — sort by publish date, new first.
* ``&outputterParams=`{templates: {item: documents_item}}` `` — item template (chunk name).
* ``&outputterParams=`{templates: {wrapper: "@CODE:[+ddGetDocuments_items+][+extenders.pagination+]"}}` `` — we need set where pagination will being outputted.
* ``&outputterParams=`{templates: {noResults: "@CODE:"}}` `` — return nothing if nothing found.
* ``&extendersParams=`{pagination: {}}` `` — pagination templates (see the parameters description).


### Extenders → Search (``&extenders=`search` ``)

Call the snippet at the page with search results.
Let's specify where and how deep we will search.
Set up filter to get only necessary documets.

```
[[ddGetDocuments?
	&fieldDelimiter=`#`
	&providerParams=`{
		parentIds: 1
		depth: 3
		filter:
			'''
			#published# = 1 AND
			#deleted# = 0 AND
			#template# = 11
			'''
	}`
	&extenders=`search`
	&extendersParams=`{
		docFieldsToSearch: pagetitle,content,someTv
	}`
	&outputterParams=`{
		templates: {
			item: documents_item
		}
	}
]]
```

Then just add to the page URL `?query=Some query text` and the snippet returns only documents contain that string in the `pagetitle`, `content` or `someTv` fields.

We recommend to use cashed snippet calls and turn on document caching type with $_GET parameters in CMS configuration.


### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

```php
// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

// Run (MODX)EvolutionCMS.snippets.ddGetDocuments
\DDTools\Snippet::runSnippet([
	'name' => 'ddGetDocuments',
	'params' => [
		// It is convenient to set the parameter as a native PHP array or object
		'providerParams' => [
			'parentIds' => 1,
		],
		'outputter' => 'json',
	],
]);
```


## Links

* [Home page](https://code.divandesign.ru/modx/ddgetdocuments)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddgetdocuments)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDocuments)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />