<?php
namespace Jaspersoft\Dto\Report\InputControls;
use Jaspersoft\Dto\DTOObject;

/**
 * Class InputControlState
 *
 * @package Jaspersoft\Dto\Report\InputControls
 * @since 2.1.0
 */
class InputControlState extends DTOObject
{
    /**
     * @var string
     */
    public $uri;
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $value;
    /**
     * @var string
     */
    public $error;
    /**
     * @var array
     */
    public $options = array();

    public static function createFromJSON($json_obj)
    {
        $parent = parent::createFromJSON($json_obj);
        if (!empty($json_obj->options)) {
            unset($parent->options);
            foreach ($json_obj->options as $option) {
                $parent->options[] = (array) $option;
            }
        }
        return $parent;
    }

}