# ddGetDocuments changelog
## Version 0.4.1 (2016-01-29)
* \+ ddGetDocuments\DataProvider\DataProvider → getUsedFieldsFromFilter: Added an additional check for tv names to prevent adverse joins in the query that could cause an irrelevant output.

## Version 0.4.0 (2016-01-20)
### General
* \+ Added extenders support.
* \- The “filterFieldDelimiter” parameter was removed.
* \+ The “fieldDelimiter” parameter was added to replace “filterFieldDelimiter”. It has absolutely the same purpose but it's more global. It affects all snippet parameters that can contain SQL queries.
* \* Fixed a plenty of bugs.

### Format\String
* \* Extenders data are now available via placeholders.
* \+ A new parameter called “noResults” has been added. It can be set as string or name of a chunk which will be used as output if no items are found.

## Version 0.3.2 (2015-10-11)
* \+ Added dd/composer-plugin-modxevo-library-ddtools-installer dependency.
* \+ The “itemNumber” and “itemNumberZeroBased” placeholders are now available within an item template.

## Version 0.3.1 (2015-10-11)
* \+ Added composer/installers dependency.

## Version 0.3.0 (2015-10-11)
* \+ Added Composer support.
* \+ The new $filterFieldDelimiter parameter has been added to the snippet to make possible to override the default '`' in filter queries
* \- The protected $filterFieldDelimiter member was removed from the basic abstract DataProvider class.
* \+ The new $filterFieldDelimiter parameter has been added to the getUsedFieldsFromFilter method of DataProvider.

## Version 0.2.0 (2015-09-25)
* \+ The new data provider “select” was added. It allows to fetch/filter/parse documents by their ids.
* \* Class members containing table names were moved from ddGetDocuments\DataProvider\Parent\DataProvider to ddGetDocuments\DataProvider along with their initialization.
* \* Fixed the mysql query of the select data provider. It didn't use to work properly if ids to select were not set while a filter was defined.
* \+ The class “ddGetDocuments\DataProvide\Output” has been added.
* \* The provider output format was changed. Data providers now must return a ddGetDocuments\DataProvide\Output.
* \* The “raw” output format has been changed completely.

## Version 0.1.0 (2015-09-23)
* \+ The first release.