<?php
namespace Jaspersoft\Dto\User;

use Jaspersoft\Dto\Role\Role;

/* Jasper\User class
 * this class represents Users from the JasperServer and contains data that is
 * accessible via the user service in the REST API.
 *
 */
class User
{

	public $username;
	public $password;
	public $emailAddress;
	public $fullName;
	public $tenantId;
	public $roles = array();
	public $enabled;
	public $externallyDefined;
	public $previousPasswordChangeTime;

	/**
     * Constructor
	 *
	 * This constructor can be used to populate a User object from scratch
	 * any settings not set at construction can be configured using the SET methods below
	 */
	public function __construct($username = null, $password = null, $emailAddress = null, $fullName = null,
                                $tenantId = null, $enabled = null, $externallyDefined = null, $previousPasswordChangeTime = null)
	{
        $this->username = $username;
        $this->password = $password;
        $this->emailAddress = $emailAddress;
        $this->fullName = $fullName;
        $this->tenantId = $tenantId;
        $this->enabled = $enabled;
        $this->externallyDefined = $externallyDefined;
        $this->previousPasswordChangeTime = $previousPasswordChangeTime;
        $this->roles = array();
	}

    public function jsonSerialize()
    {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v) || $v === false) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
}
