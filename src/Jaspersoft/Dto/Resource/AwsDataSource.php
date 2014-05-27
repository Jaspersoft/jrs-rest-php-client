<?php
namespace Jaspersoft\Dto\Resource;

/**
 * Class AwsDataSource
 * @package Jaspersoft\Dto\Resource
 */
class AwsDataSource extends Resource
{
    public $driverClass;
    public $password;
    public $username;
    public $connectionUrl;
    public $accessKey;
    public $secretKey;
    public $roleArn;
    public $region;
    public $dbName;
    public $dbInstanceIdentifier;
    public $dbService;
    public $timezone;
}
