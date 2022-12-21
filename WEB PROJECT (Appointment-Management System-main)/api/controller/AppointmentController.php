<?php
include_once '../utils/helpers.php';
include_once '../utils/constants.php';
include_once '../model/Schedule.php';
include_once '../model/User.php';
include_once '../model/Semester.php';
include_once '../model/Message.php';
include_once '../model/Appointment.php';
include_once '../model/AppointmentStatus.php';
include_once '../model/DbResponse.php';

class AppointmentController
{
    private Schedule $schedule;
    private User $user;
    private Semester $semester;
    private Message $message;
    private Appointment $appointment;

    function __construct()
    {
        $db = new DbHelper();
        $this->schedule = new Schedule($db);
        $this->user = new User($db);
        $this->semester = new Semester($db);
        $this->message = new Message($db);
        $this->appointment = new Appointment($db);
    }

    function createAppointment($appointment) {
        $this->appointment->userId = $appointment->userId;
        $this->appointment->scheduleId = $appointment->scheduleId;
        $this->schedule->id = $appointment->scheduleId;
        $sch = $this->schedule->getSchedule()[0];
        $appCount = count($this->appointment->getPendingAppointmentBySchedule());
        if ($appCount == $sch['studentLimit']) {
            return error("Appointments slots filled.");
        }
        $this->appointment->semesterId = $sch['semesterId'];
        $response = $this->appointment->addAppointment();
        if ($response[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS) {
            $this->sendMessage($this->appointment->scheduleId, $this->appointment->userId);
            return success("Appointment created successfully", null);
        }
        return error($response[RESPONSE_MESSAGE]);
    }

    function getAppointmentByUser($userId, $semesterId) {
        $this->appointment->userId = $userId;
        $this->appointment->semesterId = $semesterId;
        $response = $this->appointment->getAppointmentByUserAndSemester();
        $response = array_map(function ($appointment) {
            $this->schedule->id = $appointment['scheduleId'];
            $sch = $this->schedule->getSchedule()[0];
            $name = $this->user->getUserById($sch['supervisorId'])[0]['surname'];
            return array(
               "id" => $appointment['id'],
              "scheduleName" => $sch['name'],
              "scheduleId" => $sch['id'],
              "status" => $appointment['status'],
              "name" => $name,
              "timeSpan" => $sch['timeStart']. ' - '. $sch['timeEnd'],
              "day" => $sch['day']
            );
        }, $response);
        return success('Appointments requested', $response);
    }

    function getAppointmentByUserHistory($userId, $semesterId) {
        $this->appointment->userId = $userId;
        $this->appointment->semesterId = $semesterId;
        $response = $this->appointment->getAppointmentByUserAndSemesterHistory();
        $response = array_map(function ($appointment) {
            $this->schedule->id = $appointment['scheduleId'];
            $sch = $this->schedule->getSchedule()[0];
            $name = $this->user->getUserById($sch['supervisorId'])[0]['surname'];
            return array(
                "id" => $appointment['id'],
                "scheduleName" => $sch['name'],
                "scheduleId" => $sch['id'],
                "status" => $appointment['status'],
                "name" => $name,
                "timeSpan" => $sch['timeStart']. ' - '. $sch['timeEnd'],
                "day" => $sch['day']
            );
        }, $response);
        return success('Appointments requested', $response);
    }

    function markAttendance($appointmentId, $status) {
        $this->appointment->id = $appointmentId;
        if ($status) {
            $this->appointment->status = AppointmentStatus::success;
        } else {
            $this->appointment->status = AppointmentStatus::expired;
        }
        $response =  $this->appointment->updateAppointment();
        if ($response[RESPONSE_STATUS] != DbResponse::STATUS_ERROR) {
            return success('Appointment updated', $response);
        }
        return error($response[RESPONSE_MESSAGE]);
    }

    function getAppointmentBySchedule($scheduleId) {
        $this->appointment->scheduleId = $scheduleId;
        return success('Appointments requested', $this->appointment->getAppointmentByUser());
    }

    function cancelAppointment($appointment) {
        $this->appointment->id = $appointment->id;
        $app = $this->appointment->getAppointmentById()[0];
        $this->schedule->id = $app['scheduleId'];
        $sch = $this->schedule->getSchedule()[0];
        $curr_time = time();
        $day = date('d', $curr_time);
        $timeStart = strtotime($sch['timeStart']);
        if ($day != $sch['day'] || $curr_time - $timeStart >= 36000) {
            $this->appointment->status = AppointmentStatus::cancelled;
            $response = $this->appointment->updateAppointment();
            if ($response[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS) {
                $this->sendCustomMessage($app['scheduleId'], $app['userId'], $appointment->message);
            }
            return success('Appointment Cancelled', null);
        }
        return error('Can not cancel appointment');
    }

    private function sendMessage($scheduleId, $userId) {
        $this->schedule->id = $scheduleId;
        $schedule = $this->schedule->getSchedule()[0];
        $content = $schedule['message'];
        $this->message->scheduleId = $scheduleId;
        $this->message->senderId = $schedule['supervisorId'];
        $this->message->receiverId = $userId;
        $this->message->content = $content;
        $this->message->addMessage();
    }

    private function sendCustomMessage($scheduleId, $userId, $message) {
        $this->schedule->id = $scheduleId;
        $schedule = $this->schedule->getSchedule()[0];
        $this->message->scheduleId = $scheduleId;
        $this->message->senderId = $userId;
        $this->message->receiverId = $schedule['supervisorId'];
        $this->message->content = $message;
        $this->message->addMessage();
    }

}