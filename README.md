PlugQuery
=========

PHP Database Equilent Query Builder ORM Layer

Development version, do not use in production.


Query/Builder
```
<?php 
use PlugQuery\Query;
Query::select('account')->where('id', 1)->or->where('id', 2);
// Result: SELECT account.* FROM account WHERE id = ? OR id = ?
?>
```