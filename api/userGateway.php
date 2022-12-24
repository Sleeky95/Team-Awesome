<?php
class UserGateway {
    private $db = null;

    public function __construct($db) {
        $this->db = $db;
    }

    public function fetchAllStudents() {
        $statement = "
            SELECT 
                id, full_name, identification_no, account_type 
            FROM 
                account 
            WHERE 
                account_type = ?
        ";

         try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array("STUDENT"));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
         } catch (PDOException $e) {
            exit($e->getMessage());
         }
    }

    public function fetchStudent($id) {
        $statement = "SELECT id, full_name, identification_no, account_type FROM account WHERE id = ? AND account_type = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id, "STUDENT"));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function fetchAllExaminers() {
        $statement = "SELECT id, full_name, identification_no, account_type FROM account WHERE account_type = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array("EXAMINER"));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOExecption $e) {
            exit($e->getMessage());
        }
    }

    public function fetchSubmissions($examinerId) {
        $statement = "SELECT s.id, s.title, s.created_at FROM submission_examiners se JOIN submission s ON se.submission_id = s.id WHERE se.examiner_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($examinerId));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function fetchSubmissionSections($submissionId) {
        $statement = "SELECT id, text FROM submission_section WHERE submission_id = ?";

        try {
            $statement = $thi->db->prepare($statement);
            $statement->execute(array($submissionId));
            $result->$statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function fetchAllSubmissions() {
        
    }

    public function createAccount($full_name, $identification_no, $account_type, $password) {
        $statement = "INSERT INTO account (full_name, identification_no, account_type, password) VALUES (?, ?, ?, ?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($full_name, $identification_no, $account_type, $password));
            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function assignSubmission($submissionId, $examinerId) {
        $statement = "INSERT INTO submission_examiners (submission_id, examiner_id) VALUES (?, ?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($submissionId, $examinerId));
            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function fetchSignInDetails($identification_no) {
        $statement = "SELECT id, account_type, password FROM account WHERE identification_no = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($identification_no));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function submit(Array $submission, $studentId) {
        $title = $submission["title"];
        $submission_id = null;
        $statement = "INSERT INTO submission (title, submitted_by) VALUES (?, ?)";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($title, $studentId));
            $submission_id = $this->db->lastInsertId();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }

        $statement = "INSERT INTO submission_section (submission_id, section_id, text) VALUES (?, ?, ?)";
        $statement = $this->db->prepare($statement);

        foreach ($submission["sections"] as $index) {
            foreach ($index as $section => $content) {
                try {    
                    $statement->execute(array($submission_id, $section, $content));
                } catch (PDOException $e) {
                    exit($e->getMessage());
                }
            }
        }

        return $statement->rowCount();
    }

    public function checkSubmissionExists($submission_id) : bool {
        $statement = "SELECT id FROM submission WHERE id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($submission_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return count($result) > 0;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
     public function checkExaminerExists($examiner_id) : bool {
        $statement = "SELECT id FROM account WHERE id = ? AND account_type = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($examiner_id, "EXAMINER"));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return count($result) > 0;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
     }

    public function grade(Array $submission_section, $examinerId) {
        $statement = "INSERT INTO examiner_marking (submission_section_id, score, examiner_id) VALUES (?, ?, ?)";
        $statement = $this->db->prepare($statement);

        foreach($submission_section as $section_id => $score) {
            try {
              $statement->execute(array($section_id, $score, $examinerId));
            } catch (PDOException $e) {
                exit($e->getMessage());
            }
        }
    
    }

    public function storeSession($session_id, $user_id, $account_type, $expiry) {
        $statement = "INSERT INTO sessions (account_id, session_id, account_type, expiry) VALUES (?, ?, ?, ?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($user_id, $session_id, $account_type, $expiry));
            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function fetchSession($session_key) {
        $statement = "SELECT account_id, account_type, expiry FROM sessions WHERE session_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($session_key));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function deleteSession($account_id) {
        $statement = "DELETE FROM sessions WHERE account_id = ?";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($account_id));
            return $statement->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}
?>
