<?php
session_start();
//require('include/config.php');
	//echo $_SERVER['DOCUMENT_ROOT'];
	$docroot="C:\shared\SIS1.01";	//$path = $_SERVER['DOCUMENT_ROOT'];
	$path= "\config.php";
		require_once __DIR__ . '/../include/config.php';
	
class Kelas extends Dbconfig {	
	protected $hostName;	//hostName is local name, from config.php is serverName
    protected $userName;
    protected $password;
	protected $dbName;
	private $userTable = 'user';
	private $attendanceTable = 'sms_attendance';
	private $classesTable = 'sms_classes';
	private $sessionsTable = 'sms_sessions';
	private $subjectsTable = 'sms_subjects';
	private $sectionsTable = 'sms_section';
	private $teacherTable = 'sis_teacher';
	private $designationTable = 'sis_designation';
	private $user_studentTable = 'user_student';
	private $studentTable = 'sms_students';
	private $student_classTable = 'student_class';
	private $programsTable = 'sis_programs';
	private $class_subjectTable = 'class_subject';
	private $class_sessionTable = 'class_session';

	private $no;
	private $dbConnect = false;
    
    public function __construct(){
        if(!$this->dbConnect){ 				
			$database = new dbConfig();            
            $this -> serverName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;	
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
            sc.student_id as ID,
            (SELECT name FROM ".$this->classesTable." WHERE id=sc.class_id) as Class,
            (SELECT session FROM ".$this->sessionsTable." WHERE session_id=(SELECT session_id FROM ".$this->class_sessionTable." WHERE class_id=sc.class_id)) as Session,
            (SELECT subject FROM ".$this->subjectsTable." WHERE subject_id=(SELECT subject_id FROM ".$this->class_subjectTable." WHERE class_id=sc.class_id)) as Subject,
            (SELECT section FROM ".$this->sectionsTable." WHERE section_id=(SELECT section FROM ".$this->classesTable." WHERE id=sc.class_id)) as Section,
            (SELECT teacher FROM ".$this->teacherTable." WHERE teacher_id=(SELECT teacher_id FROM ".$this->classesTable." WHERE id=sc.class_id)) as Teacher,
            u.status as Status,
            u.id as uID,
            s.id as sID,
            c.id as cID,
            m.subject_id as mID,
            i.section_id as iID,
            t.teacher_id as tID,
            ds.designation_name as Designation,
            u.type as Type,
            (SELECT start FROM ".$this->sessionsTable." WHERE session_id=(SELECT session_id FROM ".$this->class_sessionTable." WHERE class_id=sc.class_id)) as sTime,
            a.check_in as CheckIn,
            a.check_out as CheckOut

        FROM ".$this->userTable." as u 
        INNER JOIN ".$this->designationTable." as ds ON ds.designation_id=u.designation   
        INNER JOIN ".$this->user_studentTable." as us ON us.user_id=u.id
        INNER JOIN ".$this->studentTable." as s ON us.student_id=s.id
        LEFT JOIN ".$this->classesTable." as c ON s.class=c.id
        LEFT JOIN ".$this->student_classTable." as sc ON s.id=sc.student_id
        LEFT JOIN ".$this->teacherTable." as t ON c.teacher_id=t.teacher_id
        LEFT JOIN ".$this->programsTable." as p ON t.program_id=p.program_id
        LEFT JOIN ".$this->class_subjectTable." as cm ON c.id=cm.class_id
        LEFT JOIN ".$this->subjectsTable." as m ON cm.subject_id=m.subject_id
        LEFT JOIN ".$this->sectionsTable." as i ON c.section=i.section_id
        LEFT JOIN ".$this->class_sessionTable." as cs ON c.id=cs.class_id
        LEFT JOIN ".$this->sessionsTable." as ss ON ss.session_id=cs.session_id
        LEFT JOIN ".$this->attendanceTable." as a 
            ON a.student_id = sc.student_id 
            AND a.class_id = sc.class_id 
            AND a.section_id = sc.section_id 
            AND a.attendance_date = CURDATE()
        WHERE u.id='".$_SESSION["userid"]."' ";

		if(!empty($_POST["search"]["value"])){
			$search = $_POST["search"]["value"];
			$sqlQuery .= " AND (id LIKE '%$search%' ";
			$sqlQuery .= " OR first_name LIKE '%$search%' ";
			$sqlQuery .= " OR last_name LIKE '%$search%' ";
			$sqlQuery .= " OR designation LIKE '%$search%' ";
			$sqlQuery .= " OR status LIKE '%$search%' ";
			$sqlQuery .= " OR mobile LIKE '%$search%') ";
		}

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}

		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$kelasData = array();	
		$no=1;		
		while( $kelas = mysqli_fetch_assoc($result) ) {		
			$kelasRows = array();
			$kelasRows[] = $no; $no++;			
			$kelasRows[] = $kelas['cID'];
			$kelasRows[] = $kelas['Class'];
			$kelasRows[] = $kelas['Session'];
			$kelasRows[] = $kelas['Subject'];	
			$kelasRows[] = $kelas['Section'];			
			$kelasRows[] = $kelas['Teacher'];	
			
			$status = '';
			if($kelas['Status'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else if($kelas['Status'] == 'pending') {
				$status = '<span class="label label-warning">Inactive</span>';
			} else if($kelas['Status'] == 'deleted') {
				$status = '<span class="label label-danger">Deleted</span>';
			}
			$kelasRows[] = $status;		
			
			$spanIDs = '';
			$spanIDs .='<span style="display:none" id="uID">'.$kelas['uID'].'</span>';
			$spanIDs .='<span style="display:none" id="sID">'.$kelas['sID'].'</span>';				
			$spanIDs .='<span style="display:none" id="cID">'.$kelas['cID'].'</span>';
			$spanIDs .='<span style="display:none" id="mID">'.$kelas['mID'].'</span>';
			$spanIDs .='<span style="display:none" id="iID">'.$kelas['iID'].'</span>';				
			$spanIDs .='<span style="display:none" id="tID">'.$kelas['tID'].'</span>';
			$spanIDs .='<span style="display:none" id="Designation">'.$kelas['Designation'].'</span>';
			$spanIDs .='<span style="display:none" id="Type">'.$kelas['Type'].'</span>';
			$spanIDs .='<span style="display:block" id="startTime">'.substr($kelas['sTime'],0,5).'</span>';	
			$kelasRows[] = $spanIDs;
			
			// Add check-in/out timestamps
			$checkInTime = $kelas['CheckIn'] ? date('H:i', strtotime($kelas['CheckIn'])) : '-';
			$checkOutTime = $kelas['CheckOut'] ? date('H:i', strtotime($kelas['CheckOut'])) : '-';
			$kelasRows[] = $checkInTime;
			$kelasRows[] = $checkOutTime;

			// Update button states
			$checkInDisabled = $kelas['CheckIn'] ? 'disabled' : '';
			$checkOutDisabled = ($kelas['CheckOut'] || !$kelas['CheckIn']) ? 'disabled' : '';
			
			$twoButtons = '<button type="button" name="checkIn" id="checkIn" class="btn btn-success btn-xs update" '.$checkInDisabled.'>&nbsp;In&nbsp;</button>&nbsp;';
			$twoButtons .= '<button type="button" name="checkOut" id="checkOut" class="btn btn-info btn-xs delete" '.$checkOutDisabled.'>Out&nbsp;</button>';
			$kelasRows[] = $twoButtons;

			$kelasData[] = $kelasRows;
		}
		$output = array(
			"draw"            => intval($_POST["draw"]),
			"recordsTotal"    => $numRows,
			"recordsFiltered" => $numRows,
			"data"            => $kelasData
		);
		
		header("Content-Type: application/json");
		echo json_encode($output);
		exit;
	}
	
	public function addKelas($io) {
		if ($io=="1") {
			$selectQuery = "SELECT * FROM ".$this->attendanceTable." 
				WHERE student_id=".$_POST["studentid"]." 
				AND class_id=".$_POST["classid"]." 
				AND section_id=".$_POST["sectionid"]." 
				AND attendance_date='".$_POST["attendancedate"]."'";
			$result = mysqli_query($this->dbConnect, $selectQuery);
			$numRows = mysqli_num_rows($result);
			if ($numRows == 0) {
				$insertQuery = "INSERT INTO ".$this->attendanceTable." (student_id, class_id, section_id, attendance_status, attendance_date, check_in, score) 
				VALUES (".$_POST["studentid"].",".$_POST["classid"].",".$_POST["sectionid"].",".$_POST["attendancestatus"].",'".$_POST["attendancedate"]."','".$_POST["attendancetime"]."','".$_POST["score"]."')";
				mysqli_query($this->dbConnect, $insertQuery);						
			}
		}
		if ($io=="2") {
			$selectQuery = "SELECT * FROM ".$this->attendanceTable." 
				WHERE student_id=".$_POST["studentid"]." 
				AND class_id=".$_POST["classid"]." 
				AND section_id=".$_POST["sectionid"]." 
				AND attendance_date='".$_POST["attendancedate"]."' 
				AND (check_in IS NOT NULL)";
			$result = mysqli_query($this->dbConnect, $selectQuery);
			$numRows = mysqli_num_rows($result);
			if ($numRows == 1) {
				$insertQuery = "UPDATE ".$this->attendanceTable." SET check_out='".$_POST["attendancetime"]."' 
				WHERE student_id=".$_POST["studentid"]." 
				AND class_id=".$_POST["classid"]." 
				AND section_id=".$_POST["sectionid"]." 
				AND attendance_date='".$_POST["attendancedate"]."'";
				mysqli_query($this->dbConnect, $insertQuery);				
			}
		}
	}
}
?>