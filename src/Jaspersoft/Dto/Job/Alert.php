<?php
namespace Jaspersoft\Dto\Job;

/**
 * Class Alert
 * Defines an Alert object used by Job
 *
 * @package Jaspersoft\Dto\Job
 */
class Alert
{
    /**
     * Read-only value of server-declared ID
     *
     * @var int
     */
    public $id;

    /**
     * Read-only value used for optimistic locking
     *
     * @var int
     */
    public $version;

    /**
     * Specify who receives the alert
     *
     * Supported Values:
     *   "NONE", "OWNER", "ADMIN", "OWNER_AND_ADMIN"
     *
     * Default: "OWNER_AND_ADMIN"
     *
     * @var string
     */
    public $recipient;

    /**
     * Array of email addresses
     *
     * @var array
     */
    public $toAddresses;

    /**
     * Specify when the notification is sent
     *
     * Supported Values:
     *   "NONE", "ALL", "FAIL_ONLY", "SUCCESS_ONLY"
     *
     * Default: "FAIL_ONLY"
     *
     * @var string
     */
    public $jobState;

    /**
     * Email message body
     *
     * @var string
     */
    public $messageText;

    /**
     * Email message body on failure
     * @var string
     */
    public $messageTextWhenJobFails;

    /**
     * Email subject
     * @var string
     */
    public $subject;

    /**
     * Should the stack trace be included in the alert?
     *
     * @var boolean
     */
    public $includingStackTrace;

    /**
     * Should the alert include report job info?
     *
     * @var boolean
     */
    public $includingReportJobInfo;

    public function jsonSerialize()
    {
        $result = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (isset($v)) {
                // Here a special case is handled as the JRS server requires a sublevel
                // of address for this attribute in its JSON hierarchy
                if ($k == "toAddresses") {
                    $result[$k] = array("address" => $this->toAddresses);
                }
                else {
                    $result[$k] = $v;
                }
            }
        }
        return $result;
    }

    /** This function takes a \stdClass decoded by json_decode representing a scheduled job
     * and casts it as an Alert Object
     *
     * @param \stdClass $json_obj
     * @return Alert
     */
    public static function createFromJSON($json_obj)
    {
        $result = new self();
        if (isset($json_obj->toAddresses)) {
            $result->toAddresses = (array) $json_obj->toAddresses->address;
            unset($json_obj->toAddresses);
        }
        foreach ($json_obj as $k => $v) {
            $result->$k = $v;
        }
        return $result;
    }

}