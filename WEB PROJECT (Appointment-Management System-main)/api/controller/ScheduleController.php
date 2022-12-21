<?php
require_once '../utils/helpers.php';
require_once '../utils/constants.php';
require_once '../model/Schedule.php';
require_once '../model/User.php';
require_once '../model/Semester.php';
require_once '../model/Message.php';
require_once '../model/Appointment.php';
require_once '../model/AppointmentStatus.php';


class ScheduleController
{
    private Schedule $schedule;
    private User $user;
    private Semester $semester;
    private  Message $message;
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

    function createSchedule($schedule) {
        $this->user->id = $schedule->supervisorId;
        if ($this->user->isSupervisor()) {
            $this->schedule->studentLimit = $schedule->studentLimit;
            $this->schedule->timeStart = $schedule->timeStart;
            $this->schedule->timeEnd = $schedule->timeEnd;
            $this->schedule->day = $schedule->day;
            $this->schedule->supervisorId = $schedule->supervisorId;
            $this->schedule->message = $schedule->message;
            $this->schedule->semesterId = $schedule->semesterId;
            $this->schedule->name = $schedule->name;
            return success("Schedule created successfully", $this->schedule->createSchedule());
        }
        return unauthorized("User id ". $schedule->supervisorId . " is not a supervisor");
    }

    function getScheduleBySupervisor($supervisorId, $semesterId) {
        $this->user->id = $supervisorId;
        if ($this->user->isSupervisor()) {
            $this->schedule->supervisorId = $supervisorId;
            if (isset($semesterId)) {
                $this->schedule->semesterId = $semesterId;
                $resp = $this->schedule->getSchedulesBySupervisorAndSemester();
            } else {
                $resp = $this->schedule->getSchedulesBySupervisor();
            }
            for ($i = 0; $i < count($resp); $i++) {
                $resp[$i]['studentCount'] = $this->getAppointmentCount($resp[$i]['id']);
            }
            return success("Schedules requested",  $resp);
        }
        return unauthorized("User id ". $supervisorId . " is not a supervisor");
    }

    function getScheduleBySupervisorHistory($supervisorId, $semesterId) {
        $this->user->id = $supervisorId;
        if ($this->user->isSupervisor()) {
            $this->schedule->supervisorId = $supervisorId;
            if (isset($semesterId)) {
                $this->schedule->semesterId = $semesterId;
                $resp = $this->schedule->getScheduleBySupervisorAndSemesterHistory();
            } else {
                $resp = $this->schedule->getSchedulesBySupervisor();
            }
            for ($i = 0; $i < count($resp); $i++) {
                $resp[$i]['studentCount'] = $this->getAppointmentCount($resp[$i]['id']);
            }
            return success("Schedules requested",  $resp);
        }
        return unauthorized("User id ". $supervisorId . " is not a supervisor");
    }

    function getScheduleUsers($scheduleId) {
        $this->appointment->scheduleId = $scheduleId;
        $appointments = $this->appointment->getAppointmentBySchedule();
        $response = array_map(function ($appointment) {
            return array(
                'userId' => $appointment['userId'],
                'username' => $this->user->getUserById($appointment['userId'])[0]['first_name'],
                'status' => $appointment['status'],
                'appointmentId' => $appointment['id']
            );
        }, $appointments);
        return success('Users requested', $response);
    }

    function getSchedules() {
        return success("Schedules requested", $this->schedule->getSchedules());
    }

    function getSchedulesBySemester($semesterId) {
        $this->schedule->semesterId = $semesterId;
        $response = $this->schedule->getSchedulesBySemester();
        $response = array_map(function ($schedule) {
            $name = $this->user->getUserById($schedule['supervisorId'])[0]['surname'];
            return array(
                'id' => $schedule['id'],
                'scheduleName' => $schedule['name'],
                "timeSpan" => $schedule['timeStart']. ' - '. $schedule['timeEnd'],
                "day" => $schedule['day'],
                "owner" => $name
            );
        }, $response);
        return success("Schedules requested", $response);
    }

    function getSchedule($id) {
        $this->schedule->id = $id;
        return success("Schedules requested", $this->schedule->getSchedule());
    }

    function updateSchedule($schedule) {
        $this->user->id = $schedule->supervisorId;
        $this->schedule->id = $schedule->id;
        $this->schedule->supervisorId = $schedule->supervisorId;
        $this->schedule->status = $schedule->status;
        if ($this->user->isSupervisor() && $this->schedule->isOwner()) {
            $this->schedule->updateSchedule();
            if (!$this->schedule->status) {
                $this->sendMessage($schedule->message);
                $this->appointment->scheduleId = $this->schedule->id;
            }
            return success("Schedule updated successfully", null);
        }
        return unauthorized("User id ". $schedule->supervisorId . " is not a supervisor or does not have access to schedule");
    }

    function deleteSchedule($id, $supervisorId) {
        $this->schedule->id = $id;
        $this->schedule->supervisorId = $supervisorId;
        $this->user->id = $supervisorId;
        if ($this->user->isSupervisor() && $this->schedule->isOwner()) {
            $this->schedule->deleteSchedule();
            return success("Schedule deleted successfully", null);
        }
        return unauthorized("User id ". $supervisorId . " is not a supervisor or does not have access to schedule");
    }

    function sendMessage($message) {
        $this->message->scheduleId = $this->schedule->id;
        $this->message->senderId = $this->schedule->supervisorId;
        $this->message->content = $message;
        $this->appointment->scheduleId = $this->schedule->id;
        $appointments = $this->appointment->getPendingAppointmentBySchedule();
        foreach ($appointments as $appointment) {
            $this->appointment->id = $appointment['id'];
            $this->appointment->status = AppointmentStatus::cancelled;
            $this->appointment->updateAppointment();
            $this->message->receiverId = $appointment['userId'];
            $this->message->addMessage();
        }
    }

    function getAppointmentCount($scheduleId) {
        $this->appointment->scheduleId = $scheduleId;
//        echo $this->appointment->scheduleId;
//        print_r($this->appointment->getPendingAppointmentBySchedule());
        return count($this->appointment->getPendingAppointmentBySchedule());
    }

}