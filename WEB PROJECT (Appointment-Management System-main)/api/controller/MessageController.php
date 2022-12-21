<?php
include_once '../utils/helpers.php';
include_once '../utils/constants.php';
include_once '../model/Schedule.php';
include_once '../model/User.php';
include_once '../model/Message.php';
include_once '../model/DbResponse.php';

class MessageController
{
    private Schedule $schedule;
    private User $user;
    private Message $message;

    function __construct()
    {
        $db = new DbHelper();
        $this->schedule = new Schedule($db);
        $this->user = new User($db);
        $this->message = new Message($db);
    }

    function getSentMessages($scheduleId, $userId) {
        $this->message->scheduleId = $scheduleId;
        $this->message->senderId = $userId;
        $messages = $this->message->getSentMessages();
        $messages = array_map(function ($message) {
            return array(
                'message'=> $message['content'],
                'receiver' => $this->user->getUserById($message[COL_RECEIVER_ID])[0]['first_name'],
                'dateSent' => $message[COL_DATE_SENT]
            );
        }, $messages);
        return success('Messages requested', $messages);
    }

    function getReceivedMessages($scheduleId, $userId) {
        $this->message->scheduleId = $scheduleId;
        $this->message->receiverId = $userId;
        $messages = $this->message->getSentMessages();
        $messages = array_map(function ($message) {
            return array(
                'message'=> $message['content'],
                'sender' => $this->user->getUserById($message[COL_SENDER_ID])[0]['name'],
                'dateSent' => $message[COL_DATE_SENT]
            );
        }, $messages);
        return success('Messages requested', $messages);
    }

}