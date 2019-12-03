<?php
session_start();
require_once "config.php";

if (empty(trim($_GET["id"])) || !is_numeric(htmlspecialchars($_GET["id"])) || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "Error";
} else {
    $dup = false;
    $sql = "SELECT * FROM members WHERE username = ? AND projectId = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("si", $param_username, $param_id);

        $param_username = $_SESSION["username"];
        $param_id = htmlspecialchars(trim($_GET["id"]));

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $dup = true;
            } else {
                $username = trim($_POST["username"]);
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }

    if (!$dup) {
        $sql = "INSERT INTO members (projectId, username) VALUES (?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("is", $param_id, $param_username);

            $param_username = $_SESSION["username"];
            $param_id = htmlspecialchars(trim($_GET["id"]));

            if ($stmt->execute()) {
            } else {
                echo("Statement failed: " . $stmt->error . "<br>");
                echo "Something went wrong. Please try again later.";
            }
        }
        $stmt->close();
    }
    $mysqli->close();
}