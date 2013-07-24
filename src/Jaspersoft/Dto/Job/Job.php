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


/** 
 * The Job class is a holder for several properties and arrays
 * you must refer to documentation to understand how to properly construct a job object
 */
class Job {
/*
    public $alert = array();
	public $baseOutputFilename;
	public $repositoryDestination = array();
	public $creationDate = array();
	public $description;
	public $id;
	public $label;
	public $mailNotification = array();
	public $outputFormats = array();
	public $outputLocale;
	public $source = array();
	public $trigger = array();
	public $username;
	public $version = 0;
*/


	public function __construct($job_data = null) {
        if (empty($job_data))
            return null;
        foreach ($job_data as $k => $v) {
            $this->$k = $v;
        }
	}


}

?>