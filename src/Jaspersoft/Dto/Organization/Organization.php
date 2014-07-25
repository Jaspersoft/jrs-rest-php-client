<?php
namespace Jaspersoft\Dto\Organization;
use Jaspersoft\Dto\DTOObject;

/**
 * Class Organization
 * @package Jaspersoft\Dto\Organization
 */
class Organization extends DTOObject
{
    /**
     * @var string
     */
    public $alias;
    /**
     * Read-only internal ID of organization
     * @var string
     */
    public $id;
    /**
     * Read-only internal ID of parent organization
     * @var string
     */
    public $parentId;
    /**
     * @var string
     */
    public $tenantName;
    /**
     * @var string
     */
    public $theme;
    /**
     * @var string
     */
    public $tenantDesc;
    /**
     * @var string
     */
    public $tenantFolderUri;
    /**
     * @var string
     */
    public $tenantNote;
    /**
     * @var string
     */
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

}
