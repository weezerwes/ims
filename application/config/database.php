<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'ci_series';
 * 
*/


$active_group = 'mysql';
$active_record = TRUE;
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'ci_series';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$db['inv']['hostname'] = 'inv02.stl1.prlss.net';
$db['inv']['username'] = 'root';
$db['inv']['password'] = '';
$db['inv']['database'] = 'inventory';
$db['inv']['dbdriver'] = 'mysql';
$db['inv']['dbprefix'] = '';
$db['inv']['pconnect'] = FALSE;
$db['inv']['db_debug'] = FALSE;
$db['inv']['cache_on'] = FALSE;
$db['inv']['cachedir'] = '';
$db['inv']['char_set'] = 'utf8';
$db['inv']['dbcollat'] = 'utf8_general_ci';
$db['inv']['swap_pre'] = '';
$db['inv']['autoinit'] = TRUE;
$db['inv']['stricton'] = FALSE;

$db['mysql']['hostname'] = 'localhost';
$db['mysql']['username'] = 'root';
$db['mysql']['password'] = '';
$db['mysql']['database'] = 'inventory';
$db['mysql']['dbdriver'] = 'mysql';
$db['mysql']['dbprefix'] = '';
$db['mysql']['pconnect'] = FALSE;
$db['mysql']['db_debug'] = FALSE;
$db['mysql']['cache_on'] = FALSE;
$db['mysql']['cachedir'] = '';
$db['mysql']['char_set'] = 'utf8';
$db['mysql']['dbcollat'] = 'utf8_general_ci';
$db['mysql']['swap_pre'] = '';
$db['mysql']['autoinit'] = TRUE;
$db['mysql']['stricton'] = FALSE;

$db['school']['hostname'] = 'localhost';
$db['school']['username'] = 'cct5';
$db['school']['password'] = 'cct5db2012';
$db['school']['database'] = 'inventory';
$db['school']['dbdriver'] = 'mysql';
$db['school']['dbprefix'] = '';
$db['school']['pconnect'] = FALSE;
$db['school']['db_debug'] = FALSE;
$db['school']['cache_on'] = FALSE;
$db['school']['cachedir'] = '';
$db['school']['char_set'] = 'utf8';
$db['school']['dbcollat'] = 'utf8_general_ci';
$db['school']['swap_pre'] = '';
$db['school']['autoinit'] = TRUE;
$db['school']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */