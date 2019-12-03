<?php
session_start();
require_once "config.php";

if (empty(trim($_GET["id"])) || !is_numeric(htmlspecialchars($_GET["id"])) || empty(trim($_GET["message"])) || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "Error";
} else {
    $sql = "INSERT INTO chats (projectId, username, message) VALUES (?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("iss", $param_id, $param_username, $param_message);

        $param_username = $_SESSION["username"];
        $param_id = htmlspecialchars(trim($_GET["id"]));
        $param_message = htmlspecialchars(trim($_GET["message"]));

        if ($stmt->execute()) {
        } else {
            echo("Statement failed: " . $stmt->error . "<br>");
            echo "Something went wrong. Please try again later.";
        }
    }
    $stmt->close();
    $mysqli->close();
}