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
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
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
}
?>
