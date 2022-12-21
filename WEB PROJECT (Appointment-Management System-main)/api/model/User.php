<?php
require_once '../DbHelper.php';
require_once '../utils/constants.php';
require_once 'UserType.php';


define("REQUIRED_COLUMNS", array(COL_FIRSTNAME, COL_SURNAME, COL_EMAIL, COL_PASSWORD, COL_TYPE));
define("SEARCH_COLUMNS", array(COL_FIRSTNAME, COL_SURNAME, COL_EMAIL, COL_ID, COL_TYPE));

class User
{
    private DbHelper $dbHelper;
    public $id;
    public $firstName;
    public $surname;
    public $email;
    public $password;
    public $type;
    public $profileImageUrl;

    function __construct(DbHelper $db)
    {
        $this->dbHelper = $db;
    }

    function addUser() {
        $user = null;
        if (isset($this->email)) {
            $user = $this->filterUsers($this->email, COL_EMAIL);
        }
        if ($user[RESPONSE_STATUS] != DbResponse::STATUS_SUCCESS) {
            return $this->dbHelper->insert(USER_TABLE_NAME, array(
                COL_FIRSTNAME => $this->firstName,
                COL_TYPE => $this->type,
                COL_SURNAME => $this->surname,
                COL_EMAIL => $this->email,
                COL_PASSWORD => password_hash($this->password, PASSWORD_DEFAULT),
                COL_PROFILE_PICTURE => $this->profileImageUrl
            ), REQUIRED_COLUMNS);
        } else {
            $response[RESPONSE_STATUS] = DbResponse::STATUS_ERROR;
            $response[RESPONSE_MESSAGE] = "User already exists";
            $response[RESPONSE_DATA] = null;
            return $response;
        }
    }

    private function filterUsers($value, $colName): array{
        return $this->dbHelper->select(USER_TABLE_NAME, array($colName => $value));
    }

    function getUserByEmail($email) {
        return $this->filterUsers($email, COL_EMAIL);
    }

    function getUserById($id) {
        return $this->filterUsers($id, COL_ID)[RESPONSE_DATA];
    }

    function getUsers(): array {
        return $this->dbHelper->select(USER_TABLE_NAME, array());
    }

    function isSupervisor(): bool {
//        print_r($this->getUsers());
        return $this->dbHelper->select(USER_TABLE_NAME,
                array(COL_ID => $this->id, COL_TYPE => UserType::supervisor))[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS;
    }

    function isStudent(): bool {
        return $this->dbHelper->select(USER_TABLE_NAME,
                array(COL_ID => $this->id, COL_TYPE => UserType::student))[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS;
    }

    function updateUserByEmail(String $email, array $values) {
        return $this->dbHelper->update(USER_TABLE_NAME, $values, array(COL_EMAIL => $email), array());
    }

    function updateUserById(int $id, array $values) {
        return $this->dbHelper->update(USER_TABLE_NAME, $values, array(COL_ID => $id), array());
    }

    function deleteUser(User $user) {
        if (isset($user->id)) {
            return $this->dbHelper->delete(USER_TABLE_NAME, array(COL_ID => $user->id));
        } else {
            return $this->dbHelper->delete(USER_TABLE_NAME, array(COL_EMAIL => $user->email));
        }
    }

   static function transform($content) {
        return array_map(function ($content) {
            return array(
                'id' => $content[COL_ID],
                'firstName'=> $content[COL_FIRSTNAME],
                'surname' => $content[COL_SURNAME],
                'email' => $content[COL_EMAIL],
                'type' => $content[COL_TYPE]
            );
        }, $content);
    }
}