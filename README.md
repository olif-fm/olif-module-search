#OLIF Module: Search

Módulo para aplicar filtros a los listados


## Usage

```php
$dev->getControllerModule('search', 'ControllerSearch', 'search');

...

$dev->search->setFilter("f_mat_types", "f_mat_types", "F_MAT_TYPES", "P.FK_TYPE = ?", array(
    ""
));

$filters['COND'] = $dev->search->getQuery(true, false);
$filters['PARAMS'] = $dev->search->getParams();
$isSearching = $dev->search->isSearching();

```

## Authors

[Jose Luis Represa](https://github.com/josex2r)

[Alberto Vara](https://github.com/avara1986)

##License

Olif-module-search is released under the [MIT License](http://opensource.org/licenses/MIT).
