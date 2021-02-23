<?php

$dir = '../files';
$files = array_diff(scandir($dir), array('.', '..')); 

echo '<ul>';

foreach ($files as $file) {
    echo '<li><a href="edit.php?file=files/' . $file . '">files/' . $file . '</a></li>';
}

echo '</ul>';
