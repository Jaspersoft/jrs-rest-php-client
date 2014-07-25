<?php
namespace Jaspersoft\Dto\User;
use Jaspersoft\Dto\DTOObject;

/**
 * Class UserLookup
 * @package Jaspersoft\Dto\User
 */
class UserLookup extends DTOObject
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $fullName;
    /**
     * @var boolean
     */
    public $externallyDefined;
    /**
     * @var string
     */
    public $tenantId;

    public function __construct($username, $fullName, $externallyDefined, $tenantId = null)
    {
        $this->username = $username;
        $this->fullName = $fullName;
        $this->externallyDefined = $externallyDefined;
        $this->tenantId = $tenantId;
    }

    public function jsonSerialize()
    {
        return array(
            'username' => $this->username,
            'fullName' => $this->fullName,
            'externallyDefined' => $this->externallyDefined
        );
    }
}