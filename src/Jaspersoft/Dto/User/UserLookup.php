<?php
namespace Jaspersoft\Dto\User;

class UserLookup
{
    public $username;
    public $fullName;
    public $externallyDefined;
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