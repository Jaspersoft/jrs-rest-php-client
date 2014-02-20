<?php

namespace Jaspersoft\Service\Result;
use Jaspersoft\Dto\Resource\ResourceLookup;


class SearchResourcesResult {

    public $items;
    public $resultCount;
    public $startIndex;
    public $totalCount;

    public function __construct($itemData, $resultCount = null, $startIndex = null, $totalCount = null)
    {
        $this->createItemsFromData($itemData);
        $this->resultCount = $resultCount;
        $this->startIndex = $startIndex;
        $this->totalCount = $totalCount;
    }

    public function createItemsFromData($itemData)
    {
        if ($itemData !== null) {
            foreach ($itemData->resourceLookup as $rl)
                $this->items[] = ResourceLookup::createFromJSON($rl);
        } else {
            $this->items = array();
        }
    }

}