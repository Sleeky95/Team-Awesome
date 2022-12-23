<?php
    include 'userGateway.php';
    class UserController {
        private $db;
        private $requestMethod;
        private $uri;
        private $authkey;

        private $userGateway;

        public function __construct($db, $requestMethod, $uri, $authkey) {
            $this->db = $db;
            $this->requestMethod = $requestMethod;
            $this->uri = $uri;
            $this->authkey = $authkey;

            $this->userGateway = new UserGateway($db);
        }

        public function processRequest() {
            $res = $this->userGateway->fetchSession($this->authkey);
            if (count($res) > 0) {                
            $result = $res[0];
            }
            if (count($res) > 0 and strtotime(date("Y-m-d H:i:s")) > strtotime($result["expiry"])) {
                $this->db->deleteSession($result["account_id"]);
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(array("message"=>"Session Expired, Please Login Again"));
            }
            switch ($this->requestMethod) {
             
                case 'GET':
                    if ($this->uri[1] == "admin") {
                        if (count($res) == 0 or $result["account_type"] != "ADMIN") {
                            header('HTTP/1.1 403 Forbidden');
                            return;
                        }
                       // extract dets to identify currently signed in admin
                        if ($this->uri[2] == "viewstudent") {
                            if (!isset($this->uri[3])) {              
                                $this->fetchAllStudents();
                            } else {
                                $studentId = (int) $this->uri[3];
                                $this->fetchStudent($studentId);
                            }
                        }

                        if ($this->uri[2] == "viewExaminers") {
                            if (!isset($this->uri[3])) {
                                $this->fetchAllExaminers();
                            } else {
                                $examinerId = (int) $this->uri[3];
                                $this->fetchExaminer($examinerId);
                            }
                        }
                    }

                    if ($this->uri[1] == "examiner") {
                        if (count($res) == 0 or $result["account_type"] != "EXAMINER") {
                            header('HTTP/1.1 403 Forbidden');
                            return;
                        }
                        if ($this->uri[2] == "viewSubmissions") {
                            $examinerId = (int) $result["account_id"];
                            //extract dets to identify signed in examiner
                            $this->fetchSubmissions($examinerId);
                        }
                    }

                    if ($this->uri[1] == "submission") {
                        if (count($res) == 0 or $result["account_type"] == "EXAMINER") {
                            header('HTTP/1.1 403 Forbidden');
                            return;
                        }

                        if (!isset($this->url[2])) {
                            switch ($result["account_type"]) {
                                case "ADMIN":
                                    $this->fetchAllSubmissions();
                                    break;
                                case "STUDENT":
                                    $studentId = (int) $result["account_id"];
                                    $this->fetchStudentSubmission($studentId);
                                    break;
                            }
                        }

                        if ($this->url[2] == "graded") {
                            switch ($result["account_type"]) {
                                case "ADMIN":
                                    $this->fetchAllGradedSubmissions();
                                    break;
                                case "STUDENT":
                                    $this->fetchStudentGradedSubmissions();
                                    break;

                            }
                        }
                    }
                                                
                    break;
                case 'POST':
                    if ($this->uri[1] == "admin") {
                        if ($this->uri[2] == "signup") {
                            $this->createUser();
                            return;
                        }

                        if ($this->uri[2] == "signin") {
                            $this->userSignIn("ADMIN");
                            return;
                        }
                      
                        if (count($res) == 0 or $result["account_type"] != "ADMIN") {
                            header('HTTP/1.1 403 Forbidden');
                            return;
                        }
                        
                        if ($this->uri[2] == "createAccount") {
                            // extract dets to identify currently signed in admin.....
                            $this->createUser();
                            return;
                        }

                        if ($this->uri[2] == "assign") {
                            $this->assignSubmission(); 
                            return;
                        }
                    }
                    var_dump($this->uri);
                    if ($this->uri[1] == "signout") {
                        if (count($res) > 0) {
                            $this->signOut($result["account_id"]);
                        }
                        return;
                    }

                    if ($this->uri[1] == "student") {
                        if ($this->uri[2] == "signin") {
                            $this->userSignIn("STUDENT");
                            return;
                        }
                        
                        if (count($res) == 0 or $result["account_type"] != "STUDENT") {
                            header('HTTP/1.1 403 Forbidden');                            
                            return;
                        }


                        if ($this->uri[2] == "submit") {
                            // handle extraction of signed in user and stuff
                            $studentId = (int) $result["account_id"];
                            $this->submit($studentId);
                            return;
                        }
                    }

                    if ($this->uri[1] == "examiner") {
                        if ($this->uri[2] == "signin") {
                            $this->userSignIn("EXAMINER");
                            return;
                        }

                        if (count($res) == 0 or $result["account_type"] != "EXAMINER") {
                            header('HTTP/1.1 403 Forbidden');
                            return;
                        }

                        if($this->uri[2] == "grade") {
                            if (isset($this->uri[3])) {
                               $examinerId = (int) $result["account_id"];
                               $submissionId = (int) $this->uri[3];
                               $this->grade($submissionId, $examinerId);
                            } else {
                                // return error and stuff here
                            }
                        }
                    }

                    break;
                case 'OPTIONS':
                    return;

                default:
                    //return method not allowed
                    header('HTTP/1.1 405 Method Not Allowed');
                    break;
            }
            
        }

        private function fetchAllStudents() {
            $result = $this->userGateway->fetchAllStudents();
            header('HTTP/1.1 200 OK');
            echo json_encode($result);
        }

        private function fetchStudent($studentId) {
            $result = $this->userGateway->fetchStudent($studentId);
            if (count($result) == 0) {
                header('HTTP/1.1 404 Not Found');
                return;
            }          
             header('HTTP/1.1 200 OK');
             echo json_encode($result);
           
        }

        private function fetchAllExaminers() {
            $result = $this->userGateway->fetchAllExaminers();
            header('HTTP/1.1 200 OK');
            echo json_encode($result);
        }

        private function fetchExaminer($examinerId) {
            $result = $this->userGateway->fetchExaminer($examinerId);
            if ($result == null) {
                header('HTTP/1.1 404 Not Found');
                return;
            }
            header('HTTP/1.1 200 OK');
            echo json_encode($result);
        }

        private function fetchSubmissions($examinerId) {
            $result = $this->userGateway->fetchSubmissions($examinerId);
            header('HTTP/1.1 200 OK');
            echo json_encode($result);
        }

        private function fetchSubmissionSections($submissionId) {
            $result = $this->userGateway->fetchSubmissionSections($submissionId);
            header('HTTP/1.1 200 OK');
            echo json_encode($result);
        }

        private function fetchAllSubmissions() {
        
        }

        private function fetchStudentSubmissions($studentId) {
        
        }

        private function fetchAllGradedSubmission() {
        
        }

        private function fetchStudentGradedSubmission($studentId) {
        
        }


        private function createUser() {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            if ($input["full_name"] == "" or $input["identification_no"] == "" or $input["password"] == "" or $input["account_type"] == "") {
                header('HTTP/1.1 400 Bad Request');
                return;
            }
            $result = $this->userGateway->createAccount($input["full_name"], $input["identification_no"], $input["account_type"], $input["password"]);
             if ($result === 0) {
                header('HTTP/1.1 500 Internal Server Error');
                return;
             }
             header('HTTP/1.1 200 OK');                
         }

        private function assignSubmission() {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            if ($input["submission_id"] == "" || $input["examiner_id"] == "") {
                header('HTTP/1.1 400 Bad Request');
                return;
            }
                $submission = $this->userGateway->checkSubmissionExists($input["submission_id"]);
            $examiner = $this->userGateway->checkExaminerExists($input["examiner_id"]);
            echo $input["examiner_id"];
            echo "Examiner ".$examiner;
                if (!$submission or !$examiner) {
                   header('HTTP/1.1 404 Not Exists');
                   return;
                }
                $result = $this->userGateway->assignSubmission($input["submission_id"], $input["examiner_id"]);
                echo $result;
                    if ($result === 0) {
                        header('HTTP/1.1 500 Internal Server Error');
                        return;
                    } 
                header('HTTP/1.1 202 Accepted');
                return;
        }
  

        private function userSignIn($accountType) {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            if ($input["identification_no"] == "" or $input["password"] == "") {
                header('HTTP/1.1 403 Forbidden');
                return;
            }

            // insecure as hell, but it's 3:00AM And I'm Tired AF.
            $res = $this->userGateway->fetchSignInDetails($input["identification_no"]);

            if (count($res) == 0) {
                header('HTTP/1.1 404 Not Found');
                return;
            }

            $res = $res[0];

            if ($res["password"] != $input["password"] or $res["account_type"] != $accountType) {
                header('HTTP/1.1 403 Forbidden');
                return;
            }

            $session_id = $this->random_str();
            $expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));

            $result = $this->userGateway->storeSession($session_id, $res["id"], $res["account_type"], $expiry);

            if ($result == 0) {
                header('HTTP/1.1 500 Internal Server Error');
                return;
            }

            header('HTTP/1.1 200 OK');
            echo json_encode(array("authorization" => $session_id));
        }

        private function signOut($accountId) {
            echo $accountId;
            $this->userGateway->deleteSession($accountId);
            header('HTTP/1.1 200 OK');
            return;
        }

        private function submit($studentId) {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);

            if (count($input) == 0 or $input["title"] == "" or count($input["sections"]) == 0) {
                header('HTTP/1.1 400 Bad Request');
                return;
            }

            $result = $this->userGateway->submit($input, $studentId);
            if ($result == 0) {
                header('HTTP/1.1 500 Internal Server Error');
                return;
            }

            header('HTTP/1.1 201 Created');
            return;
        }

        private function gradeSubmission($submissionId, $examinerId) {
            $input = json_decode(file_get_contents('php://input'), TRUE);

            if (count($input) == 0) {
                header('HTTP/1.1 400 Bad Request');
                return;
            }

            $result = $this->userGateway->grade($input, -1, $examinerId);
        
        }

        private function random_str(int $length = 64, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') : string {
            $pieces = [];
            $max = mb_strlen($keyspace, '8bit') - 1;
            for ($i = 0; $i < $length; ++$i) {
                $pieces []= $keyspace[random_int(0, $max)];
            }

            return implode('', $pieces);
        }
    } 

?>
