<?php
session_start(); // Ensure session is started
// require('include/config.php'); // Original include
// $docroot="C:\shared\SIS1.01";	//$path = $_SERVER['DOCUMENT_ROOT']; // Not needed if using __DIR__
// $path= "\config.php"; // Not needed if using __DIR__
require_once __DIR__ . '/../include/config.php'; // Corrected path if My_Student.php is in 'class' and config.php is in 'include'

class Kelas extends Dbconfig {
	// private $userTable = 'user'; // Should be defined in Dbconfig or here if not
	// private $attendanceTable = 'sms_attendance'; // Should be defined in Dbconfig or here
    // Define table name properties if not in Dbconfig
    private $userTable;
    private $designationTable;
    private $user_studentTable;
    private $studentTable;
    private $student_classTable;
    private $classesTable;
    private $class_sessionTable;
    private $sessionsTable;
    private $class_subjectTable;
    private $subjectsTable;
    private $sectionsTable;
    private $teacherTable;
    private $attendanceTable;
    // Removed programsTable as it wasn't used in final output columns

	private $dbConnect = false;

    public function __construct(){
        if(!$this->dbConnect){
			$database = new dbConfig();
            $this -> serverName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;

            // Initialize table names from Dbconfig or set defaults
            $this->userTable = $database->userTable ?? 'user';
            $this->designationTable = $database->designationTable ?? 'sms_designation';
            $this->user_studentTable = $database->user_studentTable ?? 'sms_user_student';
            $this->studentTable = $database->studentTable ?? 'sms_students';
            $this->student_classTable = $database->student_classTable ?? 'sms_student_class';
            $this->classesTable = $database->classesTable ?? 'sms_classes';
            $this->class_sessionTable = $database->class_sessionTable ?? 'sms_class_session';
            $this->sessionsTable = $database->sessionsTable ?? 'sms_sessions';
            $this->class_subjectTable = $database->class_subjectTable ?? 'sms_class_subject';
            $this->subjectsTable = $database->subjectsTable ?? 'sms_subjects';
            $this->sectionsTable = $database->sectionsTable ?? 'sms_sections';
            $this->teacherTable = $database->teacherTable ?? 'sis_teacher'; // or sms_teacher
            $this->attendanceTable = $database->attendanceTable ?? 'sms_attendance';


            $conn = new mysqli($this->serverName, $this->userName, $this->password, $this->dbName);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
    }

	public function getKelasList(){
        // Refactored SQL to focus on student_class entries and join for details
        // Also fetching check_in and check_out times for today
		$sqlQuery = "SELECT
            sc.class_id as cID, -- Use class_id from student_class as the main class identifier for this row
            cls.name as ClassName, -- Renamed to avoid conflict with Class keyword
            sess.session as SessionName, -- Renamed
            subj.subject as SubjectName, -- Renamed
            sect.section as SectionName,
            cls.section as section_id_val, -- This is the section_id (e.g., integer)
            tech.teacher as TeacherName, -- Renamed
            u.status as UserStatus,
            s.id as sID, -- student.id from studentTable
            sess.start as classStartTimeValue,
            att.check_in AS check_in_time,
            att.check_out AS check_out_time
            -- u.id as uID -- user.id, if needed by JS for other purposes
        FROM ".$this->userTable." AS u
        INNER JOIN ".$this->user_studentTable." AS us ON u.id = us.user_id
        INNER JOIN ".$this->studentTable." AS s ON us.student_id = s.id
        INNER JOIN ".$this->student_classTable." AS sc ON s.id = sc.student_id -- Iterate over classes student is enrolled in
        LEFT JOIN ".$this->classesTable." AS cls ON sc.class_id = cls.id
        LEFT JOIN ".$this->class_sessionTable." AS cs ON cls.id = cs.class_id
        LEFT JOIN ".$this->sessionsTable." AS sess ON cs.session_id = sess.session_id
        LEFT JOIN ".$this->class_subjectTable." AS csubj ON cls.id = csubj.class_id
        LEFT JOIN ".$this->subjectsTable." AS subj ON csubj.subject_id = subj.subject_id
        LEFT JOIN ".$this->sectionsTable." AS sect ON cls.section = sect.section_id -- cls.section is the section_id FK
        LEFT JOIN ".$this->teacherTable." AS tech ON cls.teacher_id = tech.teacher_id
        LEFT JOIN ".$this->attendanceTable." AS att
            ON att.student_id = s.id          -- student_id from studentTable (s.id)
            AND att.class_id = sc.class_id    -- class_id from student_classTable (sc.class_id)
            AND att.section_id = cls.section  -- section_id from classesTable (cls.section)
            AND att.attendance_date = CURDATE()
        WHERE u.id='".$_SESSION["userid"]."' ";

        if(!empty($_POST["search"]["value"])){
            $search = mysqli_real_escape_string($this->dbConnect, $_POST["search"]["value"]);
            $sqlQuery .= " AND (cls.id LIKE '%".$search."%' ";
            $sqlQuery .= " OR cls.name LIKE '%".$search."%' ";
            $sqlQuery .= " OR subj.subject LIKE '%".$search."%' ";
            $sqlQuery .= " OR tech.teacher LIKE '%".$search."%' ";
            $sqlQuery .= " OR sect.section LIKE '%".$search."%') ";
        }

		if(!empty($_POST["order"])){
            // Basic ordering, assumes column index maps well or specific column names are sent by DataTable
            $columnIndex = $_POST['order']['0']['column'];
            // Example mapping if needed: $columns = ['cls.id', 'cls.name', ...]; $orderBy = $columns[$columnIndex];
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' '; // Adjust if column indices don't match DB fields
		} else {
			$sqlQuery .= 'ORDER BY sc.class_id DESC ';
		}

		if(isset($_POST["length"]) && $_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
		}

		$result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            // Handle query error
            error_log("SQL Error in getKelasList: " . mysqli_error($this->dbConnect));
            $output = array(
                "draw"            => intval($_POST["draw"]),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => []
            );
            header("Content-Type: application/json");
            echo json_encode($output);
            exit;
        }

        // For total records count (simplified, ideally run count query without limit)
        $totalQuery = "SELECT COUNT(DISTINCT sc.class_id) as total_records
                       FROM ".$this->userTable." AS u
                       INNER JOIN ".$this->user_studentTable." AS us ON u.id = us.user_id
                       INNER JOIN ".$this->studentTable." AS s ON us.student_id = s.id
                       INNER JOIN ".$this->student_classTable." AS sc ON s.id = sc.student_id
                       LEFT JOIN ".$this->classesTable." AS cls ON sc.class_id = cls.id
                       LEFT JOIN ".$this->subjectsTable." AS subj ON cls.default_subject_id = subj.subject_id -- Assuming a default or primary subject for search
                       LEFT JOIN ".$this->teacherTable." AS tech ON cls.teacher_id = tech.teacher_id
                       LEFT JOIN ".$this->sectionsTable." AS sect ON cls.section = sect.section_id
                       WHERE u.id='".$_SESSION["userid"]."'";
        if(!empty($_POST["search"]["value"])){ // Apply search to count query as well
             $search = mysqli_real_escape_string($this->dbConnect, $_POST["search"]["value"]);
             $totalQuery .= " AND (cls.id LIKE '%".$search."%' OR cls.name LIKE '%".$search."%' OR subj.subject LIKE '%".$search."%' OR tech.teacher LIKE '%".$search."%' OR sect.section LIKE '%".$search."%') ";
        }
        $totalResult = mysqli_query($this->dbConnect, $totalQuery);
        $totalRow = mysqli_fetch_assoc($totalResult);
        $numRowsTotal = $totalRow['total_records'] ?? 0;


		$kelasData = array();
		$no = intval($_POST['start']) + 1;
		while( $kelas = mysqli_fetch_assoc($result) ) {
			$kelasRows = array();
			$kelasRows[] = $no++;
			$kelasRows[] = $kelas['cID']; // Class ID (sc.class_id)
			$kelasRows[] = $kelas['ClassName'] ?: 'N/A';
			$kelasRows[] = $kelas['SessionName'] ?: 'N/A';
			$kelasRows[] = $kelas['SubjectName'] ?: 'N/A';
			$kelasRows[] = $kelas['SectionName'] ?: 'N/A'; // Display name of section
			$kelasRows[] = $kelas['TeacherName'] ?: 'N/A';

			$status = '';
			if($kelas['UserStatus'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else if($kelas['UserStatus'] == 'pending') {
				$status = '<span class="label label-warning">Inactive</span>';
			} else {
				$status = '<span class="label label-danger">'.ucfirst(htmlspecialchars($kelas['UserStatus'])).'</span>';
			}
			$kelasRows[] = $status;

            $classStartTimeFormatted = $kelas['classStartTimeValue'] ? substr($kelas['classStartTimeValue'], 0, 5) : 'N/A';
			$kelasRows[] = $classStartTimeFormatted; // Class Start Time (HH:MM)

            // Display Check In and Check Out Times
            $checkInDisplay = $kelas['check_in_time'] ? date("H:i:s", strtotime($kelas['check_in_time'])) : 'N/A';
            $checkOutDisplay = $kelas['check_out_time'] ? date("H:i:s", strtotime($kelas['check_out_time'])) : 'N/A';
			$kelasRows[] = $checkInDisplay;
			$kelasRows[] = $checkOutDisplay;

            // Prepare data for buttons
            $btn_sID = $kelas['sID'];
            $btn_cID = $kelas['cID'];
            $btn_iID = $kelas['section_id_val']; // Actual section_id
            $btn_startTime = $classStartTimeFormatted;

            $checkInDisabled = '';
            $checkOutDisabled = 'disabled'; // Default: can check in, cannot check out

            if ($kelas['check_in_time']) {
                $checkInDisabled = 'disabled'; // Already checked in
                if ($kelas['check_out_time']) {
                    $checkOutDisabled = 'disabled'; // Also checked out
                } else {
                    $checkOutDisabled = ''; // Can check out
                }
            }

			$buttons = '<button type="button" name="checkIn" class="btn btn-success btn-xs checkInBtn"
                            data-sid="'.$btn_sID.'" data-cid="'.$btn_cID.'" data-iid="'.$btn_iID.'"
                            data-starttime="'.$btn_startTime.'" '.$checkInDisabled.'> In </button> ';
			$buttons .= '<button type="button" name="checkOut" class="btn btn-info btn-xs checkOutBtn"
                            data-sid="'.$btn_sID.'" data-cid="'.$btn_cID.'" data-iid="'.$btn_iID.'"
                            data-starttime="'.$btn_startTime.'" '.$checkOutDisabled.'>Out </button>';
			$kelasRows[] = $buttons;

			$kelasData[] = $kelasRows;
		}
		$output = array(
			"draw"            => intval($_POST["draw"]),
			"recordsTotal"    => $numRowsTotal,
			"recordsFiltered" => $numRowsTotal, // Assuming search is applied to total count
			"data"            => $kelasData
		);

		header("Content-Type: application/json");
		echo json_encode($output);
		exit;
	}

	public function addKelas($io) { // $io is '1' for check-in, '2' for check-out
        // Sanitize inputs
        $student_id = intval($_POST["studentid"]);
        $class_id = intval($_POST["classid"]);
        $section_id = intval($_POST["sectionid"]);
        $attendance_status = intval($_POST["attendancestatus"]);
        $attendance_date = mysqli_real_escape_string($this->dbConnect, $_POST["attendancedate"]); // YYYY/MM/DD
        $attendance_time = mysqli_real_escape_string($this->dbConnect, $_POST["attendancetime"]); // HH:MM:SS
        $score = floatval($_POST["score"]); // Assuming score is passed

        // Convert YYYY/MM/DD to YYYY-MM-DD for MySQL DATE type
        $attendance_date_mysql = str_replace('/', '-', $attendance_date);


        if ($io == "1") { // Check In
            $selectQuery = "SELECT * FROM ".$this->attendanceTable." WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date_mysql."'";
            $result = mysqli_query($this->dbConnect, $selectQuery);
            if ($result && mysqli_num_rows($result) == 0) {
                $insertQuery = "INSERT INTO ".$this->attendanceTable." (student_id, class_id, section_id, attendance_status, attendance_date, check_in, score)
                VALUES (".$student_id.",".$class_id.",".$section_id.",".$attendance_status.",'".$attendance_date_mysql."','".$attendance_time."',".$score.")";
                mysqli_query($this->dbConnect, $insertQuery);
            }
        } elseif ($io == "2") { // Check Out
            $selectQuery = "SELECT * FROM ".$this->attendanceTable." WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date_mysql."' AND check_in IS NOT NULL AND check_out IS NULL";
            $result = mysqli_query($this->dbConnect, $selectQuery);
            if ($result && mysqli_num_rows($result) == 1) {
                $updateQuery = "UPDATE ".$this->attendanceTable." SET check_out='".$attendance_time."'
                WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date_mysql."'";
                mysqli_query($this->dbConnect, $updateQuery);
            }
        }
	}
}
?>