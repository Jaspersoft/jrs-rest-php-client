<?php
namespace Jaspersoft\Dto\Resource;

/**
 * Class ReportUnit
 * @package Jaspersoft\Dto\Resource
 */
class ReportUnit extends CompositeResource
{

    public $alwaysPromptControls;
    public $controlsLayout;
    public $inputControlRenderingView;
    public $reportRenderingView;
    public $dataSnapshotId;
    public $dataSource;
    public $query;
    public $jrxml;
    public $inputControls;
    public $resources;

    public static function createFromJSON($json_data, $type = null)
    {
        // Handle resources here as a special case
        if (!empty($json_data['resources']['resource'])) {
            $json_data['resources'] = $json_data['resources']['resource'];
            return parent::createFromJSON($json_data, $type);
        } else {
            return parent::createFromJSON($json_data, $type);
        }
    }

    public function jsonSerialize()
    {
        if (!empty($this->resources)) {
            $parent = parent::jsonSerialize();
            $parent_resources = $parent['resources'];
            unset($parent['resources']);
            $parent['resources']['resource'] = $parent_resources;
            return $parent;
        } else {
            return parent::jsonSerialize();
        }
    }
}