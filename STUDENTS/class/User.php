<?php
session_start();
//require('include/config.php');
//echo $_SERVER['DOCUMENT_ROOT'];
include_once(__DIR__ . "/../../config.php");

class User extends Dbconfig
{
    // Change access level to protected
    protected $userTable = 'user';

    private $no;

    private $dbConnect = false;
    public function __construct()
    {
        if (!$this->dbConnect) {
            $database = new dbConfig();
            $this->hostName = $database->serverName;
            $this->userName = $database->userName;
            $this->password = $database->password;
            $this->dbName = $database->dbName;
            $conn = new mysqli($this->hostName, $this->userName, $this->password, $this->dbName);
            if ($conn->connect_error) {
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else {
                $this->dbConnect = $conn;
            }
        }
    }
    private function getData($sqlQuery)
    {
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            die('Error in query: ' . mysqli_error($this->dbConnect));
        }
        $data = array();
        // Fetch associative array using mysqli
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    private function getNumRows($sqlQuery)
    {
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            die('Error in query: ' . mysqli_error($this->dbConnect));
        }
        $numRows = mysqli_num_rows($result);
        return $numRows;
    }
    public function totalUsers($status = '')
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->userTable;
        if($status !== ''){
            $statusEscaped = mysqli_real_escape_string($this->dbConnect, $status);
            $sql .= " WHERE status = '" . $statusEscaped . "'";
        }
        $result = mysqli_query($this->dbConnect, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
    /**
     * Get designation options for select dropdown.
     *
     * @return string HTML <option> elements for designations.
     */
    public function getDesignationOptions()
    {
        $options = '';
        $sql = "SELECT designation_id, designation_name FROM " . $this->designationTable . " ORDER BY designation_id";
        $records = $this->getData($sql);
        foreach ($records as $row) {
            $id   = htmlspecialchars($row['designation_id'], ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($row['designation_name'], ENT_QUOTES, 'UTF-8');
            $options .= "<option value=\"{$id}\">{$name}</option>";
        }
        return $options;
    }
    public function loginStatus()
    {
        if (empty($_SESSION["userid"])) {
            header("Location: login.php");
        }
    }
    public function isLoggedin()
    {
        if (!empty($_SESSION["adminUserid"])) {
            return true;
        } else {
            return false;
        }
    }
    public function login()
    {
        $errorMessage = '';
        if (!empty($_POST["login"]) && $_POST["loginId"] != '' && $_POST["loginPass"] != '') {
            $loginId = $_POST['loginId'];
            $password = $_POST['loginPass'];
            if (isset($_COOKIE["loginPass"]) && $_COOKIE["loginPass"] == $password) {
                $password = $_COOKIE["loginPass"];
            } else {
                $password = md5($password);
            }
            $sqlQuery = "SELECT * FROM " . $this->userTable . " 
                    WHERE email='" . $loginId . "' AND password='" . $password . "' AND status = 'active' AND designation='4'";
            $resultSet = mysqli_query($this->dbConnect, $sqlQuery);
            $isValidLogin = mysqli_num_rows($resultSet);
            if ($isValidLogin) {
                if (!empty($_POST["remember"]) && $_POST["remember"] != '') {
                    setcookie("loginId", $loginId, time() + (10 * 365 * 24 * 60 * 60));
                    setcookie("loginPass", $password, time() + (10 * 365 * 24 * 60 * 60));
                } else {
                    $_COOKIE['loginId'] = '';
                    $_COOKIE['loginPass'] = '';
                }
                $userDetails = mysqli_fetch_assoc($resultSet);
                $_SESSION["userid"] = $userDetails['id'];
                $_SESSION["name"] = $userDetails['first_name'] . " " . $userDetails['last_name'];
                header("location: home.php");
            } else {
                $errorMessage = "Invalid login!";
            }
        } else if (!empty($_POST["loginId"])) {
            $errorMessage = "Enter Both username and password!";
        }
        return $errorMessage;
    }
    public function adminLoginStatus()
    {
        if (empty($_SESSION["adminUserid"])) {
            header("Location: home.php");
        }
    }
    public function adminLogin()
    {
        $errorMessage = '';
        if (!empty($_POST["login"]) && $_POST["email"] != '' && $_POST["password"] != '') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $sqlQuery = "SELECT * FROM " . $this->userTable . " 
                    WHERE email='" . $email . "' AND password='" . md5($password) . "' AND status = 'active' AND (designation='1' OR type = 'administrator')";
            $resultSet = mysqli_query($this->dbConnect, $sqlQuery);
            $isValidLogin = mysqli_num_rows($resultSet);
            if ($isValidLogin) {
                $userDetails = mysqli_fetch_assoc($resultSet);
                $_SESSION["adminUserid"] = $userDetails['id'];
                $_SESSION["admin"] = $userDetails['first_name'] . " " . $userDetails['last_name'];
                header("location: dashboard.php");
            } else {
                $errorMessage = "Invalid login!";
            }
        } else if (!empty($_POST["login"])) {
            $errorMessage = "Enter Both user and password!";
        }
        return $errorMessage;
    }
    public function register()
    {
        $message = '';
        if (!empty($_POST["register"]) && $_POST["email"] != '') {
            $sqlQuery = "SELECT * FROM " . $this->userTable . " 
                    WHERE email='" . $_POST["email"] . "'";
            $result = mysqli_query($this->dbConnect, $sqlQuery);
            $isUserExist = mysqli_num_rows($result);
            if ($isUserExist) {
                $message = "User already exist with this email address.";
            } else {
                $authtoken = $this->getAuthtoken($_POST["email"]);
                $mobile = isset($_POST["mobile"]) ? $_POST["mobile"] : '';
                $insertQuery = "INSERT INTO " . $this->userTable . " (first_name, last_name, email, password, authtoken, mobile, designation) VALUES ('" . $_POST["firstname"] . "','" . $_POST["lastname"] . "','" . $_POST["email"] . "','" . md5($_POST["passwd"]) . "','" . $authtoken . "','" . $mobile . "', '4')";
                $userSaved = mysqli_query($this->dbConnect, $insertQuery);
                if ($userSaved) {
                    $link = "<a href='../verify.php?authtoken=" . $authtoken . "'>Verify Email</a>";
                    $toEmail = $_POST["email"];
                    $subject = "Verify email to complete registration";
                    $msg = "Hi there, click on this " . $link . " to verify email to complete registration.";
                    $msg = wordwrap($msg, 70);
                    $headers = "From: info@sbit.edu.my";
                    if (mail($toEmail, $subject, $msg, $headers)) {
                        $message = "Verification email send to your email address. Please check email and verify to complete registration.";
                    }
                } else {
                    $message = "User register request failed.";
                }
            }
        }
        return $message;
    }
    public function getAuthtoken($email)
    {
        $code = md5(889966);
        $authtoken = $code . "" . md5($email);
        return $authtoken;
    }
    public function verifyRegister()
    {
        $verifyStatus = 0;
        if (!empty($_GET["authtoken"]) && $_GET["authtoken"] != '') {
            $sqlQuery = "SELECT * FROM " . $this->userTable . " 
                    WHERE authtoken='" . $_GET["authtoken"] . "'";
            $resultSet = mysqli_query($this->dbConnect, $sqlQuery);
            $isValid = mysqli_num_rows($resultSet);
            if ($isValid) {
                $userDetails = mysqli_fetch_assoc($resultSet);
                $authtoken = $this->getAuthtoken($userDetails['email']);
                if ($authtoken == $_GET["authtoken"]) {
                    $updateQuery = "UPDATE " . $this->userTable . " SET status = 'active'
                            WHERE id='" . $userDetails['id'] . "'";
                    $isUpdated = mysqli_query($this->dbConnect, $updateQuery);
                    if ($isUpdated) {
                        $verifyStatus = 1;
                    }
                }
            }
        }
        return $verifyStatus;
    }
    public function getUser_Student()
    {
        $userID = $_SESSION["userid"] ?? 0;
        $sql = "SELECT * FROM " . $this->userTable . " WHERE id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Populate class selection options for the student
    public function getSubjectOptions()
    {
        $userID = $_SESSION["userid"] ?? 0;
        // Fetch linked student_id
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            return '';
        }
        $row = mysqli_fetch_assoc($result);
        $studentID = $row['student_id'];
        // Fetch classes for this student
        $sql = "SELECT sc.class_id, c.name FROM " . $this->student_classTable . " sc
                JOIN " . $this->classesTable . " c ON c.id = sc.class_id
                WHERE sc.student_id='" . $studentID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        $options = '';
        while ($rec = mysqli_fetch_assoc($result)) {
            $cid = $rec['class_id'];
            $cname = $rec['name'];
            $selected = (isset($_POST['selClass']) && intval($_POST['selClass']) === (int)$cid) ? 'selected' : '';
            $options .= "<option value=\"{$cid}\" {$selected}>{$cname}</option>";
        }
        return $options;
    }

    // Get general report info for selected class
    public function getUser_Report()
    {
        if (empty($_POST['selClass'])) {
            return [];
        }
        $classID = intval($_POST['selClass']);
        $userID = $_SESSION["userid"] ?? 0;
        // Fetch linked student_id
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            return [];
        }
        $row = mysqli_fetch_assoc($result);
        $studentID = $row['student_id'];
        // Section name (Intake)
        $sql = "SELECT ss.section AS sSectionName FROM " . $this->sectionsTable . " ss
                JOIN " . $this->studentTable . " st ON st.section = ss.section_id
                WHERE st.id='" . $studentID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        $secName = '';
        if ($result && mysqli_num_rows($result) > 0) {
            $tmp = mysqli_fetch_assoc($result);
            $secName = $tmp['sSectionName'];
        }
        // Class info
        $sql = "SELECT name AS programName, start_date FROM " . $this->classesTable . " WHERE id='" . $classID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        $programName = '';
        $startDate = '';
        if ($result && mysqli_num_rows($result) > 0) {
            $tmp = mysqli_fetch_assoc($result);
            $programName = $tmp['programName'];
            $startDate = $tmp['start_date'];
        }
        // Full name
        $fullName = '';
        $sql = "SELECT first_name, last_name FROM " . $this->userTable . " WHERE id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $tmp = mysqli_fetch_assoc($result);
            $fullName = trim($tmp['first_name'] . ' ' . $tmp['last_name']);
        }
        return [
            'programName'  => $programName,
            'fullName'     => $fullName,
            'sSectionName' => $secName,
            'start_date'   => $startDate
        ];
    }

    // Get student grades for selected class
    public function getUser_Grades()
    {
        if (empty($_POST['selClass'])) {
            return false;
        }
        $classID = intval($_POST['selClass']);
        $userID = $_SESSION["userid"] ?? 0;
        // Fetch linked student_id
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        $studentID = $row['student_id'];
        // Fetch grades
        $sql = "SELECT sc_homework, sc_participation, sc_exam, sc_project, s2_exam, s3_exam
                FROM " . $this->student_gradesTable . "
                WHERE student_id='" . $studentID . "' AND class_id='" . $classID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return false;
    }

    /**
     * Count of classes the student is enrolled in
     */
    public function getClassCount()
    {
        $userID = $_SESSION["userid"] ?? 0;
        // find student_id
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        $sid = $row['student_id'];
        // count classes
        $sql = "SELECT COUNT(*) AS cnt FROM " . $this->student_classTable . " WHERE student_id='" . $sid . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        return intval($row['cnt']);
    }

    /**
     * Count of attendance records for the student
     */
    public function getAttendanceCount()
    {
        $userID = $_SESSION["userid"] ?? 0;
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return 0;
        }
        $sid = mysqli_fetch_assoc($result)['student_id'];
        $sql = "SELECT COUNT(*) AS cnt FROM " . $this->attendanceTable . " WHERE student_id='" . $sid . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        return intval($row['cnt']);
    }

    /**
     * Count of exams taken by the student
     */
    public function getExamCount()
    {
        $userID = $_SESSION["userid"] ?? 0;
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return 0;
        }
        $sid = mysqli_fetch_assoc($result)['student_id'];
        $sql = "SELECT COUNT(*) AS cnt FROM " . $this->student_gradesTable . " WHERE student_id='" . $sid . "' AND sc_exam_date IS NOT NULL";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        return intval($row['cnt']);
    }

    /**
     * Count of projects submitted by the student
     */
    public function getProjectCount()
    {
        $userID = $_SESSION["userid"] ?? 0;
        $sql = "SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $userID . "'";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return 0;
        }
        $sid = mysqli_fetch_assoc($result)['student_id'];
        $sql = "SELECT COUNT(*) AS cnt FROM " . $this->student_gradesTable . " WHERE student_id='" . $sid . "' AND sc_project IS NOT NULL";
        $result = mysqli_query($this->dbConnect, $sql);
        if (!$result) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        return intval($row['cnt']);
    }
    public function editAccount()
    {
        $fileName = '';
        if(isset($_FILES["photo"]) && !empty($_FILES["photo"]["name"])) {
            $target_dir = "../SCHOOL/upload/";
            $fileName = time() . $_FILES["photo"]["name"];
            $targetFile = $target_dir . basename($fileName);
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                echo "The file $fileName has been uploaded.";
                $imageUpdate = ", image = '$fileName' ";
                $photoUpdate = ", photo = '$fileName' ";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        if ($fileName == '') {
            $imageUpdate = '';
            $photoUpdate = '';
        }
        $message = '';
        $updatePassword = '';
        if (empty($_POST["passwd"])) {
            $message = "Password must be entered to update account details.";
            return $message;
        } else if (empty($_POST["cpasswd"])) {
            $message = "Enter Confirm Password.";
            return $message;
        } else if ($_POST["passwd"] != $_POST["cpasswd"]) {
            $message = "Password and Confirm Password does not match.";
        } else if ($_POST["passwd"] == $_POST["cpasswd"]) {
            $updatePassword = ", password='" . md5($_POST["passwd"]) . "' ";

            $updateQueryA = "UPDATE " . $this->userTable . " 
                    SET first_name = '" . $_POST["firstname"] . "', last_name = '" . $_POST["lastname"] . "', email = '" . $_POST["email"] . "', mobile = '" . $_POST["mobile"] . "' , designation = '4', gender = '" . $_POST["gender"] . "' $imageUpdate $updatePassword
                    WHERE id ='" . $_SESSION["userid"] . "'";
            mysqli_query($this->dbConnect, $updateQueryA);

            $updateQueryB = "UPDATE " . $this->studentTable . " 
                    SET name = '" . $_POST["firstname"] . "', email = '" . $_POST["email"] . "', mobile = '" . $_POST["mobile"] . "' , gender = '" . $_POST["gender"] . "' $photoUpdate
                    WHERE id = (SELECT student_id FROM " . $this->user_studentTable . " WHERE user_id='" . $_SESSION["userid"] . "')";
            mysqli_query($this->dbConnect, $updateQueryB);

            $isUpdated = ($updateQueryA && $updateQueryB);
            if ($isUpdated) {
                $_SESSION["name"] = $_POST['firstname'] . " " . $_POST['lastname'];
            }
        }
        return $message;
    }

    /*****************Admin User methods****************/
    public function listUser() {
        // formerly getUserList
        $sqlQuery = "SELECT * FROM " . $this->userTable . " ";
        if (!empty($_POST["search"]["value"])) {
            $sqlQuery .= ' OR id LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%" ';
        }
        if (!empty($_POST["order"])) {
            $sqlQuery .= 'ORDER BY ' . $_POST['order'][0]['column'] . ' ' . $_POST['order'][0]['dir'] . ' ';
        } else {
            $sqlQuery .= 'ORDER BY id ASC ';
        }
        if ($_POST["length"] != -1) {
            $sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        $numRows = mysqli_num_rows($result);
        $userData = array();
        $no = 1;
        while ($users = mysqli_fetch_assoc($result)) {
            $userRows = array();
            $userRows[] = $no; $no++;
            $status = '';
            if ($users['status'] == 'active') {
                $status = '<span class="label label-success">Active</span>';
            } elseif ($users['status'] == 'pending') {
                $status = '<span class="label label-warning">Inactive</span>';
            } elseif ($users['status'] == 'deleted') {
                $status = '<span class="label label-danger">Deleted</span>';
            }
            $userRows[] = $users['id'];
            $userRows[] = ucfirst($users['first_name'] . " " . $users['last_name']);
            $userRows[] = $users['gender'];
            $userRows[] = $users['email'];
            $userRows[] = $users['mobile'];
            $userRows[] = $users['type'];
            $userRows[] = $status;
            $userRows[] = '<button type="button" name="update" id="' . $users["id"] . '" class="btn btn-warning btn-xs update">Update</button>';
            $userRows[] = '<button type="button" name="delete" id="' . $users["id"] . '" class="btn btn-danger btn-xs delete">Delete</button>';
            $userData[] = $userRows;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $numRows,
            "recordsFiltered" => $numRows,
            "data" => $userData
        );
        echo json_encode($output);
    }
    public function addUser() {
        if (!empty($_POST["email"])) {
            $authtoken = $this->getAuthtoken($_POST['email']);
            $insertQuery = "INSERT INTO " . $this->userTable . " (first_name, last_name, email, gender, password, mobile, designation, type, status, authtoken)\n                VALUES ('" . $_POST["firstname"] . "', '" . $_POST["lastname"] . "', '" . $_POST["email"] . "', '" . $_POST["gender"] . "', '" . md5($_POST["password"]) . "', '" . $_POST["mobile"] . "', '3', 'general', 'active', '" . $authtoken . "')";
            mysqli_query($this->dbConnect, $insertQuery);
        }
    }
    public function updateUser() {
        if (!empty($_POST['userid'])) {
            $updateQuery = "UPDATE " . $this->userTable . "\n                SET first_name = '" . $_POST["firstname"] . "', last_name = '" . $_POST["lastname"] . "', email = '" . $_POST["email"] . "', mobile = '" . $_POST["mobile"] . "', gender = '" . $_POST["gender"] . "', status = '" . $_POST["status"] . "'\n                WHERE id = '" . $_POST["userid"] . "'";
            mysqli_query($this->dbConnect, $updateQuery);
        }
    }
    public function deleteUser() {
        if (!empty($_POST["userid"])) {
            $sqlUpdate = "UPDATE " . $this->userTable . " SET status = 'deleted' WHERE id = '" . $_POST["userid"] . "'";
            mysqli_query($this->dbConnect, $sqlUpdate);
        }
    }
    public function getUserDetails() {
        if (!empty($_POST["userid"])) {
            $sqlQuery = "SELECT * FROM " . $this->userTable . " WHERE id = '" . $_POST["userid"] . "'";
            $result = mysqli_query($this->dbConnect, $sqlQuery);
            $userDetails = mysqli_fetch_assoc($result);
            echo json_encode($userDetails);
        }
    }
}
?>
