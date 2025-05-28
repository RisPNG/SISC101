<?php
// Ensure session is started if not already (e.g., by my_action.php)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Adjust the path to your Dbconfig file.
// This assumes My_Student.php is in a subdirectory (e.g., 'classes')
// and config.php is in 'include' directory one level up.
require_once __DIR__ . '/../include/config.php'; // Or your actual path to Dbconfig

class Kelas extends Dbconfig {
    // !!! IMPORTANT: VERIFY AND UPDATE THESE TABLE NAMES !!!
    private $userTable = 'user';
    private $attendanceTable = 'sms_attendance';
    private $classesTable = 'sms_classes';
    private $sessionsTable = 'sms_sessions';
    private $class_sessionTable = 'sms_class_session';
    private $subjectsTable = 'sms_subjects';
    private $class_subjectTable = 'sms_class_subject';
    private $sectionsTable = 'sms_sections';
    private $teacherTable = 'sis_teacher'; // or sms_teacher
    private $designationTable = 'sms_designation';
    private $user_studentTable = 'sms_user_student';
    private $studentTable = 'sms_students';
    private $student_classTable = 'sms_student_class';
    private $programsTable = 'sms_programs';

    private $dbConnect = false;

    public function __construct(){
        if(!$this->dbConnect){
            $database = new dbConfig(); // Assuming dbConfig is the class name in config.php
            $this->serverName = $database->serverName;
            $this->userName = $database->userName;
            $this->password = $database->password;
            $this->dbName = $database->dbName;
            $conn = new mysqli($this->serverName, $this->userName, $this->password, $this->dbName);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
    }

    public function getKelasList(){
        $sqlQuery = "SELECT
            sc.student_id AS studentEnrollmentID, /* Using a different alias to avoid confusion with s.id */
            c.id AS cID,
            c.name AS Class,
            ss.session AS Session,
            m.subject AS Subject,
            i.section AS Section,
            t.teacher AS Teacher,
            u.status AS Status,
            u.id AS uID,
            s.id AS sID, /* This is sms_students.id */
            m.subject_id AS mID,
            i.section_id AS iID,
            t.teacher_id AS tID,
            ds.designation_name AS Designation,
            u.type AS Type,
            ss.start AS sTime,
            att.check_in,
            att.check_out
        FROM ".$this->userTable." AS u
        INNER JOIN ".$this->designationTable." AS ds ON ds.designation_id = u.designation
        INNER JOIN ".$this->user_studentTable." AS us ON us.user_id = u.id
        INNER JOIN ".$this->studentTable." AS s ON us.student_id = s.id
        LEFT JOIN ".$this->student_classTable." AS sc ON s.id = sc.student_id /* Student's specific class enrollments */
        LEFT JOIN ".$this->classesTable." AS c ON sc.class_id = c.id /* Details of the enrolled class */
        LEFT JOIN ".$this->teacherTable." AS t ON c.teacher_id = t.teacher_id
        LEFT JOIN ".$this->programsTable." AS p ON t.program_id = p.program_id
        LEFT JOIN ".$this->class_subjectTable." AS cm ON c.id = cm.class_id
        LEFT JOIN ".$this->subjectsTable." AS m ON cm.subject_id = m.subject_id
        LEFT JOIN ".$this->sectionsTable." AS i ON c.section = i.section_id
        LEFT JOIN ".$this->class_sessionTable." AS cs ON c.id = cs.class_id
        LEFT JOIN ".$this->sessionsTable." AS ss ON ss.session_id = cs.session_id
        LEFT JOIN ".$this->attendanceTable." AS att ON att.student_id = s.id AND att.class_id = c.id AND att.section_id = i.section_id AND att.attendance_date = CURDATE()
        WHERE u.id='".$_SESSION["userid"]."' AND c.id IS NOT NULL "; // Added c.id IS NOT NULL to ensure class data exists

        if(!empty($_POST["search"]["value"])){
            $search = mysqli_real_escape_string($this->dbConnect, $_POST["search"]["value"]);
            $sqlQuery .= " AND (c.name LIKE '%$search%' ";
            $sqlQuery .= " OR m.subject LIKE '%$search%' ";
            $sqlQuery .= " OR t.teacher LIKE '%$search%' ";
            $sqlQuery .= " OR i.section LIKE '%$search%') ";
        }

        if(!empty($_POST["order"])){
            // Sanitize column index
            $columnIndex = intval($_POST['order']['0']['column']);
            // Define allowable columns for ordering to prevent SQL injection
            // Adjust these based on your actual display and desired sortable columns
            // Note: Datatables sends column index based on VISIBLE columns by default
            // if some are hidden, mapping can be complex.
            // For now, using a simple mapping.
            $columnMap = [2 => 'c.name', 3 => 'ss.session', 4 => 'm.subject', 5 => 'i.section', 6 => 't.teacher'];
            if (isset($columnMap[$columnIndex])) {
                 $sqlQuery .= 'ORDER BY '.$columnMap[$columnIndex].' '.mysqli_real_escape_string($this->dbConnect, $_POST['order']['0']['dir']).' ';
            } else {
                 $sqlQuery .= 'ORDER BY c.id DESC '; // Default sort
            }
        } else {
            $sqlQuery .= 'ORDER BY c.id DESC ';
        }

        // Get total records without limit for pagination after filtering
        $resultTotal = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$resultTotal) {
             // Handle error
            $output = array(
                "draw"            => intval($_POST["draw"]),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => "SQL Error (total): " . mysqli_error($this->dbConnect)
            );
            header("Content-Type: application/json");
            echo json_encode($output);
            exit;
        }
        $numTotalRows = mysqli_num_rows($resultTotal);


        if($_POST["length"] != -1){
            $sqlQuery .= 'LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
        }

        $result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            $output = array(
                "draw"            => intval($_POST["draw"]),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => "SQL Error (data): " . mysqli_error($this->dbConnect) . " Query: " . $sqlQuery // Include query for debugging
            );
            header("Content-Type: application/json");
            echo json_encode($output);
            exit;
        }

        $numRows = mysqli_num_rows($result);
        $kelasData = array();
        $no = intval($_POST['start']) + 1;

        while( $kelas = mysqli_fetch_assoc($result) ) {
            $kelasRows = array();
            $row_identifier = $kelas['cID'] . "_" . $kelas['sID'] . "_" . $no; // More unique identifier

            $kelasRows[] = $no; $no++;
            $kelasRows[] = $kelas['cID']; // Hidden ID column
            $kelasRows[] = htmlspecialchars($kelas['Class']);
            $kelasRows[] = htmlspecialchars($kelas['Session']);
            $kelasRows[] = htmlspecialchars($kelas['Subject']);
            $kelasRows[] = htmlspecialchars($kelas['Section']);
            $kelasRows[] = htmlspecialchars($kelas['Teacher']);

            $status = '';
            if($kelas['Status'] == 'active')    {
                $status = '<span class="label label-success">Active</span>';
            } else if($kelas['Status'] == 'pending') {
                $status = '<span class="label label-warning">Inactive</span>';
            } else if($kelas['Status'] == 'deleted') {
                $status = '<span class="label label-danger">Deleted</span>';
            }
            $kelasRows[] = $status;

            // Scheduled Start Time (and hidden data)
            $spanIDs = '';
            $spanIDs .='<span style="display:none" id="uID_'.$row_identifier.'">'.htmlspecialchars($kelas['uID']).'</span>';
            $spanIDs .='<span style="display:none" id="sID_'.$row_identifier.'">'.htmlspecialchars($kelas['sID']).'</span>'; // Student ID from sms_students
            $spanIDs .='<span style="display:none" id="cID_'.$row_identifier.'">'.htmlspecialchars($kelas['cID']).'</span>'; // Class ID
            $spanIDs .='<span style="display:none" id="mID_'.$row_identifier.'">'.htmlspecialchars($kelas['mID']).'</span>'; // Subject ID
            $spanIDs .='<span style="display:none" id="iID_'.$row_identifier.'">'.htmlspecialchars($kelas['iID']).'</span>'; // Section ID
            $spanIDs .='<span style="display:none" id="tID_'.$row_identifier.'">'.htmlspecialchars($kelas['tID']).'</span>'; // Teacher ID
            $spanIDs .='<span style="display:block" id="startTime_'.$row_identifier.'">'.substr(htmlspecialchars($kelas['sTime']),0,5).'</span>';
            $kelasRows[] = $spanIDs;

            // Display Check-In Time
            $checkInTimeDisplay = $kelas['check_in'] ? date('H:i:s', strtotime($kelas['check_in'])) : '-';
            $kelasRows[] = $checkInTimeDisplay;

            // Display Check-Out Time
            $checkOutTimeDisplay = $kelas['check_out'] ? date('H:i:s', strtotime($kelas['check_out'])) : '-';
            $kelasRows[] = $checkOutTimeDisplay;

            // Buttons
            $checkInDisabled = $kelas['check_in'] ? 'disabled' : '';
            $checkOutDisabled = ($kelas['check_in'] && !$kelas['check_out']) ? '' : 'disabled';

            $twoButtons = '<button type="button" name="checkIn" class="btn btn-success btn-xs checkInBtn" data-row-id="'.$row_identifier.'" '.$checkInDisabled.'> In </button> ';
            $twoButtons .= '<button type="button" name="checkOut" class="btn btn-info btn-xs checkOutBtn" data-row-id="'.$row_identifier.'" '.$checkOutDisabled.'>Out </button>';
            $kelasRows[] = $twoButtons;

            $kelasData[] = $kelasRows;
        }
        $output = array(
            "draw"            => intval($_POST["draw"]),
            "recordsTotal"    => $numTotalRows, // Total records before limiting (for pagination)
            "recordsFiltered" => $numTotalRows, // Total records after filtering (if search applied)
            "data"            => $kelasData
        );

        header("Content-Type: application/json");
        echo json_encode($output);
        exit;
    }

    public function addKelas($io) { // $io: 1 for Check-In, 2 for Check-Out
        // Sanitize inputs
        $student_id = intval($_POST["studentid"]);
        $class_id = intval($_POST["classid"]);
        $section_id = intval($_POST["sectionid"]);
        $attendance_date = mysqli_real_escape_string($this->dbConnect, $_POST["attendancedate"]);
        $attendance_time = mysqli_real_escape_string($this->dbConnect, $_POST["attendancetime"]);
        $attendance_status = intval($_POST["attendancestatus"]);
        $score = floatval($_POST["score"]); // Assuming score is a float

        if ($io == "1") { //Check In
            $selectQuery = "SELECT * FROM ".$this->attendanceTable." WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date."'";
            $result = mysqli_query($this->dbConnect, $selectQuery);
            if ($result && mysqli_num_rows($result) == 0) {
                $insertQuery = "INSERT INTO ".$this->attendanceTable." (student_id, class_id, section_id, attendance_status, attendance_date, check_in, score)
                VALUES (".$student_id.",".$class_id.",".$section_id.",".$attendance_status.",'".$attendance_date."','".$attendance_time."',".$score.")";
                mysqli_query($this->dbConnect, $insertQuery);
                if(mysqli_affected_rows($this->dbConnect) > 0) {
                    echo json_encode(["status" => "success", "message" => "Checked In successfully."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Check In failed or already exists." . mysqli_error($this->dbConnect)]);
                }
            } else {
                 echo json_encode(["status" => "error", "message" => "Already checked in or error checking existing record."]);
            }
        } elseif ($io == "2") { //Check Out
            $selectQuery = "SELECT * FROM ".$this->attendanceTable." WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date."' AND check_in IS NOT NULL AND check_out IS NULL";
            $result = mysqli_query($this->dbConnect, $selectQuery);
            if ($result && mysqli_num_rows($result) == 1) {
                $updateQuery = "UPDATE ".$this->attendanceTable." SET check_out='".$attendance_time."'
                WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date."'";
                mysqli_query($this->dbConnect, $updateQuery);
                 if(mysqli_affected_rows($this->dbConnect) > 0) {
                    echo json_encode(["status" => "success", "message" => "Checked Out successfully."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Check Out failed or not eligible." . mysqli_error($this->dbConnect)]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Not checked in, already checked out, or error finding record."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid operation."]);
        }
        exit; // Ensure no further output after JSON
    }
}
?>