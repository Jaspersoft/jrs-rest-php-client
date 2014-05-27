<?php
namespace Jaspersoft\Dto\Job;

/**
 * Class OutputFTPInfo
 *
 * This class defines an FTP connection to be used with a Job.
 *
 * @package Jaspersoft\Dto\Job
 */
class OutputFTPInfo
{

    /**
     * FTP Server Username
     * @var string
     */
    public $userName;

    /**
     * @var string
     */
    public $password;

    /**
     * Path to remote folder where output resources will be stored
     * @var string
     */
    public $folderPath;

    /**
     * FTP Server Hostname
     * @var string
     */
    public $serverName;

    /**
     * FTP type (supported values: "ftp", "ftps")
     * @var string
     */
    public $type;

    /**
     * FTP Server Protocol (e.g: SSL or TLS)
     * @var string
     */
    public $protocol;

    /**
     * FTP Server Port
     * @var int
     */
    public $port;

    /**
     * Specifies security mode for FTPS (true: implicit, false: explicit)
     *
     * Default: true
     *
     * @var boolean
     */
    public $implicit;

    /**
     * Return PROT command
     *          (supported values: "C", "S", "E", "P")
     *          "C": Clear
     *          "S": Safe (SSL Only)
     *          "E": Confidential (SSL Only)
     *          "P": Private
     *
     * @var string
     */
    public $prot;

    /**
     * Specifies protection buffer size
     * @var int
     */
    public $pbsz;

    public function jsonSerialize() {
        $result = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (isset($v)) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    public static function createFromJSON($json_obj)
    {
        $result = new self();
        foreach ($json_obj as $k => $v) {
            $result->$k = $v;
        }
        return $result;
    }

}