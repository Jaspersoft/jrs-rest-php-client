<?php
namespace jaspersoft\criteria;


class RepositorySearchCriteria extends Criterion {

    protected $folderUri;
    protected $forceTotalCount;
    protected $limit;
    protected $offset;
    protected $query;
    protected $recursive;
    protected $showHiddenItems;
    protected $sortBy;
    protected $type;

    public function __construct() {

    }

    /**
     * @param mixed $folderUri
     */
    public function setFolderUri($folderUri)
    {
        $this->folderUri = $folderUri;
    }

    /**
     * @return mixed
     */
    public function getFolderUri()
    {
        return $this->folderUri;
    }

    /**
     * @param mixed $forceTotalCount
     */
    public function setForceTotalCount($forceTotalCount)
    {
        $this->forceTotalCount = $forceTotalCount;
    }

    /**
     * @return mixed
     */
    public function getForceTotalCount()
    {
        return $this->forceTotalCount;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return mixed
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param mixed $showHiddenItems
     */
    public function setShowHiddenItems($showHiddenItems)
    {
        $this->showHiddenItems = $showHiddenItems;
    }

    /**
     * @return mixed
     */
    public function getShowHiddenItems()
    {
        return $this->showHiddenItems;
    }

    /**
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }



}