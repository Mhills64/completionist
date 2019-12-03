<?php
session_start();

if (empty(trim($_GET["id"])) || !is_numeric(htmlspecialchars($_GET["id"])) || empty(trim($_GET["description"])) || empty(trim($_GET["percent"])) || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "No parameters provided or not logged in!";
} else {
    require_once "config.php";

    $sql = "UPDATE projects SET description = ?,  percent = ? WHERE id=? AND owner=?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("siis", $descParam, $percentParam, $idParam, $ownerParam);

        $descParam = htmlspecialchars(trim($_GET["description"]));
        $percentParam = htmlspecialchars(trim($_GET["percent"]));

        $idParam = htmlspecialchars(trim($_GET["id"]));
        $ownerParam = $_SESSION["username"];

        if ($stmt->execute()) {
        } else {
            echo("Statement failed: " . $stmt->error . "<br>");
            echo "Something went wrong. Please try again later.";
        }
        $stmt->close();
    }

    $mysqli->close();
}
