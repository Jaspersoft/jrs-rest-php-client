<?php
namespace Jaspersoft\Dto\Organization;

/* Jasper\Organization class
 * this class represents Organizations from the JasperServer and contains data that is
 * accessible via the user service in the REST API.
 *
 * author: gbacon
 * date: 06/07/2012
 */
class Organization
{
	public $alias;
	public $id;
	public $parentId;
	public $tenantName;
	public $theme;
	public $tenantDesc;
	public $tenantFolderUri;
	public $tenantNote;
	public $tenantUri;

	public function __construct(
		$alias = null,
		$id = null,
		$parentId = null,
		$tenantName = null,
		$theme = null,
		$tenantDesc = null,
		$tenantFolderUri = null,
		$tenantNote = null,
		$tenantUri = null)
	{
        $this->alias = $alias;
        $this->id = $id;
        $this->parentId = $parentId;
        $this->tenantName = $tenantName;
        $this->theme = $theme;
        $this->tenantDesc = $tenantDesc;
        $this->tenantFolderUri = $tenantFolderUri;
        $this->tenantNote = $tenantNote;
        $this->tenantUri = $tenantUri;
	}

    public function jsonSerialize()
    {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }

}
