<?php
// session_start(); // Session should be started by the calling script like my_action.php

// require_once __DIR__ . '/../include/config.php'; // This path assumes My_Student.php is in 'class' and config.php is in 'include'

class Kelas extends Dbconfig {
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
			$database = new dbConfig(); // This assumes dbConfig is in the global namespace or autoloaded
            // Ensure config.php is included if dbConfig is defined there and not autoloaded
            if (!class_exists('dbConfig')) {
                // Adjust path as necessary if My_Student.php is not in 'class' or config.php is elsewhere
                $configFile = __DIR__ . '/../include/config.php';
                if (file_exists($configFile)) {
                    require_once $configFile;
                    $database = new dbConfig();
                } else {
                    // Fallback or error if config can't be loaded
                    // This will cause issues downstream if not handled
                    error_log("My_Student.php: dbConfig class not found and config.php could not be loaded from: " . $configFile);
                    // For DataTables, we need to output valid JSON even for errors
                    // This constructor error is tricky to report back via JSON directly to DataTables from here
                    // The calling script (my_action.php) would typically handle this kind of fatal error
                    $this->sendJsonError("Database configuration class 'dbConfig' not found.", 500); // Exit
                }
            }


            $this -> serverName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;

            // Initialize table names from Dbconfig or set defaults
            // It's crucial these properties exist in your Dbconfig class or the defaults are correct
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
                // For DataTables, try to send a JSON error back
                $this->sendJsonError("MySQL Connection Error: " . $conn->connect_error, 500); // Exit
            } else{
                $this->dbConnect = $conn;
                $this->dbConnect->set_charset("utf8mb4"); // Good practice
            }
        }
    }

    private function sendJsonError($message, $httpStatusCode = 400, $sql = null) {
        if (!headers_sent()) {
            header("Content-Type: application/json");
            http_response_code($httpStatusCode);
        }
        $errorData = [
            "error" => $message,
            "data" => [] // DataTables expects a 'data' array even in errors
        ];
        if ($sql) {
            $errorData["sql_debug"] = $sql;
        }
        echo json_encode($errorData);
        exit;
    }

	public function getKelasList(){
        if (!isset($_SESSION["userid"])) {
            $this->sendJsonError("User session not found. Please login again.");
            return;
        }
        $userId = mysqli_real_escape_string($this->dbConnect, $_SESSION["userid"]);

		$sqlQuery = "SELECT
            sc.class_id as cID,
            cls.name as ClassName,
            sess.session as SessionName,
            subj.subject as SubjectName,
            sect.section as SectionName,
            cls.section as section_id_val,
            tech.teacher as TeacherName,
            u.status as UserStatus,
            s.id as sID,
            sess.start as classStartTimeValue,
            att.check_in AS check_in_time,
            att.check_out AS check_out_time
        FROM ".$this->userTable." AS u
        INNER JOIN ".$this->user_studentTable." AS us ON u.id = us.user_id
        INNER JOIN ".$this->studentTable." AS s ON us.student_id = s.id
        INNER JOIN ".$this->student_classTable." AS sc ON s.id = sc.student_id
        LEFT JOIN ".$this->classesTable." AS cls ON sc.class_id = cls.id
        LEFT JOIN ".$this->class_sessionTable." AS cs ON cls.id = cs.class_id
        LEFT JOIN ".$this->sessionsTable." AS sess ON cs.session_id = sess.session_id
        LEFT JOIN ".$this->class_subjectTable." AS csubj ON cls.id = csubj.class_id
        LEFT JOIN ".$this->subjectsTable." AS subj ON csubj.subject_id = subj.subject_id
        LEFT JOIN ".$this->sectionsTable." AS sect ON cls.section = sect.section_id
        LEFT JOIN ".$this->teacherTable." AS tech ON cls.teacher_id = tech.teacher_id
        LEFT JOIN ".$this->attendanceTable." AS att
            ON att.student_id = s.id
            AND att.class_id = sc.class_id
            AND att.section_id = cls.section
            AND att.attendance_date = CURDATE()
        WHERE u.id='".$userId."' ";

        $searchableColumns = ['cls.id', 'cls.name', 'subj.subject', 'tech.teacher', 'sect.section']; // Define searchable DB columns

        if(!empty($_POST["search"]["value"])){
            $searchValue = mysqli_real_escape_string($this->dbConnect, $_POST["search"]["value"]);
            $searchSql = [];
            foreach ($searchableColumns as $col) {
                $searchSql[] = "$col LIKE '%".$searchValue."%'";
            }
            if (!empty($searchSql)) {
                 $sqlQuery .= " AND (" . implode(" OR ", $searchSql) . ") ";
            }
        }

        // Base query for counting total records (before pagination, but after filtering)
        $countQueryBase = "SELECT COUNT(DISTINCT sc.class_id) as total_records
                           FROM ".$this->userTable." AS u
                           INNER JOIN ".$this->user_studentTable." AS us ON u.id = us.user_id
                           INNER JOIN ".$this->studentTable." AS s ON us.student_id = s.id
                           INNER JOIN ".$this->student_classTable." AS sc ON s.id = sc.student_id
                           LEFT JOIN ".$this->classesTable." AS cls ON sc.class_id = cls.id
                           LEFT JOIN ".$this->class_subjectTable." AS csubj_count ON cls.id = csubj_count.class_id
                           LEFT JOIN ".$this->subjectsTable." AS subj_count ON csubj_count.subject_id = subj_count.subject_id
                           LEFT JOIN ".$this->teacherTable." AS tech_count ON cls.teacher_id = tech_count.teacher_id
                           LEFT JOIN ".$this->sectionsTable." AS sect_count ON cls.section = sect_count.section_id
                           WHERE u.id='".$userId."'";

        $totalQuery = $countQueryBase;
        if(!empty($_POST["search"]["value"])){
            $searchValue = mysqli_real_escape_string($this->dbConnect, $_POST["search"]["value"]);
            $searchSqlCount = [];
            // Adjust column names for count query aliases if different
            $searchableCountColumns = ['cls.id', 'cls.name', 'subj_count.subject', 'tech_count.teacher', 'sect_count.section'];
            foreach ($searchableCountColumns as $col) {
                $searchSqlCount[] = "$col LIKE '%".$searchValue."%'";
            }
            if (!empty($searchSqlCount)) {
                 $totalQuery .= " AND (" . implode(" OR ", $searchSqlCount) . ") ";
            }
        }

        $totalResult = mysqli_query($this->dbConnect, $totalQuery);
        if (!$totalResult) {
            $this->sendJsonError("SQL Error (Count Query): " . mysqli_error($this->dbConnect), 500, $totalQuery);
            return;
        }
        $totalRow = mysqli_fetch_assoc($totalResult);
        $numRowsTotal = $totalRow['total_records'] ?? 0;


		if(!empty($_POST["order"])){
            $columnIndex = intval($_POST['order']['0']['column']);
            // IMPORTANT: Map DataTables column index to actual database column name/alias for security and correctness
            // This mapping depends on the order of columns in your My_Attendance.js DataTable definition
            $columnOrderMap = [
                // 0 => 'sc.class_id', // Assuming 'No.' is not sortable from DB
                // 1 => 'sc.class_id', // Assuming 'ID' (cID) is sortable
                2 => 'ClassName',      // Class
                3 => 'SessionName',    // Session
                4 => 'SubjectName',    // Subject
                5 => 'SectionName',    // Section
                6 => 'TeacherName',    // Teacher
                // 7 => 'UserStatus',  // Status (might not be suitable for direct DB sort)
                // 8 => 'classStartTimeValue' // Start Time
                // 9, 10, 11 are CheckIn, CheckOut, Actions - likely not sortable from DB
            ];
            if (isset($columnOrderMap[$columnIndex])) {
                $orderByColumn = $columnOrderMap[$columnIndex];
                $orderDir = mysqli_real_escape_string($this->dbConnect, $_POST['order']['0']['dir']);
                if (in_array(strtoupper($orderDir), ['ASC', 'DESC'])) {
                    $sqlQuery .= ' ORDER BY '.$orderByColumn.' '.$orderDir.' ';
                } else {
                    $sqlQuery .= ' ORDER BY sc.class_id DESC '; // Default if direction is invalid
                }
            } else {
			    $sqlQuery .= ' ORDER BY sc.class_id DESC '; // Default order
            }
		} else {
			$sqlQuery .= ' ORDER BY sc.class_id DESC ';
		}

		if(isset($_POST["length"]) && $_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
		}

		$result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            $this->sendJsonError("SQL Error (Data Query): " . mysqli_error($this->dbConnect), 500, $sqlQuery);
            return;
        }

		$kelasData = array();
		$no = intval($_POST['start'] ?? 0) + 1;
		while( $kelas = mysqli_fetch_assoc($result) ) {
			$kelasRows = array();
			$kelasRows[] = $no++;
			$kelasRows[] = $kelas['cID'];
			$kelasRows[] = htmlspecialchars($kelas['ClassName'] ?: 'N/A');
			$kelasRows[] = htmlspecialchars($kelas['SessionName'] ?: 'N/A');
			$kelasRows[] = htmlspecialchars($kelas['SubjectName'] ?: 'N/A');
			$kelasRows[] = htmlspecialchars($kelas['SectionName'] ?: 'N/A');
			$kelasRows[] = htmlspecialchars($kelas['TeacherName'] ?: 'N/A');

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
			$kelasRows[] = $classStartTimeFormatted;

            $checkInDisplay = $kelas['check_in_time'] ? date("H:i:s", strtotime($kelas['check_in_time'])) : 'N/A';
            $checkOutDisplay = $kelas['check_out_time'] ? date("H:i:s", strtotime($kelas['check_out_time'])) : 'N/A';
			$kelasRows[] = $checkInDisplay;
			$kelasRows[] = $checkOutDisplay;

            $btn_sID = $kelas['sID'];
            $btn_cID = $kelas['cID'];
            $btn_iID = $kelas['section_id_val'];
            $btn_startTime = $classStartTimeFormatted;

            $checkInDisabled = '';
            $checkOutDisabled = 'disabled';

            if ($kelas['check_in_time']) {
                $checkInDisabled = 'disabled';
                if ($kelas['check_out_time']) {
                    $checkOutDisabled = 'disabled';
                } else {
                    $checkOutDisabled = '';
                }
            }

			$buttons = '<button type="button" name="checkIn" class="btn btn-success btn-xs checkInBtn"
                            data-sid="'.htmlspecialchars($btn_sID).'" data-cid="'.htmlspecialchars($btn_cID).'" data-iid="'.htmlspecialchars($btn_iID).'"
                            data-starttime="'.htmlspecialchars($btn_startTime).'" '.$checkInDisabled.'> In </button> ';
			$buttons .= '<button type="button" name="checkOut" class="btn btn-info btn-xs checkOutBtn"
                            data-sid="'.htmlspecialchars($btn_sID).'" data-cid="'.htmlspecialchars($btn_cID).'" data-iid="'.htmlspecialchars($btn_iID).'"
                            data-starttime="'.htmlspecialchars($btn_startTime).'" '.$checkOutDisabled.'>Out </button>';
			$kelasRows[] = $buttons;

			$kelasData[] = $kelasRows;
		}
		$output = array(
			"draw"            => intval($_POST["draw"] ?? 0),
			"recordsTotal"    => $numRowsTotal, // This should be total records *without* filtering
			"recordsFiltered" => $numRowsTotal, // This should be total records *with* filtering applied by search
			"data"            => $kelasData
		);
        // For recordsTotal, you ideally need another query that counts *all* records for this user without the search filter.
        // The current $numRowsTotal is total *filtered* records.
        // If you need accurate "Showing X to Y of Z entries" where Z is total before search, add another count query.
        // For simplicity, I'm using $numRowsTotal for both, which is common if total unfiltered count is not critical.


		if (!headers_sent()) {
            header("Content-Type: application/json");
        }
		echo json_encode($output);
		exit;
	}

	public function addKelas($io) {
        if (!isset($_POST["studentid"], $_POST["classid"], $_POST["sectionid"], $_POST["attendancedate"], $_POST["attendancetime"], $_POST["attendancestatus"], $_POST["score"])) {
            // Not sending JSON here because my_action.php expects to do that for addKelas
            error_log("addKelas: Missing required POST parameters.");
            // The calling function in my_action.php should handle the JSON response for this error
            return false; // Indicate failure
        }

        $student_id = intval($_POST["studentid"]);
        $class_id = intval($_POST["classid"]);
        $section_id = intval($_POST["sectionid"]);
        $attendance_status = intval($_POST["attendancestatus"]);
        $attendance_date = $this->dbConnect->real_escape_string($_POST["attendancedate"]);
        $attendance_time = $this->dbConnect->real_escape_string($_POST["attendancetime"]);
        $score = floatval($_POST["score"]);

        $attendance_date_mysql = str_replace('/', '-', $attendance_date);

        $success = false;
        if ($io == "1") {
            $selectQuery = "SELECT * FROM ".$this->attendanceTable." WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date_mysql."'";
            $result = mysqli_query($this->dbConnect, $selectQuery);
            if ($result && mysqli_num_rows($result) == 0) {
                $insertQuery = "INSERT INTO ".$this->attendanceTable." (student_id, class_id, section_id, attendance_status, attendance_date, check_in, score)
                VALUES (".$student_id.",".$class_id.",".$section_id.",".$attendance_status.",'".$attendance_date_mysql."','".$attendance_time."',".$score.")";
                if(mysqli_query($this->dbConnect, $insertQuery)) {
                    $success = true;
                } else {
                     error_log("addKelas Check-In SQL Error: " . mysqli_error($this->dbConnect) . " Query: " . $insertQuery);
                }
            } elseif (!$result) {
                error_log("addKelas Check-In Select SQL Error: " . mysqli_error($this->dbConnect) . " Query: " . $selectQuery);
            }
        } elseif ($io == "2") {
            $selectQuery = "SELECT * FROM ".$this->attendanceTable." WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date_mysql."' AND check_in IS NOT NULL AND check_out IS NULL";
            $result = mysqli_query($this->dbConnect, $selectQuery);
            if ($result && mysqli_num_rows($result) == 1) {
                $updateQuery = "UPDATE ".$this->attendanceTable." SET check_out='".$attendance_time."'
                WHERE student_id=".$student_id." AND class_id=".$class_id." AND section_id=".$section_id." AND attendance_date='".$attendance_date_mysql."'";
                if(mysqli_query($this->dbConnect, $updateQuery)) {
                    $success = true;
                } else {
                    error_log("addKelas Check-Out SQL Error: " . mysqli_error($this->dbConnect) . " Query: " . $updateQuery);
                }
            } elseif (!$result) {
                 error_log("addKelas Check-Out Select SQL Error: " . mysqli_error($this->dbConnect) . " Query: " . $selectQuery);
            }
        }
        return $success; // Return true on success, false on failure
	}
}
?>