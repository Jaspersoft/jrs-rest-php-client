<?php
namespace Jaspersoft\Dto\Resource;

/**
 * Class InputControl
 * @package Jaspersoft\Dto\Resource
 */
class InputControl extends CompositeResource
{
    public $mandatory;
    public $readOnly;
    public $visible;
    public $type;
    public $dataType;
    public $listOfValues;
    public $visibleColumns;
    public $valueColumn;
    public $query;
}