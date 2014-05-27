<?php
namespace Jaspersoft\Dto\ImportExport;

class ImportTask
{
    public $update;
    public $skipUserUpdate;
    public $includeAccessEvents;
    public $includeAuditEvents;
    public $includeMonitoringEvents;
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