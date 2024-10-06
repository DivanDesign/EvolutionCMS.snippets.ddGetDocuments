# (MODX)EvolutionCMS.snippets.ddGetDocuments changelog


## Версия 1.7 (2024-10-06)

* \+ Outputters → Json → Параметры:
	* \+ `outputterParams->templates->{$docFieldName}` → Плейсхолдеры → `[+itemNumber+]`, `[+itemNumberZeroBased+]`: Новые плейсхолдеры.
	* \+ `outputterParams->docFields[i]`: Добавлена возможность использовать кастомные псевдонимы вместо имён полей для вывода, используя разделитель `'='`, например: `'pagetitle=title'`, `'content=text'`, etc. (см. README → Примеры).
* \* `\ddTools::getTpl` используется вместо `$modx->getTpl` (стало чуть меньше багов).
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.62.


## Версия 1.6 (2022-09-30)

* \+ Outputters → Json → Параметры → `outputterParams->templates->{$docFieldName}` → Плейсхолдеры: Новые плейсхолдеры. Содержат значения полей документа (и TV), указанных в `outputterParams->docFields`.
* \* Outputters → Yandexmarket: Исправлена критическая ошибка, связанная с отсутствием инициализации поля объекта.


## Версия 1.5 (2022-06-03)

* \+ Параметры → `providerParams->groupBy`: Новый параметр. Позволяет сгруппировать элементы, имеющие одинаковые значения полей, в один сводный элемент (как SQL `GROUP BY`). См. README.
* \* README → Примеры: HJSON используется для всех примеров.


## Версия 1.4 (2021-07-27)

* \* Внимание! Требуется PHP >= 5.6.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.50.
* \+ Параметры → `providerParams`, `outputterParams`, `extendersParams`: Также могут быть заданы, как [HJSON](https://hjson.github.io/) или как нативный PHP объект или массив (например, для вызовов через `$modx->runSnippet`).
* \+ Запустить сниппет без DB и eval можно через `\DDTools\Snippet::runSnippet` (см. примеры в README).
* \+ Outputters → Json → Параметры → `outputterParams->templates`: Новые параметры. Вы можете использовать шаблоны для нужных полей документов.
* \* Outputters → String → Параметры → `outputterParams->templates`: Следующие параметры перемещены сюда (с обратной совместимостью):
	* \* `outputterParams->itemTpl` → `outputterParams->templates->item`.
	* \* `outputterParams->itemTplFirst` → `outputterParams->templates->itemFirst`.
	* \* `outputterParams->itemTplLast` → `outputterParams->templates->itemLast`.
	* \* `outputterParams->wrapperTpl` → `outputterParams->templates->wrapper`.
	* \* `outputterParams->noResults` → `outputterParams->templates->noResults`.
* \* Outputters → Sitemap → Параметры → `outputterParams->templates`: Следующие параметры перемещены сюда (с обратной совместимостью):
	* \* `outputterParams->itemTpl` → `outputterParams->templates->item`.
	* \* `outputterParams->wrapperTpl` → `outputterParams->templates->wrapper`.
* \+ Документация → Установка → Используя (MODX)EvolutionCMS.libraries.ddInstaller.
* \+ Composer.json.
	* \+ `support`.
	* \+ `authors`: Добавлены недостающие авторы.


## Версия 1.3.1 (2021-02-28)

* \* Outputters → String → Параметры → `outputterParams->placeholders`: Исправлена критическая ошибка, когда параметр используется.


## Версия 0.1 (2015-09-23)

* \+ Первый релиз.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>