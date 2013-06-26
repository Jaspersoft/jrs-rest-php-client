PHP REST Wrapper for JRS
=======================================

Introduction
-------------
Using this library you can make requests and interact with the Jasper Reports Server through the REST API in native PHP. This allows you to more easily embed data from your report server, or perform administrative tasks on the server using PHP.

Requirements
-------------
To use this wrapper, you will need:
- PHP (version >=5.3)
- PEAR Package Manager (http://pear.php.net)
- XML_Serializer PEAR package (dependency soon to be removed!)
- PHPUnit PEAR package (for testing)

Installation
-------------
Be sure your php.ini is configured properly to use the proper timezone settings.

Security Notice
----------------
This package uses BASIC authentication to identify itself with the server. This package should only be used over a trusted connection between your report server and your web server.

PHPUnits
--------
The PHPUnits are provided both as an example of code, and to test for quality assurance. It is *not advised to run these tests on a production server!!* As some of the features tested do overwrite existing data (import/export service specifically)

