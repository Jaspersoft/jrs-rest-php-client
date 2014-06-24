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
    <title>Documentation - JasperReports Server PHP Client</title>

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
		</p>
		<p>
			Code highlighting for this page provided by <a href="http://softwaremaniacs.org/soft/highlight/en/">highlight.js</a>,
			written by Ivan Sagalaev (<a href="highlight/LICENSE">license</a>/<a href="highlight/AUTHORS.en.txt">authors</a>).
        </p>
      </header>

	<nav>
	<h1> Table of Contents </h1>
	<ul></ul>
	</nav>

	<section class='about'>
		<article id='about_class'>
			<h3> About the Class </h3>
			<p>
			   The JasperReports Server PHP Client is a wrapper for the JasperReports Server REST API. This client abstracts the
			   details behind the communications with the server into simple-to-use functions representative of their functionality
			   on the server. Developers do not have to deal with parsing and creating JSON data, but rather deal with objects that
			   represent resources within the server.
			</p>
			<p>
			   By using JasperReports Server PHP Client, developers can focus on their logic and implementation rather than detailed
			   communications with JasperReports Server.
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


	<h2 id='prep'> Getting Started </h2>

	<article>
		<h3> Invoking the Client </h3>
		<p>
			In your project, you must include the autoloader so that classes can be loaded as needed. If you have not generated the
			autoloader file, please follow the instructions in the README for installation. Then you must initialize a client with
			your login credentials.
		</p>

		<pre><code><?php inject_sample('code/client_invokation.txt'); ?>
		</code>
		</pre>
	</article>

	<article id="usage_patterns">
		<h3> Usage Patterns </h3>
		<p>
			There are several way to invoke the functions of the client:
		</p>
		<pre><code><?php inject_sample('code/service_access.txt'); ?>
		</code>
		</pre>
		<p>
			Note that all examples on this page assume you are importing objects using the PHP 'use' keyword. If you are not, you
			must refer to the objects using the full qualified namespace.
		</p>
	</article>

    <article>
        <h3> Altering the Request Timeout </h3>
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
			The client class can also return data about the sever it is connecting to. This data includes date formats, license
			information and other info about the server's configruation. It is returned in an associative array format.
		</p>

		<pre><code><?php inject_sample('code/server_info.txt'); ?>
		</code>
		</pre>
	</article>

<h1 id="section_group"> Repository and Resource Services </h1>

	<h2 id="repository_service"> repositoryService() </h2>
		
	<article id="get_repository">
		<h3> Searching the Repository </h3>
		<p>
            You can search the repository by using a <code>RepositorySearchCriteria</code> object to define your search
            parameters. If no criteria is provided, the entire repository will be returned.
		</p><p>
			Results are returned as a <code>SearchResourcesResult</code> object. Each result is contained in the items element of the
            result object.
		</p>
		<pre><code><?php inject_sample('code/get_repository.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="create_resource">
		<h3> Creating a Resource </h3>
		<p>
			Many types of resources can be created. See the namespace <code>\Jaspersoft\Dto\Resource</code> to see the types you may
            work with. Note: <code>CompositeResource</code> and <code>Resource</code> are abstract classes and are not expected to be insantisted
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
		</p><p>
            For example, when you create a ReportUnit object, you can set the <code>datasource</code> field to either
            of the following:
        </p><ul>
            <li>A string representing the path to a data source in the repository. This will create a reference to the designated resource.
            </li>
            <li>A concrete <code>DataSource</code> object with all the required fields. This will create a local definition of the data source.
            </li>
        </ul>
		<p>
            The following example defines a ReportUnit that references a data source and a query in the repository. The array of
            input controls includes both referenced resources and a locally defined <code>InputControl</code> structure.
        </p>
            <pre><code><?php inject_sample('code/composite_resource.txt'); ?>
            </code>
            </pre>
    </article>
	
	<article id="create_binary_resource">
		<h3> Creating Binary Resources </h3>
		<p>
            The repository service is capable of uploading binary files as well. These must be handled differently than
            other types of resources.
		</p><p>
            The following example shows how to upload an image to the JasperReports Server repository.
		</p>
		<pre><code><?php inject_sample('code/create_binary_resource.txt'); ?>
		</code>
		</pre>
		<p>
            Note: If your instance of JRS employs custom file types, you must define the mapping of the server type to
            the proper MIME type in the Jaspersoft\Tool\MimeMapper object which contains an associative array of JRS
            file types mapped to their relevant MIME type.
		</p>
	</article>
	
	<article id="binary_resource">
		<h3> Requesting Binary Content </h3>
		<p>
			The following example shows how to request an image and display it in inline HTML using base64
            encoding.
		</p>
		<pre><code><?php inject_sample('code/binary_resource.txt'); ?>
		</code>
		</pre>
	</article>
	
		
	<article id="delete_resource">
		<h3> Deleting a Resource </h3>
		<p>
			The <code>deleteResources</code> function removes resources from the repository, either one at a time or several at a time.
			When deleting a folder, all folder contents are deleted, including recursive subfolders and their contents, provided the
			user credentials have delete permission on all folders and resources.
		</p>
		<pre><code><?php inject_sample('code/delete_resource.txt'); ?>
		</code>
		</pre>
	</article>

	<article id="move_resource">
		<h3> Moving a Resource </h3>
		<p>
			Resources can be moved from one location to another within the Repository. Note that the <code>moveResource</code>
		 	function does not rename the resource, only changes its parent. The following example moves the
            folder "/ImageFolder" to "/MyReports/ImageFolder":
		</p>
		<pre><code><?php inject_sample('code/move_resource.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="copy_resource">
		<h3> Copying a Resource </h3>
		<p>
			Resources can be copied from one location to another within the Repository. The following example copies the
            folder "/MyReports/ImageFolder" to "/SharedReports/ImageFolder". By setting the last argument to true,
            folders that do not exist in the new parent's path will be created.
		</p>
		<pre><code><?php inject_sample('code/copy_resource.txt'); ?>
		</code>
		</pre>
	</article>

<h2 id="permission_service"> permissionService() </h2>

<article id="search_permissions">
    <h3> Searching Permissions </h3>
    <p>
        You can search for user permissions to a give resource in the repository by using the <code>searchRepositoryPermissions</code>
   	    function. Provide the URI for the resource as the first argument. Other arguments are available to filter the results as needed.
   	    The following example lists all the set permission recipients for "/reports/samples/AllAccounts":
    </p>
		<pre><code><?php inject_sample('code/search_permissions.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_permissions">
    <h3> Updating Permissions </h3>
    <p>
        To update permissions, you must retrieve existing permissions, modify them, and then pass them to the <code>updateRepositoryPermissions</code>
        function. The following example retrieves permissions for a report, alters the first one to have no access, and updates it on the server.
    </p>
		<pre><code><?php inject_sample('code/update_permissions.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_permission">
    <h3> Updating a Single Permission </h3>
    <p>
        You can update one permission at a time as shown in the following code example. It is also possible to create a new permission object from
        scratch and use it to update a single permission if desired.
    </p>
            <pre><code><?php inject_sample('code/update_permission.txt'); ?>
            </code>
            </pre>
</article>


<article id="create_permissions">
    <h3> Creating Permissions </h3>
    <p>
        Permissions can be created by first describing the permissions in Permission objects, then passing them to the server. The following example
        creates a new permisison for joeuser in organization_1 to administer the AllAccounts report.
    </p>
		<pre><code><?php inject_sample('code/create_permissions.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_permissions">
    <h3> Deleting Permissions </h3>
    <p>
        Removing permissions is possible by passing the permission to the deleteRepositoryPermissions function. The example below will delete the
        permission created in the previous example.
    </p>
		<pre><code><?php inject_sample('code/delete_permissions.txt'); ?>
        </code>
		</pre>
</article>

<h2 id="importExportService"> importExportService() </h2>

<article id="export_service">
    <h3> Exporting Resources </h3>
    <p>
        Using this service, you can export data from the server to store or import to another server. You must set the user credentials
        to a system admin (<code>superuser</code> by default) when calling this service. Data is compressed as a zip archive and sent as
        binary data. First construct an <code>ExportTask</code> object that defines what data is to be extracted, then pass it to the
        <code>startExportTask</code> function. Data can then be stored using PHP file I/O functions, or streamed to a browser by preparing
        the proper headers and echoing the binary data.
    </p><p>
        The following example requests an export, then refreshes the status of the export every 10 seconds. When it is finished, it downloads
        the data as a zip file, stores it in a file (export.zip), and offers a link to download the file.
    </p>
		<pre><code><?php inject_sample('code/export_service.txt'); ?>
        </code>
		</pre>
</article>

<article id="import_service">
    <h3> Importing Resources </h3>
    <p>
        The import service allows you to import data that was previously exported. There are various flags that can be set to alter what
        data is imported; see the REST API documentation for specific examples of such flags.
    </p><p>
        The following example submits an import from the file "import_data.zip" assumed to be stored in the same folder as the PHP file.
        It displays "Import in progress..." and checks the status every 10 seconds until it is complete. Then it announces that the import
        has completed.
    </p>
		<pre><code><?php inject_sample('code/import_service.txt'); ?>
        </code>
		</pre>
</article>

<h1 id="section_group"> Reporting Services </h1>

	<h2 id="report_service"> reportService() </h2>
		
	<article id="run_report">
		<h3> Running a Report </h3>
		<p>
			The following code example requests the AllAccounts report in HTML format. Since the data is HTML, we can simply echo it,
			and the report will be presented in the web page. You can do many things with the binary data, including offering the file
			to be downloaded or storing it to a file.
		</p>
		<pre><code><?php inject_sample('code/run_report_html.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="download_report">
		<h3> Downloading a Report in a Binary Report </h3>
		<p>
			By setting the proper headers before calling the <code>runReport</code> function, you can serve binary data to a browser as a download. 
		</p>
		<pre><code><?php inject_sample('code/download_report.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="run_report_ic">
		<h3> Running a Report with Input Controls </h3>
		<p>
			The following example shows how to run a report with various input controls. 
		</p>
		<pre><code><?php inject_sample('code/run_report_ic.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="get_input_controls">
		<h3> Retrieving Input Control Values </h3>
		<p>
			You can retrieve the input controls defined for a report, their possible values, and other metadata about controls.
			The following example lists each control and its corresponding values.
		</p>
		<pre><code><?php inject_sample('code/get_input_controls.txt'); ?>
		</code>
		</pre>
	</article>

	<h2 id="options_service"> optionsService() </h2>
	
	<article id="get_options">
		<h3> Listing Report Options </h3>
		<p>
			A report option is a set of saved input control values for a given report. You can view the different stored options for
			your reports using the <code>getReportOptions</code> function. It takes the URI of a report that has options, and returns
			an array of objects representing each report option. The following example shows how to request all the <code>ReportOptions</code>
			objects, iterate through them and print the labels of each one.
		</p>
		<pre><code><?php inject_sample('code/get_options.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="update_options">
		<h3> Creating and Updating Report Options </h3>
		<p>
			The <code>updateReportOptions</code> function can be used both to create new report options or to update existing ones.
			The following example shows how to create a new report option that makes selections for existing input controls.
		</p>
		<pre><code><?php inject_sample('code/update_options.txt'); ?>
		</code>
		</pre>
	</article>	
	
	<article id="delete_options">
		<h3> Deleting Report Options </h3>
		<p>
			To delete a report option, specify the URI of the report, and provide the label of the report option.
		</p><p>
			If your report options has whitespace in the label, the current version of this function may not handle it well. Instead,
			use the <code>deleteResources</code> function to delete the report option.
		</p>
		<pre><code><?php inject_sample('code/delete_options.txt'); ?>
		</code>
		</pre>
	</article>

	<h2 id="job_service"> jobService() </h2>
	
	<article id="search_jobs">
		<h3> Searching for Jobs </h3>
		<p>
			The <code>searchJobs</code> function has various options that can be set to filter your results. With no options set, it
			returns all existing jobs on the server. All results are <code>JobSummary</code> objects that contain the ID of the job.
			The following example searches for all jobs scheduled for a given report.
		</p>
		<pre><code><?php inject_sample('code/search_jobs.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="get_job">
		<h3> Getting Job Details </h3>
		<p>
			Once you know the ID of a job, you can request the specific details of that job. The <code>getJob</code> function
			returns a <code>Job</code> object describing the job.
		</p>
		<pre><code><?php inject_sample('code/get_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="create_job">
		<h3> Creating a Job </h3>
		<p>
			To create a job, first create a well-defined <code>Job</code> object. The <code>Job</code> object contains
            subclasses that define the behaviour of the scheduled job. The response to a successful job creation contains
            the ID of the new job.
		</p>
		<pre><code><?php inject_sample('code/create_job.txt'); ?>
		</code>
		</pre>
	</article>	
	
	<article id="update_job">
		<h3> Updating a Job </h3>
		<p>
			To update a scheduled job, request its <code>Job</code> object from the server, modify it, and then call the
			<code>updateJob</code> function.
		</p><p>
			The <code>Job</code> class uses properties and arrays to manage its data; this is different from the other
			objects that use only properties. This means you do not use get/set methods to alter the data in a Job object,
			but rather set the properties as if they were variables. If a property refers to a nested element of the
			<code>Job</code> class, use array functions to manipulate the arrays. 
		</p>
		<pre><code><?php inject_sample('code/update_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="pause_job">
		<h3> Pausing a Job </h3>
		<p>
			When a job is paused, it does not run any reports. The only argument to the <code>pauseJob</code> function is
			either a single job ID as an integer or an array of job IDs. If no argument is provided, this function pauses
			all jobs to which the user has access. 
		</p>
		<pre><code><?php inject_sample('code/pause_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="resume_job">
		<h3> Resuming a Job </h3>
		<p>
			To resume a job, pass the job ID to the <code>resumeJob</code> function. For convenience, you may also pass an
			array of job IDs. As with the <code>pauseJob</code> function, this function resumes all jobs if no argument is
			provided.
		</p>
		<pre><code><?php inject_sample('code/resume_job.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="delete_job">
		<h3> Deleting Job </h3>
		<p>
			To delete a job, search for the job by name or URI, then pass its ID to the <code>deleteJob</code> function.
		</p>
		<pre><code><?php inject_sample('code/delete_job.txt'); ?>
		</code>
		</pre>
	</article>
	

	<h2 id="query_service"> queryService() </h2>
	
	<article id="execute_query">
		<h3> Executing a Query </h3>
		<p>
			This service allows you to execute a query on a data source or Domain. Pass the URI of the data source or Domain and
			a properly written query string as parameters. The <code>executeQuery</code> function returns an associative array
			representing the names and values in the query results.
		</p>
		<pre><code><?php inject_sample('code/execute_query.txt'); ?>
		</code>
		</pre>
	</article>	
	

<h1 id="section_group"> Administration Services </h1>

	<h2 id="organization_service"> organizationService() </h2>

<article id="search_organization">
    <h3> Searching for Organizations </h3>
    <p>
        Use the <code>searchOrganization</code> function to search for organizations by ID.
    </p>
		<pre><code><?php inject_sample('code/search_organization.txt'); ?>
        </code>
		</pre>
</article>

<article id="get_organization">
    <h3> Getting Organization Details</h3>
    <p>
        Once you know the full ID of a specific organization, use the <code>getOrganization</code> function to request its detailed
        information. This function returns an <code>Organization</code> object.
    </p>
            <pre><code><?php inject_sample('code/get_organization.txt'); ?>
            </code>
            </pre>
</article>

<article id="create_organization">
    <h3> Creating an Organization </h3>
    <p>
        To create a new organization, define a new <code>Organization</code> object and pass it to the <code>createOrganization</code> function.
    </p>
		<pre><code><?php inject_sample('code/create_organization.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_organization">
    <h3> Updating an Organization </h3>
    <p>
        To modify an organization, obtain its <code>Organization</code> object with the <code>searchOrganization</code>
        or <code>getOrganization</code> functions. Then modify the fields you wish to update and pass it to the
        <code>updateOrganization</code> function.
    </p>
		<pre><code><?php inject_sample('code/update_organization.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_organization">
    <h3> Deleting Organizations </h3>
    <p>
		To delete an organization, obtain its <code>Organization</code> object with the <code>searchOrganization</code>
        or <code>getOrganization</code> functions, and pass it to the <code>deleteOrganization</code> function.
    </p>
		<pre><code><?php inject_sample('code/delete_organization.txt'); ?>
        </code>
		</pre>
</article>

<h2 id="role_service"> roleService() </h2>

<article id="get_many_roles">
    <h3> Searching for Roles </h3>
    <p>
        Use the the <code>searchRoles</code> function to request all the roles of an organization, or all roles on the server.
        Optionally, you can search based on specific criteria for roles. The following example returns all roles on the server.
    </p>
		<pre><code><?php inject_sample('code/get_many_roles.txt'); ?>
        </code>
		</pre>
</article>

<article id="get_role">
    <h3> Getting a Specific Role </h3>
    <p>
        If you know the name of the role, you can request it specifically using the <code>getRole</code> function.
        This function returns a <code>Role</code> object.
    </p>
		<pre><code><?php inject_sample('code/get_role.txt'); ?>
        </code>
		</pre>
</article>

<article id="create_role">
    <h3> Creating a Role </h3>
    <p>
        To create a role, describe it in a <code>Role</code> object, then pass it to the <code>createRole</code> function.
        The following example creates a new role in organization_1.
    </p>
		<pre><code><?php inject_sample('code/create_role.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_role">
    <h3> Updating a Role </h3>
    <p>
        To upate a role, pass an updated <code>Role</code> object to the <code>updateRole</code> function. If you change the
        name of the role, you must pass the old name of the role as the second argument.
    </p>
		<pre><code><?php inject_sample('code/update_role.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_role">
    <h3> Deleting a Role </h3>
    <p>
        To delete a role, first retrieve the <code>Role</code> object representing the role you wish to remove, then pass it
        to the <code>deleteRole</code> function.
    </p>
		<pre><code><?php inject_sample('code/delete_role.txt'); ?>
        </code>
		</pre>
</article>
<h2 id="user_service"> userService() </h2>

<article id="search_user">
    <h3> Searching for Users </h3>
    <p>
        Using the <code>searchUsers</code> function you can search for several users based on various critera. This function
        returns an array of <code>UserLookup</code> objects that can be used with the <code>getUserByLookup</code> function
        to retrieve their fully described <code>User</code> objects.
    </p><p>
        The example below finds all users of organization_1 containing 'j' in their username, and prints out the roles
        assigned to each user.
    </p>
		<pre><code><?php inject_sample('code/search_user.txt'); ?>
        </code>
		</pre>
</article>

<article id="create_user">
    <h3> Creating a User </h3>
    <p>
        To create a user, define a <code>User</code> object that fully describes the user, and pass it to the <code>addOrUpdateUser</code> function.
    </p>
		<pre><code><?php inject_sample('code/create_user.txt'); ?>
        </code>
		</pre>
</article>

<article id="update_user">
    <h3> Updating a User </h3>
    <p>
        To update a user, pass an updated <code>User</code> object to the <code>addOrUpdateUser</code> function.
    </p>
		<pre><code><?php inject_sample('code/update_user.txt'); ?>
        </code>
		</pre>
</article>

<article id="delete_user">
    <h3> Delete User </h3>
    <p>
        To delete a user, first retrieve the <code>User</code> object representing the user you wish to remove, then pass it
        to the <code>deleteUser</code> function.
    </p>
		<pre><code><?php inject_sample('code/delete_user.txt'); ?>
        </code>
		</pre>
</article>

	<article id="attribute_functions">
		<h3> Reading Attributes </h3>
		<p>
			User attributes are name-value pairs that administrators can assign to any user. To read the attributes on a given user,
			pass a <code>User</code> object to the <code>getAttributes</code> function. You can also specifiy specific attributes
			that you wish to read, otherwise all attributes for the user will be returned.
		</p>
		<pre><code><?php inject_sample('code/get_attributes.txt'); ?>
		</code>
		</pre>
	</article>

	<article id="add_attributes">
		<h3> Adding or Updating Attributes </h3>
		<p>
			Use <code>addOrUpdateAttribute</code> function to create or update an attribute for a user. Specify the name and value
			in an <code>Attribute</code> object. If the attribute name matches an existing attribute, its value is updated. If the
			attribute name does not exist for the given user, the attribute is added to the user.
		</p>
		<pre><code><?php inject_sample('code/add_attributes.txt'); ?>
		</code>
		</pre>
	</article>
	
	<article id="delete_attributes">
		<h3> Deleting Attributes </h3>
		<p>
			To remove attributes from a user, pass a <code>User</code> object and an array of attribute names (not <code>Attribute</code>
			objects) to the <code>deleteAttributes</code> function. If no attribute names are given, all attributes are removed.
		</p>
		<pre><code><?php inject_sample('code/delete_attributes.txt'); ?>
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
