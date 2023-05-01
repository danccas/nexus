<?php

use Core\DB;

app()->library('Misc');
app()->library('db.pdo', 'DB');

app()->library('Pagination');
app()->library('Identify');
app()->library('Session');
app()->library('Tablefy');
app()->library('formity2');

app()->attr('root', dirname(__FILE__) . '/');
app()->attr('librarys', app()->attr('root') . 'app/Librarys/');
app()->attr('controllers', app()->attr('root') . 'app/Controllers/');
app()->attr('views', app()->attr('root') . 'resources/views/');

//DB::registerDSN('sutran', 'pgsql://postgres:meteLPBDo0gmsc3d@10.91.240.45:5432/sutran');

if (getenv('OS') == 'Windows_NT') {
  DB::createDSN('sutran', 'pgsql://postgres:meteLPBDo0gmsc3d@34.172.3.216:5432/sutran');
} else {
  DB::createDSN('sutran', 'pgsql://postgres:meteLPBDo0gmsc3d@10.91.240.45:5432/sutran');
}

define('ROL_ADMIN', 1);
define('ROL_INSTITUCION', 2);
define('ROL_EMV', 3);
define('ROL_CLIENTE', 4);
