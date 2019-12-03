<?php
session_start();
require_once "config.php";

if (empty(trim($_GET["id"])) || !is_numeric(htmlspecialchars($_GET["id"])) || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "Error";
} else {
    $sql = 'SELECT * FROM ( SELECT username, message, created FROM chats WHERE projectId="' . $_GET["id"] . '" ORDER BY created DESC LIMIT 100) sub ORDER BY created ASC';

    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['username'] == "SERVER") {
                echo "<b class='m-0'>" . $row['message'] . "</b>";
            } else {
                echo "<p class='m-0'>" . $row['username'] . ": " . $row['message'] . "</p>";
            }
        }
    }


    $mysqli->close();
}

