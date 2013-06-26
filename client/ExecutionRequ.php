<?php
/* ==========================================================================

Copyright (C) 2005 - 2012 Jaspersoft Corporation. All rights reserved.
http://www.jaspersoft.com.

Unless you have purchased a commercial license agreement from Jaspersoft,
the following license terms apply:

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero  General Public License for more details.

You should have received a copy of the GNU Affero General Public  License
along with this program. If not, see <http://www.gnu.org/licenses/>.

=========================================================================== */
/** Report Execution Request
 * This object forms a request for a Report Execution
 * @author gbacon
 */

namespace Jasper;


class ExecutionRequest implements \JsonSerializable {

    public $reportUnitUri;
    public $async;
    public $outputFormat;
    public $interactive;
    public $freshData;
    public $saveDataSnapshot;
    public $ignorePagination;
    public $transformerKey;
    public $pages;
    public $attachmentsPrefix;
    public $parameters;

    public function jsonSerialize() {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    public function setParameters(array $parameters) {
        $this->parameters = $parameters;
    }
    public function getParameters() {
        return $this->parameters;
    }

    public function __construct($reportUnitUri, $outputFormat) {
        $this->reportUnitUri = $reportUnitUri;
        $this->outputFormat = $outputFormat;
    }

}