<?php
session_start();
require_once "config.php";

$sql = "DELETE FROM `projects` WHERE `id` = ? AND `owner` = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("is", $idParam, $ownerParam);

    $ownerParam = $_SESSION["username"];
    $idParam = $_GET["id"];

    if ($stmt->execute()) {
        $sql = "DELETE FROM `members` WHERE `projectId` = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $idParam);

            $idParam = $_GET["id"];

            if ($stmt->execute()) {
                echo "Success!";
            } else {
                echo("Statement failed: " . $stmt->error . "<br>");
                echo "Something went wrong. Please try again later.";
            }
        }
        $stmt->close();
    } else {
        echo("Statement failed: " . $stmt->error . "<br>");
        echo "Something went wrong. Please try again later.";
    }
}
$stmt->close();

$mysqli->close();

if (is_dir(__DIR__ . "/resources/" . $_GET["id"])) {
    rmdir(__DIR__ . "/resources/" . $_GET["id"]);
}
?>