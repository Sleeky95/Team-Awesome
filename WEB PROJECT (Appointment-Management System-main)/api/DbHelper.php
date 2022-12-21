<?php
require_once 'config.php';
require_once 'utils/constants.php';
require_once 'model/DbResponse.php';

class DbHelper
{
    private PDO $db;

    function __construct() {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
        try {
            $this->db = new PDO($dsn, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
            $response[RESPONSE_MESSAGE] = 'Connection failed: ' . $e->getMessage();
            $response[RESPONSE_DATA] = null;
            header('HTTP/1.1 500 Internal server error');
            exit;
        }
    }

    function select($tableName, $where){
        try{
            $a = array();
            $w = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " like :".$key;
                $a[":".$key] = $value;
            }
            $stmt = $this->db->prepare("select * from ".$tableName." where 1=1 ". $w);
            $stmt->execute($a);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($rows)<=0){
                $response[RESPONSE_STATUS] = DbResponse::STATUS_WARNING;
                $response[RESPONSE_MESSAGE] = "No data found.";
            }else{
                $response[RESPONSE_STATUS] = DbResponse::STATUS_SUCCESS;
                $response[RESPONSE_MESSAGE] = "Data selected from database";
            }
            $response[RESPONSE_DATA] = $rows;
        }catch(PDOException $e){
            $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
            $response[RESPONSE_MESSAGE] = 'Select Failed: ' .$e->getMessage();
            $response[RESPONSE_DATA] = null;
        }
        return $response;
    }

    function insert($tableName, $columnsArray, $requiredColumnsArray) {
        $response = $this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
        if ($response != null) {
            return $response;
        }

        try{
            $a = array();
            $c = "";
            $v = "";
            foreach ($columnsArray as $key => $value) {
                $c .= $key. ", ";
                $v .= ":".$key. ", ";
                $a[":".$key] = $value;
            }
            $c = rtrim($c,', ');
            $v = rtrim($v,', ');
            $stmt =  $this->db->prepare("INSERT INTO $tableName($c) VALUES($v)");
            $stmt->execute($a);
            $affected_rows = $stmt->rowCount();
            $response[RESPONSE_STATUS] = DbResponse::STATUS_SUCCESS;
            $response[RESPONSE_MESSAGE] = $affected_rows." row inserted into database";
        }catch(PDOException $e){
            $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
            $response[RESPONSE_MESSAGE] = 'Insert Failed: ' .$e->getMessage();
        }
        return $response;
    }

    function update($tableName, $columnsArray, $where, $requiredColumnsArray){
        $this->verifyRequiredParams($columnsArray, $requiredColumnsArray);
        try{
            $a = array();
            $w = "";
            $c = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " = :".$key;
                $a[":".$key] = $value;
            }
            foreach ($columnsArray as $key => $value) {
                $c .= $key. " = :".$key.", ";
                $a[":".$key] = $value;
            }
            $c = rtrim($c,", ");

            $stmt =  $this->db->prepare("UPDATE $tableName SET $c WHERE 1=1 ".$w);
            $stmt->execute($a);
            $affected_rows = $stmt->rowCount();
            if($affected_rows<=0){
                $response[RESPONSE_STATUS] = DbResponse::STATUS_WARNING;
                $response[RESPONSE_MESSAGE] = "No row updated";
            }else{
                $response[RESPONSE_STATUS] = DbResponse::STATUS_SUCCESS;
                $response[RESPONSE_MESSAGE] = $affected_rows." row(s) updated in database";
            }
        }catch(PDOException $e){
            $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
            $response[RESPONSE_MESSAGE] = "Update Failed: " .$e->getMessage();
        }
        return $response;
    }

    function delete($tableName, $where){
        if(count($where)<=0){
            $response[RESPONSE_STATUS] = DbResponse::STATUS_WARNING;
            $response[RESPONSE_MESSAGE] = "Delete Failed: At least one condition is required";
        }else{
            try{
                $a = array();
                $w = "";
                foreach ($where as $key => $value) {
                    $w .= " and " .$key. " = :".$key;
                    $a[":".$key] = $value;
                }
                $stmt =  $this->db->prepare("DELETE FROM $tableName WHERE 1=1 ".$w);
                $stmt->execute($a);
                $affected_rows = $stmt->rowCount();
                if($affected_rows<=0){
                    $response[RESPONSE_STATUS] = DbResponse::STATUS_WARNING;
                    $response[RESPONSE_MESSAGE] = "No row deleted";
                }else{
                    $response[RESPONSE_STATUS] = DbResponse::STATUS_SUCCESS;
                    $response[RESPONSE_MESSAGE] = $affected_rows." row(s) deleted from database";
                }
            }catch(PDOException $e){
                $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
                $response[RESPONSE_MESSAGE] = 'Delete Failed: ' .$e->getMessage();
            }
        }
        return $response;
    }

    function verifyRequiredParams($inArray, $requiredColumns) {
        $error = false;
        $errorColumns = "";
        foreach ($requiredColumns as $field) {
            if (!isset($inArray[$field]) || strlen(trim($inArray[$field])) <= 0) {
                $error = true;
                $errorColumns .= $field . ', ';
            }
        }

        if ($error) {
            $response = array();
            $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
            $response[RESPONSE_MESSAGE] = 'Required field(s) ' . rtrim($errorColumns, ', ') . ' is missing or empty';
            return $response;
        }
        return null;
    }
}