<!DOCTYPE html>

<?php
	function inject_sample($file) {
		$dirfile = dirname(__FILE__) . '/'. $file;
		if(empty($dirfile) || !file_exists($dirfile)) { 
			printf('Cannot open sample code: %s', $dirfile);
			return false;
		}
		$fh = fopen($dirfile, 'r')
			or printf('Cannot open sample code');
		$contents = fread($fh, filesize($dirfile));
		echo htmlentities($contents);
		fclose($fh);
	}
?>

<html>
  <head>
    <title>Documentation</title>

    <meta charset='utf-8'>
	<link rel="stylesheet" href="highlight/styles/github.css">

  <style>
  .deprecated_notice { color: #f00; font-style: italic; }
  #section_group { font-size: 32pt; }
  </style>

  </head>
  <body>

      <header id='intro'>
        <h1 class='introduction'>
			Documentation for JasperReports Server PHP Client
        </h1>
        <p>
			Jaspersoft Corporation, &copy; 2014
		  <br>
		  <br>
			Code highlighting for this page provided by <a href="http://softwaremaniacs.org/soft/highlight/en/">highlight.js</a>, written by Ivan Sagalaev (<a href="highlight/LICENSE">license</a>/<a href="highlight/AUTHORS.en.txt">authors</a>).
        </p>
      </header>

	<nav>
	<h1> Table of Contents </h1>
	<ul></ul>
	</nav>

	<section class='about'>
		<article id='about_class'>
			<h3> About the class </h3>
			<p>
			   The JasperReports PHP Client is a wrapper for the JasperReports Web Services REST API. This client abstracts the details behind the communications with the server
			   into simple to use functions representative of their functionality on the server. Users do not have to deal with parsing and creating JSON data, but rather deal with
			   objects that represent objects within the server.<br>
			   Use of this wrapper is intented to allow users to focus on their logic and implementation rather than detailed communications with a server.
			</p>
		</article>
	</section>

	<section class='examples'>

<!-- skeleton
	<article id="">
		<h3>  </h3>
		<p>

		</p>
		<pre><code><?php inject_sample(''); ?>
		</code>
		</pre>
	</article>
end of skeleton -->


	<h2 id='prep'> Preparing the class </h2>

	<article>
		<h3> Invoking the client </h3>
		<p>
			In your project, you must include the autoloader so that classes can be loaded as needed. If you have not generated the autoloader file, please follow the instructions in the README for installation. Then you must initialize a client with your login credentials.
			In addition, please note that the examples below assume you are importing objects using the PHP 'use' keyword. If you are not, you must refer to the objects using the full qualified namespace.
		</p>

		<pre><code><?php inject_sample('code/client_invokation.txt'); ?>
		</code>
		</pre>
	</article>

    <article>
        <h3> Altering the request timeout </h3>
        <p>
            If you are having issues with requests timing out, you can alter the amount of time the client waits for a response
            before timing out. The default timeout is 30 seconds.
        </p>

            <pre><code><?php inject_sample('code/client_timeout.txt'); ?>
            </code>
            </pre>
    </article>


	<article>
		<h3> Server Information </h3>
		<p>
			The client class can also return data about the sever it is connecting to. This data includes date formats, license information and other info about the server's configruation. It is returned in an associative array format.
		</p>

		<pre><code><?php inject_sample('code/server_info.txt'); ?>
		</code>
		</pre>
	</article>

	<h2 id='service_access'> Available Services </h2>

	<article id="service_access">
		<h3> List of Services </h3>
		<p>
		There are many services exposed through the client. Each can be requested as an object or accessed through the client itself each time.
		
		<ul>
			<li> importExportService </li>
			<li> jobService </li>
			<li> optionsService </li>
			<li> organizationService </li>
			<li> permissionService </li>
			<li> queryService </li>
			<li> reportService </li>
			<li> repositoryService </li>
			<li> roleService </li>
			<li> userService </li>
		</ul>
		</p>
		<pre><code><?php inject_sample('code/service_access.txt'); ?>
		</code>
		</pre>
	</article>
    <h1 id="section_group"> Administration Services </h1>
<h2 id="user_service"> User Service </h2>

<article id="search_user">
    <h3> Search User </h3>
    <p>
        Using the searchUsers method you can search for several users based on various critera. This method will return an array of UserLookup objects that can be used with the getUserByLookup() function to retrieve their fully described User objects. The example below grabs all users containing 'j' in their username, and that are members of organization_1 and prints out the roles assigned to that user.
    </p>
		<pre><code><?php inject_sample('code/search_user.txt'); ?>
        </code>
		</pre>
</article>

<article id="create_user">
    <h3> Create User </h3>
    <p>
        To create a user, define a user object which fully describes the user, use it with the addOrUpdateUser function.
    </p>
		<pre><code><?php inject_sample('code/create_user.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_user">
    <h3> Update User </h3>
    <p>
        To update a user, you can also use the addOrUpdateUser function.
    </p>
		<pre><code><?php inject_sample('code/update_user.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_user">
    <h3> Delete User </h3>
    <p>
        To delete a user, obtain its user object from the server, then pass it to the deleteUser function.
    </p>
		<pre><code><?php inject_sample('code/delete_user.txt'); ?>
        </code>
		</pre>
</article>

	<article id="attribute_functions">
		<h3> Get Attributes </h3>
		<p>
			Using this function, you can request the attributes of a user. You are able to specifiy specific attributes that you wish to get, otherwise all attributes for user will be returned.
			You must supply a User object representing the user you wish to find the attributes of.
		</p>
		<pre><code><?php inject_sample('code/get_attributes.txt'); ?>
		</code>
		</pre>
	</article>

	<article id="add_attributes">
		<h3> Add Attributes </h3>
		<p>
			addOrUpdateAttribute can be used to create or update an attribute for a user.
		</p>
		<pre><code><?php inject_sample('code/add_attributes.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="delete_attributes">
		<h3> Delete Attributes </h3>
		<p>
			To remove attributes of a user, you must supply a User object of the user to remove from, and an array of the names of the attributes (Not attribute objects) If no attribute names are given, all attributes will be removed.
		</p>
		<pre><code><?php inject_sample('code/delete_attributes.txt'); ?>
		</code>
		</pre>
	</article>
<h2 id="organization_service"> Organization Service </h2>

<article id="create_organization">
    <h3> Create Organization </h3>
    <p>
        Creating a new organization requires constructing a new Organization object and sending it using the createOrganization function.
    </p>
		<pre><code><?php inject_sample('code/create_organization.txt'); ?>
        </code>
		</pre>
</article>

<article id="search_organization">
    <h3> Searching Organizations </h3>
    <p>
        Using the searchOrganization function you can search for organizations by ID.
    </p>
		<pre><code><?php inject_sample('code/search_organization.txt'); ?>
        </code>
		</pre>
</article>

<article id="get_organization">
    <h3> Get Organization </h3>
    <p>
        Using the getOrganization function you can request the data about a specific organization for which you know
        the ID of.
    </p>
            <pre><code><?php inject_sample('code/get_organization.txt'); ?>
            </code>
            </pre>
</article>

<article id="delete_organization">
    <h3> Deleting Organizations </h3>
    <p>
        An organization may be deleted by providing the Organization Object that correlates to the organization that is to be deleted. This can be retrieved as shown below by using the searchOrganizations() method.
    </p>
		<pre><code><?php inject_sample('code/delete_organization.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_organization">
    <h3> Updating Organizations </h3>
    <p>
        Modifying an organization is done in a similar fashion to modifying a user. The organization object needs to be obtained with the searchOrganization method, modified, and then return it back to the server as shown below.
    </p>
		<pre><code><?php inject_sample('code/update_organization.txt'); ?>
        </code>
		</pre>
</article>

<h2 id="role_service"> Role Service </h2>


<article id="get_many_roles">
    <h3> Get Many Roles </h3>
    <p>
        You can request all the roles of an organization, or of the server using this function. The example below
        will request all roles on the server. Optionally, you can search based on specific criteria for roles.
    </p>
		<pre><code><?php inject_sample('code/get_many_roles.txt'); ?>
        </code>
		</pre>
</article>

<article id="get_role">
    <h3> Get a Specific Role </h3>
    <p>
        If you know the name of the role, you can request it specifically using this function. The example below
        will request the ROLE_USER data.
    </p>
		<pre><code><?php inject_sample('code/get_role.txt'); ?>
        </code>
		</pre>
</article>

<article id="create_role">
    <h3> Create Role </h3>
    <p>
        Creating a role requires you to describe the role with an object, then pass it to the server. The example below will create a new role for organization_1.
    </p>
		<pre><code><?php inject_sample('code/create_role.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_role">
    <h3> Update Role </h3>
    <p>
        Updating a role requires you to provide an updated model of the new role. If changing the name of the role, you must pass the old name of the role as the second argument.
    </p>
		<pre><code><?php inject_sample('code/update_role.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_role">
    <h3> Delete Role </h3>
    <p>
        Removing a role requires you to retrieve the role object representing the role you wish to remove, then pass it to the deleteRole function.
    </p>
		<pre><code><?php inject_sample('code/delete_role.txt'); ?>
        </code>
		</pre>
</article>

<h1 id="section_group"> Reporting Services </h1>
<h2 id="job_service"> Job Service </h2>
	
	<article id="search_jobs">
		<h3> Search Jobs </h3>
		<p>
			Using the searchJobs function, you can search for existing jobs. There are various options that can be set to filter your results. With no options set, you will recieve all existing jobs on the server.
			The example below will search for all jobs schedule for the report at the URI "/reports/samples/AllAccounts"
		</p>
		<pre><code><?php inject_sample('code/search_jobs.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="get_job">
		<h3> Get Job by ID </h3>
		<p>
			If you know the ID of a job, you can request the specific details of that job. All results for searchJobs are JobSummary objects which contain IDs. Also when you create a new job, the ID will be returned in the response data.
			You can use these IDs with this function to request the details of the jobs.
		</p>
		<pre><code><?php inject_sample('code/get_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="create_job">
		<h3> Create Job </h3>
		<p>
			Creating a job requires you to create a well-defined Job object. Job objects consist of subclasses that
            define the behaviour of the scheduled job.
		</p>
		<pre><code><?php inject_sample('code/create_job.txt'); ?>
		</code>
		</pre>
	</article>	
	
	<article id="update_job">
		<h3> Updating Job </h3>
		<p>
			To update a scheduled job, simply request the old job object from the server, modify it, and then use the updateJob() function to reupload it to the server to be updated. The Job class utilizes properties and arrays to manage its data, which is different from the other objects which use only properties. This means you will not use get/set methods to alter the data in a Job object, but rather set the properties as if they were variables. If a property refers to a nested element of the job class, use array functions to manipulate the arrays. 
		</p>
		<pre><code><?php inject_sample('code/update_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="pause_job">
		<h3> Pause Job </h3>
		<p>
			Jobs can be paused using the pauseJob function. The only argument for this function accepts either a single job ID as an integer, an array of job IDs; or, if no argument is provided all jobs will be paused. 
		</p>
		<pre><code><?php inject_sample('code/pause_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="resume_job">
		<h3> Resume Job </h3>
		<p>
			To resume a job, pass the job's ID to the resumeJob function. For convenience you may also pass an array of job IDs. Similarly, all jobs will be resumed if no IDs are provided.
		</p>
		<pre><code><?php inject_sample('code/resume_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="delete_job">
		<h3> Delete Job </h3>
		<p>
			To delete a job, search for the job by name or URI, then hand its ID to the deleteJob function. The example below will delete all scheduled jobs on the server. 
		</p>
		<pre><code><?php inject_sample('code/delete_job.txt'); ?>
		</code>
		</pre>
	</article>
	
		<h2 id="options_service"> Options Service </h2>
	
	<article id="get_options">
		<h3> Get Report Options </h3>
		<p>
			You can view the different stored options for your reports that have input controls using this function. Simply provide the URI of the report that has options, and an array of objects representing each report option will be returned. The example below shows you how to request all the ReportOptions objects, iterate through them and print the Labels of each of them.
		</p>
		<pre><code><?php inject_sample('code/get_options.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="update_options">
		<h3> Update/Create Report Options </h3>
		<p>
			The updateReportOptions function can be used both to create new, or update existing report options. The example below shows how to create a new report that makes selections for existing input controls.
		</p>
		<pre><code><?php inject_sample('code/update_options.txt'); ?>
		</code>
		</pre>
	</article>	
	
	<article id="delete_options">
		<h3> Delete Report Options </h3>
		<p>
			To delete report options, you must retrieve the URI for the report containing the options, and provide the label for the option setting. If your report options has whitespace in the label, currently this function may not handle it well. Instead use the deleteResources() function to delete the Report Option. The example below will remove the report options created in the example above.
		</p>
		<pre><code><?php inject_sample('code/delete_options.txt'); ?>
		</code>
		</pre>
	</article>


		<h2 id="query_service"> Query Executor Service </h2>
	
	<article id="execute_query">
		<h3> Execute Query </h3>
		<p>
			This service allows you to execute a query on a data source or domain. Pass the URI and a properly written query string as parameters. An associative array representing the names and values of the query passed will be returned to you.
		</p>
		<pre><code><?php inject_sample('code/execute_query.txt'); ?>
		</code>
		</pre>
	</article>	
	
		<h2 id="report_service"> Report Service </h2>
		
	<article id="run_report">
		<h3> Running a Report </h3>
		<p>
			The following code will request the AllAccounts sample report in HTML format. Since the data is HTML, we can simply echo it and the report will be presented in a webpage. You can do many things with the binary data, including offering the file to be downloaded or storing it to a file.
		</p>
		<pre><code><?php inject_sample('code/run_report_html.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="run_report_ic">
		<h3> Running a Report with Input Controls </h3>
		<p>
			The following example displays how a report can be ran with various input controls set. 
		</p>
		<pre><code><?php inject_sample('code/run_report_ic.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="download_report">
		<h3> Offering a Binary Report to Download </h3>
		<p>
			By offering the proper headers before anything else is sent by the script, we can serve binary data to a browser as a download. 
		</p>
		<pre><code><?php inject_sample('code/download_report.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="get_input_controls">
		<h3> Retrieving the Input Controls and Values for a Report </h3>
		<p>
			You can retrieve the input controls defined for a report, their possible values, and other metadata about controls with the report service. The example below will list each control, and its corresponding values.
		</p>
		<pre><code><?php inject_sample('code/get_input_controls.txt'); ?>
		</code>
		</pre>
	</article>

<h1 id="section_group"> Repository & Resource Services </h1>
		<h2 id="repository_service"> Repository Service </h2>
		
	<article id="get_repository">
		<h3> Searching the Repository </h3>
		<p>
            The repository can be searched for items, using a RepositorySearchCriteria object to define your search
            parameters. If no criteria is provided, the entire repository will be returned.
			<br>
			Results are returned as a SearchResourcesResult object. Each result is contained in the items element of the
            result object.
		</p>
		<pre><code><?php inject_sample('code/get_repository.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="create_resource">
		<h3> Create Resource </h3>
		<p>
			Many types of resources can be created. See the namespace \Jaspersoft\Dto\Resource to see the types you may
            work with. Note: CompositeResource and Resource are abstract classes and are not expected to be insantisted
            or used with any 'CRUD' operations.
		</p>
		<pre><code><?php inject_sample('code/create_resource.txt'); ?>
		</code>
		</pre>
	</article>

    <article id="composite_resource">
        <h3> Working with Composite Resources </h3>
        <p>
            Some resources can reference or define subresources, these resources are known as composite resources. When dealing
            with such resources while using the PHP Client, you can decide to provide a reference to an existing resource,
            or define the resource locally.
            <br><br>
            For example, if you create a ReportUnit object, and wish to link to a datasource you can set the fie
            "datasource" to a string or a concrete DataSource object which will create a reference or a local definition
            respectively.
            <br><br>
            In the example below, a ReportUnit is defined with a reference to a datasource, and query. <br><br>
            In addition, the Input Controls are set to an array of both referenced and locally defined input controls.


        </p>
            <pre><code><?php inject_sample('code/composite_resource.txt'); ?>
            </code>
            </pre>
    </article>

	
	<article id="create_binary_resource">
		<h3> Create Binary Resource </h3>
		<p>
            The repository service is capable of uploading binary files as well. These must be handled differently than
            other types of resources.

            This example will explain how you can upload an image to your repository.

            Note: If your isntance of JRS employs custom file types, you must define the mapping of the server type to
            the proper MIME type in the Jaspersoft\Tool\MimeMapper object which contains an associative array of JRS
            file types mapped to their relevant MIME type.
		</p>
		<pre><code><?php inject_sample('code/create_binary_resource.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="binary_resource">
		<h3> Requesting Binary Content </h3>
		<p>
			The example below will show how you can request an image, and display it in inline HTML using base64
            encoding.
		</p>
		<pre><code><?php inject_sample('code/binary_resource.txt'); ?>
		</code>
		</pre>
	</article>
	
		
	<article id="delete_resource">
		<h3> Delete Resource </h3>
		<p>
			You can remove resources from the repository, either one at a time, or several at a time. Using
            deleteResources.
		</p>
		<pre><code><?php inject_sample('code/delete_resource.txt'); ?>
		</code>
		</pre>
	</article>

	<article id="move_resource">
		<h3> Move Resource </h3>
		<p>
			Resources can be moved from one location to another within the Repository. The example below will move the
            folder "/ImageFolder/anchorsteam" to "/anchorsteam"
		</p>
		<pre><code><?php inject_sample('code/move_resource.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="copy_resource">
		<h3> Copy Resource </h3>
		<p>
			Resources can be copied from one location to another within the Repository. The example below will copy the
            folder "/anchorsteam" to the new location "/ImageFolder/anchorsteam". By setting the last argument to true,
            folders which do not exist will be created when copying the file.
		</p>
		<pre><code><?php inject_sample('code/copy_resource.txt'); ?>
		</code>
		</pre>
	</article>
<h2 id="permission_service"> Permission Service </h2>

<article id="search_permissions">
    <h3> Searching Permissions </h3>
    <p>
        You can search for user permissions in regards to a resource in the repository by using the searchRepositoryPermissions function. Provide the URI for the resource as the first argument. Other arguments are available to filter the results as needed. The example below will list all the set permission recipients for "/reports/samples/AllAccounts"
    </p>
		<pre><code><?php inject_sample('code/search_permissions.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_permissions">
    <h3> Updating Permissions </h3>
    <p>
        To update permissions, you must retrieve existing permissions, modify them, and then return them to the server. The example below will retrieve permissions for a report, alter the first one to have no access, and update it.
    </p>
		<pre><code><?php inject_sample('code/update_permissions.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_permission">
    <h3> Updating a single Permission </h3>
    <p>
        You can update one permission at a time in using the following code. It is also possible to create a new permission object from
        scratch and use it to update a single permission if desired.
    </p>
            <pre><code><?php inject_sample('code/update_permission.txt'); ?>
            </code>
            </pre>
</article>


<article id="create_permissions">
    <h3> Creating Permissions </h3>
    <p>
        Permissions can be created by first describing the permissions in Permission objects, then passing them to the server. The example below will create a new permisison for joeuser in organization 1 to administer the AllAccounts report.
    </p>
		<pre><code><?php inject_sample('code/create_permissions.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_permissions">
    <h3> Deleting Permissions </h3>
    <p>
        Removing permissions is possible by passing the permission to the deleteRepositoryPermissions function. The example below will delete the permission created in the previous example.
    </p>
		<pre><code><?php inject_sample('code/delete_permissions.txt'); ?>
        </code>
		</pre>
</article>


<h1 id="section_group"> Superuser Services </h1>
<h2 id="importExportService"> Import/Export Service </h2>

<article id="import_service">
    <h3> Import Service </h3>
    <p>
        The import service allows you to import data that was previously exported. There are various flags that can be set to alter what data is imported, see the REST API documentation for more specific examples of such flags. The example below will submit an import from the file "import_data.zip" assumed to be stored in the same folder as the PHP file.
        It will repeat "Still importing..." and check the status every 10 seconds until it is complete. Then it will announce that the import has completed.
    </p>
		<pre><code><?php inject_sample('code/import_service.txt'); ?>
        </code>
		</pre>
</article>

<article id="export_service">
    <h3> Export Service </h3>
    <p>
        Using this service you can export data from the server to store or import to another server. You must be authorized as the superuesr to use this service. data is compressed as a zip archive and sent as binary data. First construct an ExportTask object that defines what data is to be extracted, then pass it to the startExportTask function. Data can then be stored using PHP file I/O functions, or streamed to a browser by preparing the proper headers and echoing the binary data.
        <br>
        The example below will request an export, then refresh the status of the export every 10 seconds. When it is finished, it will download the data as a zip file, and then store it in a file (export.zip) and offer a link to download the file.
    </p>
		<pre><code><?php inject_sample('code/export_service.txt'); ?>
        </code>
		</pre>
</article>



<!-- END OF ARTICLE SECTION -->
    </section>

	<footer style="float: right;">
		<a href="#intro">back to top</a>
	</footer>


  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="highlight/highlight.pack.js"></script>
  <script type="text/javascript">
	hljs.tabReplace = '  ';
	hljs.initHighlightingOnLoad();
  </script>
  <script type="text/javascript">
	$(document).ready(function() {
		$('code').addClass("php")
	
		toc = $('nav ul')
	
		$('h2').each(function(i, v) {
			toc.append('<li><a href="#'+$(v).attr('id')+'">'+$(v).text()+'</a></li>')
			$(this).nextUntil('h2', 'h3').each(function() {
				toc.append("<li>"+$(this).text()+"</li>")
			});
	/*		$(v).nextUntil('h2', 'h3').each(function(x, h) {
				$(v).append('<li class="subitem"><a href="#'+$(h).attr('id')+'">'+$(h).text()+'</a></li>')
			});
	*/
				
		});
		
	});
  </script>
  </body>

</html>
