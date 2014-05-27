<?php
namespace Jaspersoft\Dto\Permission;

/**
 * Class RepositoryPermission
 * @package Jaspersoft\Dto\Permission
 */
class RepositoryPermission
{
    /**
     * URI of resource the permission is related to
     * @var string
     */
    public $uri;
    /**
     * Descriptor of user or role for which permission belongs
     * @var string
     */
    public $recipient;
    /**
     * A numerical descriptor of the permissions granted
     * @var int
     */
    public $mask;

	public function __construct($uri, $recipient, $mask)
    {
		$this->uri = $uri;
		$this->recipient = $recipient;
		$this->mask = $mask;
	}
	
	public function jsonSerialize()
    {
		$data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (isset($v)) {
                $data[$k] = $v;
            }
        }
        return $data;
	}

    public static function createFromJSON($json_data)
    {
        $perm = json_decode($json_data);
        return new self($perm->uri, $perm->recipient, $perm->mask);
    }
}