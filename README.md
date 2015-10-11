# ddGetDocuments
A snippet for fetching and parsing resources from the document tree by a custom rule.

## Changelog
### Version 0.3.0 (2015-10-11)
* \+ Added Composer support.
* \+ The new $filterFieldDelimiter parameter has been added to the snippet to make possible to override the default '`' in filter queries
* \- The protected $filterFieldDelimiter member was removed from the basic abstract DataProvider class.
* \+ The new $filterFieldDelimiter parameter has been added to the getUsedFieldsFromFilter method of DataProvider.

### Version 0.2.0 (2015-09-25)
* \+ The new data provider “select” was added. It allows to fetch/filter/parse documents by their ids.
* \* Class members containing table names were moved from ddGetDocuments\DataProvider\Parent\DataProvider to ddGetDocuments\DataProvider along with their initialization.
* \* Fixed the mysql query of the select data provider. It didn't use to work properly if ids to select were not set while a filter was defined.
* \+ The class “ddGetDocuments\DataProvide\Output” has been added.
* \* The provider output format was changed. Data providers now must return a ddGetDocuments\DataProvide\Output.
* \* The “raw” output format has been changed completely.

### Version 0.1.0 (2015-09-23)
* \+ The first release.