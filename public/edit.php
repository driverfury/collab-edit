<?php

session_start();
$session_id = session_id();

require_once '../vendor/autoload.php';

function generateRandomId() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 16; $i++) {
        $randstring = $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

$message_success = '';
if (isset($_GET['message_success'])) {
    $message_success = $_GET['message_success'];
}

$id = '';
$pathname = $_GET['file'];

$mysqli = mysqli_connect("localhost", "root", "", "etherpad");
$res = mysqli_query($mysqli, 'SELECT * FROM files WHERE pathname = \'' . $pathname . '\'');
$row = mysqli_fetch_assoc($res);
if ($row == NULL) {
    $id = generateRandomId();
    mysqli_query($mysqli, 'INSERT INTO files (id, pathname) VALUES (\'' . $id . '\', \'' . $pathname . '\')');
    $res = mysqli_query($mysqli, 'SELECT * FROM files WHERE pathname = \'' . $pathname . '\'');
    $row = mysqli_fetch_assoc($res);
}

$id = $row['id'];
$locked = $row['locked'];

if (!$row['locked']) {
    $file_content = file_get_contents('../' . $pathname);

    $instance = new EtherpadLite\Client('c3a475bea7cbbd527ba8092cd08c8b32434a0abd919b9efcc7a4bb065a06a621', 'http://localhost:9001/api');

    $already_exists = false;
    $pads = $instance->listAllPads();
    foreach ($pads->padIDs as $pad_id) {
        if ($pad_id == $id) {
            $already_exists = true;
            break;
        }
    }

    if (!$already_exists) {
        $instance->createPad($id, $file_content);
    }

    $usersCount = ($instance->padUsersCount($id))->padUsersCount;
    if ($usersCount == 0) {
        $instance->setText($id, $file_content);
    }
} else {
    echo 'File is currently locked. Try again later.';
}
?>

<span style="color: green;"><?php echo ($message_success != '') ? $message_success : ''; ?></span><br>
You are editing: <?php echo $pathname; ?>&nbsp;
<a href="save.php?file=<?php echo $pathname;?>"><button>Save</button></a>&nbsp;
<a href="lock.php?file=<?php echo $pathname;?>"><button><?php echo ($locked) ? 'Unlock' : 'Lock'; ?></button></a>
<iframe
    name="embed_readwrite"
    src="http://localhost:9001/p/<?php echo $id; ?>?showControls=false&showChat=false&showLineNumbers=true&useMonospaceFont=true"
    width="100%" height="600" frameborder="0">
</iframe>
