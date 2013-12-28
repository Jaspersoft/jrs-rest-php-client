<?php


namespace Jaspersoft\Dto\Job;


/**
 * Class Alert
 * @package Jaspersoft\Dto\Job
 */
class Alert {

    /** Specify who receives the alert
     *
     * Supported Values:
     *   "NONE", "OWNER", "ADMIN", "OWNER_AND_ADMIN"
     *
     * Default: "OWNER_AND_ADMIN"
     *
     * @var string
     */
    public $recipient;

    /** Array of email addresses
     *
     * @var array
     */
    public $toAddresses;

    /** Specify when the notification is sent
     *
     * Supported Values:
     *   "NONE", "ALL", "FAIL_ONLY", "SUCCESS_ONLY"
     *
     * Default: "FAIL_ONLY"
     *
     * @var string
     */
    public $jobState;

    /** Email message body
     *
     * @var string
     */
    public $messageText;

    /** Email message body on failure
     * @var string
     */
    public $messageTextWhenJobFails;

    /** Email subject
     * @var string
     */
    public $subject;

    /** Should the stack trace be included in the alert?
     *
     * @var boolean
     */
    public $includingStackTrace;

    /** Should the alert include report job info?
     *
     * @var boolean
     */
    public $includingReportJobInfo;

    public function jsonSerialize() {
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

}