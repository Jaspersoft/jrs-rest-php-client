<?php


namespace Jaspersoft\Dto\Job;


class RepositoryDestination {

    /** Repository URI of the folder to store the output
     * @var string
     */
    public $folderURI;

    /** Should a timestamp be added to the names of output resources?
     * The timestamp affixed is described by $timestampPattern
     *
     * @var boolean
     */
    public $sequentialFilenames;

    /** Defines the pattern to be used for the timestamp naming.
     *
     * Default: "yyyyMMddHHmm"
     *
     * @var string
     */
    public $timestampPattern;

    /** Should the scheduler overwrite files when saving?
     *
     * @var boolean
     */
    public $overwriteFiles;

    /** Description to be used for output resources, this will be used for all output resources
     * @var string
     */
    public $outputDescription;

    /** Should the scheduler write files to the repository?
     *
     * Default: true
     *
     * @var boolean
     */
    public $saveToRepository;

    /** Default scheduled report output folder URI of the job owner
     * @var string
     */
    public $defaultReportOutputFolderURI;

    /** Should output files be exported to default report output folder URI of job owner?
     *
     * Default: false
     *
     * @var boolean
     */
    public $usingDefaultReportOutputFolderURI;

    /** Local path of folder where job output resources will be created
     * This is a path to a folder on the JRS host's local filesystem.
     *
     * By default, this functionality is disabled on JRS hosts, and can be enabled by modifying applicationContext.xml
     *
     * Example: "/tmp/reports"
     *
     * @var string
     */
    public $outputLocalFolder;

    /** Output FTP location information
     * @var OutputFTPInfo
     */
    public $outputFTPInfo;


    /** Return an associative array representing the attribute. Special handling for
     * outputFTPInfo as it is a complex attribute of RepositoryDestination
     *
     * @return array
     */
    public function jsonSerialize() {
        $result = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (isset($v)) {
                if ($k == "outputFTPInfo") {
                    $result[$k] = $v->jsonSerialize();
                }
                else {
                    $result[$k] = $v;
                }
            }
        }
        return $result;
    }

    /** This function takes a \stdClass decoded by json_decode representing a scheduled job
     * and casts it as a RepositoryDestination Object
     *
     * @param \stdClass $json_obj
     * @return RepositoryDestination
     */
    public static function createFromJSON($json_obj)
    {
        $result = new self();
        if (isset($json_obj->outputFTPInfo)) {
            $result->outputFTPInfo = OutputFTPInfo::createFromJSON($json_obj->outputFTPInfo);
            unset($json_obj->outputFTPInfo);
        }
        foreach ($json_obj as $k => $v) {
            $result->$k = $v;
        }
        return $result;
    }
}