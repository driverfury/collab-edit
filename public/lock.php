<?php

session_start();
$session_id = session_id();

require_once '../vendor/autoload.php';

$id = '';
$pathname = $_GET['file'];

$mysqli = mysqli_connect("localhost", "root", "", "etherpad");
$res = mysqli_query($mysqli, 'SELECT * FROM files WHERE pathname = \'' . $pathname . '\'');
$row = mysqli_fetch_assoc($res);
if ($row == NULL) {
    exit();
}

$id = $row['id'];
$locked = $row['locked'];

$instance = new EtherpadLite\Client('c3a475bea7cbbd527ba8092cd08c8b32434a0abd919b9efcc7a4bb065a06a621', 'http://localhost:9001/api');

if ($locked) {
    $locked = 0;

    $file_content = file_get_contents('../' . $pathname);
    $instance->setText($id, $file_content);
} else {
    $locked = 1;
}


$res = mysqli_query($mysqli, 'UPDATE files SET locked = ' . $locked . ' WHERE pathname = \'' . $pathname . '\'');

header('Location: edit.php?message_success=File ' . ((!$locked) ? 'un' : '') . 'locked successfully!&file=' . $pathname);

?>
