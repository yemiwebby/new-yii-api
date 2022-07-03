<?php

$db = require __DIR__ . '/db.php';

$db['dsn'] = 'mysql:host=127.0.0.1;dbname=auth_api_test';
$db['password'] = 'secret';

return $db;