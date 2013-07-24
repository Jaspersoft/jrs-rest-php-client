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
namespace Jasper;

    /* Constants
     *
     * contains constants to be used by the JasperClient class
     *
     * author: gbacon
     * date: 06/06/2012
     */

// ASCII URL codes
const PIPE = "%7C";
const AMPERSAND = "%26";


// URLs
const PROTOCOL = "http://";
const BASE_REST_URL = "/rest";
const BASE_REST2_URL = "/rest_v2";
const ATTRIBUTE_BASE_URL  = "/attribute";
const ATTRIBUTE_2_BASE_URL = "/attributes";
const USER_BASE_URL = "/user";
const USER_2_BASE_URL = "/users";
const ORGANIZATION_BASE_URL = "/organization";
const ORGANIZATION_2_BASE_URL = "/organizations";
const ROLE_BASE_URL = "/role";
const ROLE_2_BASE_URL = "/roles";
const JOB_2_BASE_URL = "/jobs";
const REPORTS_BASE_URL = "/reports";
const LOGIN_BASE_URL = "/login";
const REPORT_EXECUTIONS_BASE_URL = '/reportExecutions';
const EXPORT_BASE_URL = '/export';
const IMPORT_BASE_URL = '/import';
const QUERY_EXECUTOR_BASE_URL = '/queryExecutor';

// URL FLAGS
const STATUS_FLAG = '/status';
const EXPORTS_FLAG = '/exports';
const ATTACHMENTS_FLAG = '/attachments';
const OUTPUT_RESOURCE_FLAG = 'outputResource';


// XML
const ENTITY_RESOURCE_ROOT_XML = "<entityResource></entityResource>";
const USER_RESOURCE_ROOT_XML = "<user></user>";

?>