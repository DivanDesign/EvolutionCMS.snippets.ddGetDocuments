# (MODX)EvolutionCMS.snippets.ddGetDocuments changelog


## Version 1.6 (2022-09-30)
* \+ Outputters → Json → Parameters → `outputterParams->templates->{$docFieldName}` → Placeholders: The new placeholders contain any document field name or TV specified in `outputterParams->docFields`.
* \* Outputters → Yandexmarket: Critical error related to missing initialization of an object field has been fixed.


## Version 1.5 (2022-06-03)
* \+ Parameters → `providerParams->groupBy`: The new parameter. Allows to group items that have the same field values into summary item (like SQL `GROUP BY`). See README.
* \* README → Examples: HJSON is used for all examples.


## Version 1.4 (2021-07-27)
* \* Attention! PHP >= 5.6 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.50 is required.
* \+ Parameters → `providerParams`, `outputterParams`, `extendersParams`: Can also be set as [HJSON](https://hjson.github.io/) or as a native PHP object or array (e. g. for calls through `$modx->runSnippet`).
* \+ You can just call `\DDTools\Snippet::runSnippet` to run the snippet without DB and eval (see README → Examples).
* \+ Outputters → Json → Parameters → `outputterParams->templates`: The new parameters. You can use templates for some document fields.
* \* Outputters → String → Parameters → `outputterParams->templates`: The following parameters moved here (with backward compatibility):
	* \* `outputterParams->itemTpl` → `outputterParams->templates->item`.
	* \* `outputterParams->itemTplFirst` → `outputterParams->templates->itemFirst`.
	* \* `outputterParams->itemTplLast` → `outputterParams->templates->itemLast`.
	* \* `outputterParams->wrapperTpl` → `outputterParams->templates->wrapper`.
	* \* `outputterParams->noResults` → `outputterParams->templates->noResults`.
* \* Outputters → Sitemap → Parameters → `outputterParams->templates`: The following parameters moved here (with backward compatibility):
	* \* `outputterParams->itemTpl` → `outputterParams->templates->item`.
	* \* `outputterParams->wrapperTpl` → `outputterParams->templates->wrapper`.
* \+ README → Documentation → Installation → Using (MODX)EvolutionCMS.libraries.ddInstaller.
* \+ Composer.json.
	* \+ `support`.
	* \+ `authors`: Added missed authors.


## Version 1.3.1 (2021-02-28)
* \* Outputters → String → Parameters → `outputterParams->placeholders`: Fixed critical error when the parameter is used.


## Version 1.3 (2021-02-27)
* \* Attention! (MODX)Evolution.libraries.ddTools >= 0.42 is required.
* \* Parameters: The following parameters were moved from Snippet to Provider (with backward compatibility):
	* \* `filter` → `providerParams->filter`.
	* \* `offset` → `providerParams->offset`.
	* \* `total` → `providerParams->total`.
* \* Outputters → Yandexmarket:
	* \* Fixed smart name and price preparation.
	* \* Parameters: Structure was changed, added an additional nesting level for `shopData`, `offerFields` and `templates` (with backward compatibility).
	* \+ Added the ability to disable escaping special characters (`'`, `"`, `&`, `<`, `>`) in the field value (see `outputterParams->offerFields->{$fieldName}->disableEscaping`).
	* \+ Also you can set a custom string that will be added before or after the field value (see `outputterParams->offerFields->{$fieldName}->valuePrefix` and `outputterParams->offerFields->{$fieldName}->valueSuffix`).
* \+ Added the ability to run snippet without DB requests and PHP `eval`:
	```php
	\DDTools\Snippet::runSnippet([
		'name' => 'ddGetDocuments',
		'params' => $params
	]);
	```
* \+ README:
	* \+ Parameters description → Extenders parameters → `extendersParams->{$extenderName}`.
	* \+ Links → Packagist.


## Version 1.2 (2020-10-09)
* \+ Extenders → SortFromURL (see README).
* \* Parameters: The following were changed (with backward compatibility):
	* \- `orderBy`.
	* \+ `providerParams->orderBy`.
* \* Refactoring.
* \+ README:
	* \+ Examples.
	* \+ Links.


## Version 1.1 (2020-07-05)
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.40.1 is required (not tested in older versions).
* \* Improved compatibility with new versions of (MODX)EvolutionCMS.libraries.ddTools.
* \* Snippet:
	* \+ `extendersParams->pagination->wrapperTpl`: The new placeholder `[+totalPages+]`.
	* \* Small refactoring.
* \* `\ddGetDocuments\DataProvider\DataProvider::prepareQueryData`: TVs default values are used (#6).
* \* `\ddGetDocuments\Outputter\Json\Outputter::parse`: Removed unused variable.
* \+ README, CHAGNELOG: Style improvements.
* \* Composer.json:
	* \+ `homepage`.
	* \+ `authors`.
	* \* `name`: Renamed as `evolutioncms-snippets-ddgetdocuments` from `dd/modxevo-snippet-ddgetdocuments`.
	* \* `require`:→ `dd/evolutioncms-libraries-ddtools`: Renamed from `dd/modxevo-library-ddtools`.


## Version 1.0 (2020-03-11)
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.30 is required.
* \+ Providers → Customdbtable. Get resources from custom DB table.
* \* Outputters → Yandexmarket:
	* \+ Added the following parameters:
		* \+ `outputterParams->shopData_platform`
		* \+ `outputterParams->shopData_version`
	* \+ Offer `priceOld` will be used if `price` is empty.
	* \+ Offer `pagetitle` will be used if `name` is empty.
	* \+ `0` weight is not displayed because it's invalid.
* \* Extenders → Pagination:
	* \* `extendersParams->pagination->currentPageTpl`: Fixed empty href on default value.
* \* Snippet:
	* \* Empty `extenders` parameter is not used.
* \+ README:
	* \+ Requires.
	* \+ Documentation → Installation.
	* \+ Documentation → Parameters description.
* \+ CHANGELOG.


## Version 0.1 (2015-09-23)
* \+ The first release.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>