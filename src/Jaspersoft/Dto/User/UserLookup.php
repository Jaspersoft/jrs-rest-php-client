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
/**
 * UserLookup object
 * this object represents a summary user object that is found when using searchUsers
 *
 * It is not a full representation of a User. You must use getUser
 */

namespace Jaspersoft\Dto\User;


class UserLookup {
    public $username;
    public $fullName;
    public $externallyDefined;
    public $tenantId;

    public function __construct($username, $fullName, $externallyDefined, $tenantId = null) {
        $this->username = $username;
        $this->fullName = $fullName;
        $this->externallyDefined = $externallyDefined;
        $this->tenantId = $tenantId;
    }

    public function jsonSerialize() {
        return array(
            'username' => $this->username,
            'fullName' => $this->fullName,
            'externallyDefined' => $this->externallyDefined
        );
    }



}