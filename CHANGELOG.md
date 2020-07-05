# (MODX)EvolutionCMS.snippets.ddGetDocuments changelog


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


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />
<style>ul{list-style:none;}</style>