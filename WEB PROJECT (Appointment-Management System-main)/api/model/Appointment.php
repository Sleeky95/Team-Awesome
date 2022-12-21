<?php
require_once '../DbHelper.php';
require_once '../utils/constants.php';
require_once 'AppointmentStatus.php';

class Appointment
{
    private DbHelper $db;
    public $id;
    public $scheduleId;
    public $userId;
    public $status;
    public $date;
    public $semesterId;

    function __construct($db)
    {
        $this->db = $db;
    }

    function addAppointment() {
        return $this->db->insert(APPOINTMENT_TABLE_NAME, array(
            COL_SCHEDULE_ID => $this->scheduleId,
            COL_USER_ID => $this->userId,
            COL_SEMESTER_ID => $this->semesterId,
            COL_STATUS => AppointmentStatus::pending
        ), array(COL_SCHEDULE_ID, COL_USER_ID, COL_STATUS, COL_SEMESTER_ID));
    }

    function updateAppointment() {
        return $this->db->update(APPOINTMENT_TABLE_NAME,
            array(COL_STATUS => $this->status),
            array(COL_ID => $this->id),
            array(COL_STATUS => $this->status));
    }

    function getAppointmentById() {
        return $this->transform($this->db->select(APPOINTMENT_TABLE_NAME, array(COL_ID => $this->id))[RESPONSE_DATA]);
    }

    function getAppointmentBySchedule() {
        return $this->transform($this->db->select(APPOINTMENT_TABLE_NAME,
            array(COL_SCHEDULE_ID => $this->scheduleId, COL_SEMESTER_ID => $this->semesterId))[RESPONSE_DATA]);
    }

    function getPendingAppointmentBySchedule() {
        return $this->transform($this->db->select(APPOINTMENT_TABLE_NAME,
            array(COL_SCHEDULE_ID => $this->scheduleId,
            COL_STATUS => AppointmentStatus::pending))[RESPONSE_DATA]);
    }

    function getAppointmentByUser() {
        return $this->transform($this->db->select(APPOINTMENT_TABLE_NAME, array(COL_USER_ID => $this->userId))[RESPONSE_DATA]);
    }

    function getAppointmentByUserAndSemester() {
        return $this->transform($this->db->select(APPOINTMENT_TABLE_NAME,
            array(COL_USER_ID => $this->userId, COL_SEMESTER_ID => $this->semesterId, COL_STATUS => AppointmentStatus::pending))[RESPONSE_DATA]);
    }

    function getAppointmentByUserAndSemesterHistory() {
        return $this->transform($this->db->select(APPOINTMENT_TABLE_NAME,
            array(COL_USER_ID => $this->userId, COL_SEMESTER_ID => $this->semesterId))[RESPONSE_DATA]);
    }

    function transform($content) {
        return array_map(function ($content) {
            return array(
                'id' => $content[COL_ID],
                'userId' => $content[COL_USER_ID],
                'scheduleId' => $content[COL_SCHEDULE_ID],
                'status' => $content[COL_STATUS],
                'semesterId' => $content[COL_SEMESTER_ID]
            );
        }, $content);
    }
}