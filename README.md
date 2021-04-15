## Deprecated

This repository is no longer being actively maintained. We encourage you to not use this code.
If you rely on this code you might want to fork the repository to keep your systems from breaking, if we remove this repository in the future.

LswDoctrinePdoDblib
===================

Doctrine 2 does support any method of connecting to SQL Server on a Linux box. Here's a simple driver that supports PDO DBlib. Many tests fail, but most are related to shortcomings of the PDODBlib driver. There is a patch in the PHP repo to add transaction and lastInsertId support, but this package has some minor work arounds.

This bundle requires the following:
* pdo_dblib
* FreeTDS

FreeTDS configuration
---------------------
DBLib requires FreeTDS. We can't go into detail about configuring FreeTDS, but the connection configured should look something like following:

```
[mssql_freetds]
    host = 172.30.252.25
    port = 1433
    tds version = 8.0
    client charset = UTF-8
    text size = 20971520

```

Setting up bundle in Symfony
----------------------------

In your Symfony2 project, modify your `config.yml` as follows to use the DBlib bundle and the server setup under FreeTDS:

```yml
# Doctrine Configuration
doctrine:
    dbal:
        driver_class:   Lsw\DoctrinePdoDblib\Doctrine\DBAL\Driver\PDODblib\Driver
        host:           mssql_freetds

```

And in your `autoload.php` register the new bundle:
```php
$loader->registerNamespaces(array(
    'PDODblib'         => __DIR__ . '/../vendor/bundles',
));
```

Putting everything together
---------------------------
Getting everything together wasn't easy. You need to complete the following steps, checking each installation is successful by connecting with the appropriate tools:

* Install FreeTDS and configure a server connection 
    * *Verify* with `./tsql -S mssql_freetds -U yourusername -P yourpassword`
* Install the PHP DBLib extension -- verify with PHP script containing 
    * *Verify* `$pdo = new PDO('dblib:host=mssql_freetds;dbname=yourdb', 'yourusername', 'yourpassword');`
* Install and configure the PDODblibBundle 
    * *Verify* Some kind of SQL against your database


FYI - PHP pdo_dblib patch
-------------------------

You can find a patch for some of the short-comings of pdo_dblib on SVN.

http://svn.php.net/viewvc/php/php-src/trunk/ext/pdo_dblib/dblib_driver.c?view=log

See:
Revision 300647 - lastInsertId
Revision 300628 - transaction support

FYI - Doctrine Test Suite
-------------------------

Doctrine2's test suite does not allow you to add database drivers on the fly. If you want to test this package, modify `Doctrine/DBAL/Driver/DriverManager::$_driverMap` as follows:

```php
final class DriverManager
{
    private static $_driverMap = array(
		/* ... snip ... */
        'pdo_dblib'          => 'Lsw\DoctrinePdoDblib\Doctrine\DBAL\Driver\PDODblib\Driver',
    );
}
```

FYI - Generating Entities from database
---------------------------------------

It's possible, but not easy. Here's what I did:

- Map any non-compatible column types to string
- Hack the Doctrine core to skip any tables without primary keys


