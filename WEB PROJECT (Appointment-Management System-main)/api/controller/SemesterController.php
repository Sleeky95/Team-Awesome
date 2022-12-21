<?php
require_once '../utils/helpers.php';
require_once '../utils/constants.php';
require_once '../model/Semester.php';

class SemesterController
{
    private Semester $semester;
    function __construct(DbHelper $db)
    {
        $this->semester = new Semester($db);
    }

    function createSemester($semester) {
        $this->semester->name = $semester->name;
        $response = $this->semester->createSemester();
        if ($response[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS) {
            return success("Semester created successfully", $response[RESPONSE_DATA]);
        } else {
            return error($response[RESPONSE_MESSAGE]);
        }
    }

    function getSemesters() {
        return success("Semesters requested",$this->semester->getSemesters());
    }

    function getSemesterById($id)
    {
        return success("Semesters requested", $this->semester->getSemesterById($id));
    }

    function getSemesterByName($name)
    {
        return success("Semesters requested",$this->semester->getSemesterByName($name));
    }

    function deleteSemester($id) {
        $this->semester->deleteSemesterById($id);
        return success("Semester id ". $id . " deleted", null);
    }
}