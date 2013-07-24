PHP REST Client for JRS
=======================================

Introduction
-------------
Using this library you can make requests and interact with the Jasper Reports Server through the REST API in native PHP. This allows you to more easily embed data from your report server, or perform administrative tasks on the server using PHP.

Requirements
-------------
To use this client, you will need:
- JasperReports Server (version >= 5.2)
- PHP (version >= 5.3)
- Composer dependency manager (http://getcomposer.org/download)


Installation
-------------
Add the following to your composer.json file for your project

    {
	    "require": {
		    "jaspersoft/rest_client": ">=2.0"
	    }
    }

Or alternatively, download this package from github, and run `php composer.phar install` in the directory containing composer.json to generate the autoloader, then require the autoloader using
    require_once "vendor/autoload.php"


Security Notice
----------------
This package uses BASIC authentication to identify itself with the server. This package should only be used over a trusted connection between your report server and your web server.

PHPUnits
--------
The tests contained in this package are integration tests and are _not intended to be ran on a production server!_

