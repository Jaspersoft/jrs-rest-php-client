<?php
namespace Jaspersoft\Dto\Job;

/**
 * Class MailNotification
 * @package Jaspersoft\Dto\Job
 */
class MailNotification
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
     * To recipients
     * @var array
     */
    public $toAddresses;

    /**
     * Carbon Copy recipients
     * @var array
     */
    public $ccAddresses;

    /**
     * Blind Carbon Copy recipients
     * @var array
     */
    public $bccAddresses;

    /**
     * Email Subject Text
     * @var string
     */
    public $subject;

    /**
     * Email Body Text
     * @var string
     */
    public $messageText;

    /**
     * Determines whether notification includes job as attachments, or links
     *
     * Supported Values:
     *   "SEND" - Notification contains links to job output generated in repository
     *   "SEND_ATTACHMENT" - Notification contains job output as attachments
     *   "SEND_ATTACHMENT_NOZIP" - Notification contains job output as non-zipped attachments
     *   "SEND_EMBED" - Notification embeds HTML job in email body
     *   "SEND_ATTACHMENT_ZIP_ALL" - Notification sends all output in one ZIP file
     *   "SEND_EMBED_ZIP_ALL_OTHERS" - Notification embeds HTML job in email body and puts other types in one ZIP file
     *
     * Default: "SEND"
     *
     * @var string
     */
    public $resultSendType;

    /**
     * Should email notifications be skipped for jobs that produce empty reports?
     *
     * Default: false
     *
     * @var boolean
     */
    public $skipEmptyReports;

    /**
     * The text of the Email Body for when a job fails
     *
     * @var string
     */
    public $messageTextWhenJobFails;

    /**
     * Should the notification include a stack trace of an exception?
     *
     * Default: false
     *
     * @var boolean
     */
    public $includingStackTraceWhenJobFails;

    /**
     * Should the notification be skipped when a job fails?
     *
     * Default: false
     *
     * @var boolean
     */
    public $skipNotificationWhenJobFails;

    /**
     * Create an associative array of the data which is set to a non-null value
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $result = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (isset($v)) {
                // JSON specification set by server requires sublevel of "address" for these
                // attributes of the MailNotification, so they are special cases handled below
                if ($k == "toAddresses") {
                    $result[$k] = array("address" => $this->toAddresses);
                }
                else if ($k == "ccAddresses") {
                    $result[$k] = array("address" => $this->ccAddresses);
                }
                else if ($k == "bccAddresses") {
                    $result[$k] = array("address" => $this->bccAddresses);
                }
                else {
                    $result[$k] = $v;
                }
            }
        }
        return $result;
    }

    /**
     * This function takes a \stdClass decoded by json_decode representing a scheduled job
     * and casts it as a MailNotification Object
     *
     * @param \stdClass $json_obj
     * @return MailNotification
     */
    public static function createFromJSON($json_obj)
    {
        $result = new self();
        foreach ($json_obj as $k => $v) {
            if ($k == "toAddresses") {
                $result->toAddresses = (array) $v->address;
            } else if ($k == "ccAddresses") {
                $result->ccAddresses = (array) $v->address;
            } else if ($k == "bccAddresses") {
                $result->bccAddresses = (array) $v->address;
            } else {
                $result->$k = $v;
            }
        }
        return $result;
    }

}