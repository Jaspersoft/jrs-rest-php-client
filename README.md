PHP REST Client for JasperReports Server
=======================================

Introduction
-------------
Using this library you can make requests and interact with the Jasper Reports Server through the REST API in native PHP. This allows you to more easily embed data from your report server, or perform administrative tasks on the server using PHP.

Requirements
-------------
To use this client, you will need:
- JasperReports Server (version >= 5.2)
- PHP (version >= 5.3, with cURL extension)
- Composer dependency manager <http://getcomposer.org/download> (Optional, but recommended)


Installation
-------------
Add the following to your composer.json file for your project, or run `php composer.phar reqiure jaspersoft/rest-client v2.0.0` in the directory of your project

    {
	    "require": {
		    "jaspersoft/rest-client": "v2.0.0"
	    }
    }

Or alternatively, download this package from github, and run `php composer.phar install` in the directory containing composer.json to generate the autoloader, then require the autoloader using

    require_once "vendor/autoload.php"
	
Additionally, a distributed autoloader is included if oyu want to simply include it in an existing project, or do not want to bother with Composer.

	require_once "autoload.dist.php"

Online Documentation
--------------------
Preview the [documentation online] (http://community.jaspersoft.com/wiki/php-client-sample-code) at the Jaspersoft Community website.

Security Notice
----------------
This package uses BASIC authentication to identify itself with the server. This package should only be used over a trusted connection between your report server and your web server.

PHPUnits
--------
The tests contained in this package are integration tests and are _not intended to be ran on a production server!_

License
--------
Copyright &copy; 2005 - 2014 Jaspersoft Corporation. All rights reserved.
http://www.jaspersoft.com.

Unless you have purchased a commercial license agreement from Jaspersoft,
the following license terms apply:

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Lesser  General Public License for more details.

You should have received a copy of the GNU Lesser General Public  License
along with this program. If not, see <http://www.gnu.org/licenses/>.
