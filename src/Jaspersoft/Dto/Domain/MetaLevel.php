<?php


namespace Jaspersoft\Dto\Domain;


class MetaLevel extends AbstractMetaEntity {

    /** @var  array */
    public $items;
    /** @var  array */
    public $subLevels;

    public static function createFromJSON($json_obj) {
        $parent = parent::createFromJSON($json_obj);
        if (!empty($json_obj->items)) {
            unset($parent->items);
            foreach ($json_obj->items as $element) {
                $parent->items[] = MetaItem::createFromJSON($element);
            }
        }

        if (!empty($json_obj->subLevels)) {
            unset($parent->subLevels);
            foreach ($json_obj->subLevels as $element) {
                $parent->subLevels[] = MetaLevel::createFromJSON($element);
            }
        }

        return $parent;
    }

    public function jsonSerialize()
    {
        $parent = parent::jsonSerialize();
        if (!empty($this->subLevels) && is_array($this->subLevels)) {
            unset($parent['subLevels']);
            foreach ($this->subLevels as $level) {
                if ($level instanceof MetaLevel) {
                    $parent['subLevels'][] = $level->jsonSerialize();
                }
            }
        }

        if (!empty($this->items) && is_array($this->items)) {
            unset($parent['items']);
            foreach ($this->items as $item) {
                if ($item instanceof MetaItem) {
                    $parent['items'][] = $item->jsonSerialize();
                }
            }
        }

        return $parent;
    }

} 