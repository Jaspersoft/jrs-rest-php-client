<?php


namespace Jaspersoft\Dto\Report\InputControls;


use Jaspersoft\Dto\DTOObject;

class InputControl extends DTOObject {

    /** @var  string */
    public $id;
    /** @var  string */
    public $description;
    /** @var  string */
    public $type;
    /** @var  string */
    public $uri;
    /** @var  string */
    public $label;
    /** @var  boolean */
    public $mandatory;
    /** @var  boolean */
    public $readOnly;
    /** @var  boolean */
    public $visible;
    /** @var  array */
    public $masterDependencies;
    /** @var  array */
    public $slaveDependencies;
    /** @var  array */
    public $validationRules;
    /** @var  InputControlState */
    public $state;

    public static function createFromJSON($json_obj)
    {
        $parent = parent::createFromJSON($json_obj);
        if (!empty($parent->state)) {
            $parent->state = InputControlState::createFromJSON($parent->state);
        }
        return $parent;
    }



} 