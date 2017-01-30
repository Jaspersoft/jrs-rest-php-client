<?php

namespace Jaspersoft\Dto\Role;

use Jaspersoft\Dto\DTOObject;

/**
 * Class Role
 * @package Jaspersoft\Dto\Role
 */
class Role extends DTOObject
{
    /**
     * Role name
     * @var string
     */
    public $name;

    /**
     * Organization name role may belong to
     * @var string
     */
    public $tenantId;

    /**
     * @var boolean
     */
    public $externallyDefined;

    public function __construct($name = null, $tenantId = null,
                                $externallyDefined = null)
    {
        $this->name              = $name;
        $this->externallyDefined = $externallyDefined;
        $this->tenantId          = $tenantId;
    }

    public function jsonSerialize()
    {
        return array(
            'name' => $this->name,
            'externallyDefined' => $this->externallyDefined
        );
    }
}