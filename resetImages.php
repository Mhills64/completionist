<?php
session_start();
require_once "config.php";

$sql = "SELECT * FROM `projects` WHERE `id` = ? AND `owner` = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("is", $idParam, $ownerParam);

    $ownerParam = $_SESSION["username"];
    $idParam = $_GET["id"];

    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            delete_directory(__DIR__ . "/resources/" . $_GET["id"]);
        }
        mkdir(__DIR__ . "/resources/" . $_GET["id"]);
    } else {
        echo("Statement failed: " . $stmt->error . "<br>");
        echo "Something went wrong. Please try again later.";
    }
}
$stmt->close();

$mysqli->close();

function delete_directory($dirname)
{
    if (is_dir($dirname))
        $dir_handle = opendir($dirname);
    if (!$dir_handle)
        return false;
    while ($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname . "/" . $file))
                unlink($dirname . "/" . $file);
            else
                delete_directory($dirname . '/' . $file);
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}