<?php
namespace Jaspersoft\Dto\Role;

class Role
{

	public $name;
	public $tenantId;
	public $externallyDefined;

	public function __construct($name = null, $tenantId = null, $externallyDefined = null)
	{
        $this->name = $name;
        $this->externallyDefined = $externallyDefined;
        $this->tenantId = $tenantId;
	}

    public function jsonSerialize()
    {
        return array(
            'name' => $this->name,
            'externallyDefined' => $this->externallyDefined
        );
    }
}
