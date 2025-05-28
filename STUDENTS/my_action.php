<?php
// Ensure session is started as My_Student.php will use $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('class/My_Student.php'); // Path to your My_Student class file
$kelas = new Kelas();

if(!empty($_POST['action']) && $_POST['action'] == 'listKelas') {
	$kelas->getKelasList();
} elseif(!empty($_POST['action']) && $_POST['action'] == 'addKelas') {
    // checkedInOut is passed in $_POST from the hidden field
	$kelas->addKelas($_POST['checkedInOut']);
    // Optionally, return a JSON response for success/failure
    echo json_encode(['status' => 'success', 'message' => 'Attendance recorded.']); // Added response for addKelas
    exit;
} elseif(!empty($_POST['action']) && $_POST['action'] == 'updateUser') {
    // You'll need a method in User.php or another relevant class to handle this
    // For example:
    // include_once('class/User.php');
    // $userHandler = new User(); // Assuming User class has an updateUser method
    // $userHandler->updateUser(); // This method would use $_POST data
    echo json_encode(['status' => 'success', 'message' => 'User updated (stub).']); // Example response
    exit;
} else {
    // If no action is matched, send a clear error
    header("Content-Type: application/json");
    http_response_code(400); // Bad Request
    echo json_encode([
        "error" => "Invalid action specified.",
        "post_data" => $_POST // Show what was received, for debugging
    ]);
    exit;
}
?>