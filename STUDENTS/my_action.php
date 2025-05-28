<?php
include('class/My_Student.php');
$kelas = new Kelas();
if(!empty($_POST['action']) && $_POST['action'] == 'listKelas') {
    $kelas->getKelasList();
}
if(!empty($_POST['action']) && $_POST['action'] == 'addKelas') {
    $kelas->addKelas($_POST['checkedInOut']);
}
?>