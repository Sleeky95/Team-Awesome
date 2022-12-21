<?php

require_once '../DbHelper.php';
require_once '../utils/constants.php';

class Semester
{
    public int $id;
    public string $name;
    private DbHelper $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function createSemester() {
        return $this->db->insert(SEMESTER_TABLE_NAME,
            array(
                COL_NAME => $this->name
            ),
            array(COL_NAME)
        );
    }

    function deleteSemesterById(int $id) {
        return $this->db->delete(SEMESTER_TABLE_NAME, array(COL_ID => $id));
    }

    function deleteSemesterByName(string $name) {
        return $this->db->delete(SEMESTER_TABLE_NAME, array(COL_NAME => $name));
    }

    function getSemesters() {
        return $this->transform($this->db->select(SEMESTER_TABLE_NAME, array())[RESPONSE_DATA]);
    }

    function getSemesterById(int $id) {
        return $this->transform($this->db->select(SEMESTER_TABLE_NAME, array(COL_ID => $id))[RESPONSE_DATA]);
    }

    function getSemesterByName(string $name) {
        return $this->transform($this->db->select(SEMESTER_TABLE_NAME, array(COL_NAME => $name))[RESPONSE_DATA]);
    }

    function transform($content) {
        return array_map(function ($content) {
            return array(
                'id' => $content['id'],
                'name'=> $content['name']
            );
        }, $content);
    }

}