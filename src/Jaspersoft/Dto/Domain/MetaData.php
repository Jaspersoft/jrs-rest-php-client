<?php


namespace Jaspersoft\Dto\Domain;


use Jaspersoft\Dto\DTOObject;
use Jaspersoft\Exception\DtoException;

class MetaData extends DTOObject
{
    /** @var MetaLevel */
    public $rootLevel;

    public static function createFromJSON($json_obj) {
        $result = new self();
        $result->rootLevel = MetaLevel::createFromJSON($json_obj->rootLevel);
        return $result;
    }

    public function jsonSerialize() {
        if ($this->rootLevel instanceof MetaLevel) {
            return array("rootLevel" => $this->rootLevel->jsonSerialize());
        } else {
            throw new DtoException("Unable to serialize MetaData object, invalid rootLevel data.");
        }

    }

} 