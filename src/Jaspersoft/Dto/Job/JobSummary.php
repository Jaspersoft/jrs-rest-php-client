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

namespace Jaspersoft\Dto\Job;

class JobSummary {

	public $id;
	public $label;
	public $reportUnitURI;
	public $state = array();
	public $version;
	public $owner;

	public function __construct($id, $label, $reportUnitURI, $version, $owner, $state, $nextFireTime = null, $previousFireTime = null) {
		$this->id = strval($id);
		$this->label = strval($label);
		$this->reportUnitURI = strval($reportUnitURI);
		$this->state['value'] = strval($state);
		$this->version = strval($version);
		$this->owner = strval($owner);
		if (!empty($nextFireTime)) {
			$this->state['nextFireTime'] = strval($nextFireTime);
		}
		if (!empty($previousFireTime)) {
			$this->state['previousFireTime'] = strval($previousFireTime);
		}
	}

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'version' => $this->version,
            'reportUnitURI' => $this->reportUnitURI,
            'label' => $this->label,
            'owner' => $this->owner,
            'state' => $this->state
        );
    }

	public function getId() { return $this->id; }
	public function getLabel() { return $this->label; }
	public function getReportUnitURI() { return $this->reportUnitURI; }
	public function getState() { return $this->state['value']; }
	public function getVersion() { return $this->version; }
	public function getNextFireTime() { return $this->state['nextFireTime']; }
	public function getPreviousFireTime() { return $this->state['previousFireTime']; }

}

?>