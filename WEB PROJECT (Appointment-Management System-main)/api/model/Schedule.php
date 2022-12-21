<?php

require_once '../DbHelper.php';
require_once '../utils/constants.php';

class Schedule
{
    private DbHelper $db;
    public $semesterId;
    public $id;
    public $name;
    public $day;
    public $timeStart;
    public $timeEnd;
    public $studentLimit;
    public $supervisorId;
    public $status;
    public $message;

   function __construct($db)
   {
       $this->db = $db;
   }

   function createSchedule() {
        return $this->db->insert(SCHEDULE_TABLE_NAME,
            array(
                COL_SEMESTER_ID => $this->semesterId,
                COL_DAY => $this->day,
                COL_TIME_START => $this->timeStart,
                COL_TIME_END => $this->timeEnd,
                COL_STUDENT_LIMIT => $this->studentLimit,
                COL_SUPERVISOR_ID => $this->supervisorId,
                COL_MESSAGE => $this->message,
                COL_NAME => $this->name
            ),
            array(COL_SEMESTER_ID, COL_DAY, COL_TIME_START, COL_TIME_END, COL_STUDENT_LIMIT, COL_SUPERVISOR_ID, COL_NAME)
        );
   }

   function getSchedules() {
       return $this->transform($this->db->select(SCHEDULE_TABLE_NAME, array())[RESPONSE_DATA]);
   }

   function getSchedulesBySemester() {
       return $this->transform($this->db->select(SCHEDULE_TABLE_NAME, array(COL_SEMESTER_ID => $this->semesterId))[RESPONSE_DATA]);
   }

   function getSchedulesBySupervisor() {
       return $this->transform($this->db->select(SCHEDULE_TABLE_NAME, array(COL_SUPERVISOR_ID => $this->supervisorId))[RESPONSE_DATA]);
   }

    function getSchedulesBySupervisorAndSemester() {
        return $this->transform($this->db->select(SCHEDULE_TABLE_NAME,
            array(COL_SUPERVISOR_ID => $this->supervisorId,
                COL_SEMESTER_ID => $this->semesterId,
                COL_STATUS => true))[RESPONSE_DATA]);
    }

    function getScheduleBySupervisorAndSemesterHistory() {
        return $this->transform($this->db->select(SCHEDULE_TABLE_NAME,
            array(COL_SUPERVISOR_ID => $this->supervisorId,
                COL_SEMESTER_ID => $this->semesterId))[RESPONSE_DATA]);
    }

    function getSchedule() {
        return $this->transform($this->db->select(SCHEDULE_TABLE_NAME, array(COL_ID => $this->id))[RESPONSE_DATA]);
    }

   function updateSchedule() {
       return $this->db->update(SCHEDULE_TABLE_NAME,
           array(COL_STATUS => $this->status),
           array(COL_ID => $this->id),
           array(COL_STATUS));
   }

   function deleteSchedule() {
       return $this->db->delete(SCHEDULE_TABLE_NAME, array(COL_SUPERVISOR_ID => $this->supervisorId,
           COL_ID => $this->id));
   }

   function isOwner() {
       return $this->db->select(SCHEDULE_TABLE_NAME,
               array(COL_ID => $this->id, COL_SUPERVISOR_ID => $this->supervisorId))[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS;
   }

    function transform($content) {
        return array_map(function ($content) {
            return array(
                'id' => $content[COL_ID],
                'name'=> $content[COL_NAME],
                'timeStart'=> $content[COL_TIME_START],
                'timeEnd'=> $content[COL_TIME_END],
                'day' => $content[COL_DAY],
                'studentLimit' => $content[COL_STUDENT_LIMIT],
                'supervisorId' => $content[COL_SUPERVISOR_ID],
                'message' => $content[COL_MESSAGE],
                'status' => $content[COL_STATUS],
                'semesterId' => $content[COL_SEMESTER_ID]
            );
        }, $content);
    }

}