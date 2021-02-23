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

if (!$row['locked']) {
    $instance = new EtherpadLite\Client('c3a475bea7cbbd527ba8092cd08c8b32434a0abd919b9efcc7a4bb065a06a621', 'http://localhost:9001/api');
    $file_content = ($instance->getText($id))->text;

    file_put_contents('../' . $pathname, $file_content);
    header('Location: edit.php?message_success=File saved successfully!&file=' . $pathname);
}
?>
