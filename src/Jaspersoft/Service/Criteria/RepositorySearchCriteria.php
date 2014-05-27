<?php
namespace Jaspersoft\Service\Criteria;

class RepositorySearchCriteria extends Criterion
{

    public $folderUri;
    public $forceTotalCount;
    public $limit;
    public $offset;
    public $q;
    public $recursive;
    public $showHiddenItems;
    public $sortBy;
    public $type;

    public function __construct($q = null)
    {
		$this->q = $q;
    }

}