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
/** Execution class - Stores metadata for a report execution
 *
 * @author gbacon
 */

namespace Jasper;

class Execution implements \JsonSerializable {

    public $status;
    public $totalPages;
    public $currentPage;
    public $errorDescriptor;
    public $reportURI;
    public $requestId;
    public $exports;


    public function jsonSerialize() {

    }

    public function __construct() {

    }

    public static function createFromJSON($json_data) {
        $data = json_decode($json_data, true);
        $result = new self();
        foreach ($data as $k => $v)
            $result->$k = $v;
        return $result;
    }


}