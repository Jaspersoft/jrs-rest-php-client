<?php
namespace Jaspersoft\Dto\ImportExport;
use Jaspersoft\Dto\DTOObject;

/**
 * Class ImportTask
 * Define an import task to be executed
 *
 * @package Jaspersoft\Dto\ImportExport
 */
class ImportTask extends DTOObject
{
    /**
     * @var boolean
     */
    public $update;
    /**
     * @var boolean
     */
    public $skipUserUpdate;
    /**
     * @var boolean
     */
    public $includeAccessEvents;
    /**
     * @var boolean
     */
    public $includeAuditEvents;
    /**
     * @var boolean
     */
    public $includeMonitoringEvents;
    /**
     * @var boolean
     */
    public $includeServerSettings;

    public function queryData()
    {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v) && $v == true) {
                $data[$k] = 'true';
            } elseif (!empty($v)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }

}