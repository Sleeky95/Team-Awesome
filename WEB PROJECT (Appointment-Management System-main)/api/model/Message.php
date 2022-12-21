<?php
require_once '../DbHelper.php';
require_once '../utils/constants.php';

class Message
{
    private DbHelper $db;
    public $id;
    public $content;
    public $senderId;
    public $receiverId;
    public $scheduleId;
    public $dateSent;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addMessage() {
        return $this->db->insert(MESSAGE_TABLE_NAME,
            array(COL_CONTENT => $this->content,
                COL_SENDER_ID => $this->senderId,
                COL_RECEIVER_ID => $this->receiverId,
                COL_SCHEDULE_ID => $this->scheduleId),
            array(COL_CONTENT, COL_SENDER_ID, COL_RECEIVER_ID, COL_SCHEDULE_ID));
    }

    public function getSentMessages() {
        return $this->db->select(MESSAGE_TABLE_NAME,
            array(COL_SCHEDULE_ID => $this->scheduleId,
                COL_SENDER_ID => $this->senderId))[RESPONSE_DATA];
    }

    public function getReceivedMessages() {
        return $this->db->select(MESSAGE_TABLE_NAME,
            array(COL_SCHEDULE_ID => $this->scheduleId,
                COL_RECEIVER_ID => $this->receiverId))[RESPONSE_DATA];
    }

}