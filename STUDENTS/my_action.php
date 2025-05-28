<?php
ini_set('display_errors', 1); // FOR DEBUGGING ONLY - REMOVE IN PRODUCTION
ini_set('display_startup_errors', 1); // FOR DEBUGGING ONLY
error_reporting(E_ALL); // FOR DEBUGGING ONLY

// Ensure the path to My_Student.php is correct relative to my_action.php
// If my_action.php is in the root, and My_Student.php is in a 'class' subdirectory:
include_once('class/My_Student.php'); // Use include_once or require_once

// Check if the class Kelas exists after include
if (!class_exists('Kelas')) {
    // Output a JSON error if the class isn't found
    header("Content-Type: application/json");
    echo json_encode([
        "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Server configuration error: Kelas class not found. Check include path in my_action.php."
    ]);
    exit;
}

$kelas = new Kelas();

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'listKelas':
            $kelas->getKelasList(); // This method should handle its own exit and JSON output
            break;
        case 'addKelas':
            if (isset($_POST['checkedInOut'])) {
                $kelas->addKelas($_POST['checkedInOut']); // This method should handle its own exit and JSON output
            } else {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error", "message" => "Missing 'checkedInOut' parameter for addKelas action."]);
                exit;
            }
            break;
        // Add other cases as needed
        // default:
        //     header("Content-Type: application/json");
        //     echo json_encode(["status" => "error", "message" => "Unknown action."]);
        //     exit;
    }
} else {
    // No action specified
    header("Content-Type: application/json");
    echo json_encode([
        "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "No action specified in the request."
    ]);
    exit;
}

// No further output should be here if methods in Kelas class call exit()
?>