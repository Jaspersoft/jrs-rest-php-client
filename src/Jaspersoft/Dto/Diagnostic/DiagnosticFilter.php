<?php

namespace Jaspersoft\Dto\Diagnostic;


use Jaspersoft\Dto\DTOObject;

class DiagnosticFilter extends DTOObject
{
    public $userId;
    public $sessionId;
    public $resource;

    public static function createFromJSON($json_obj)
    {
        $obj = parent::createFromJSON($json_obj);
        $obj->resource = DiagnosticFilterResource::createFromJSON($json_obj->resource);
    }

    public function toJSON() {

    }
}