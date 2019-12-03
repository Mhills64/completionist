<?php
session_start();

if (empty(trim($_GET["name"])) || empty(trim($_GET["description"])) || empty(trim($_GET["lat"])) || empty(trim($_GET["long"])) || empty(trim($_GET["percent"])) || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "No parameters provided or not logged in!";
    echo "Name: " . trim($_GET["name"]);
    echo "Description: " . trim($_GET["description"]);
    echo "Lat: " . trim($_GET["lat"]);
    echo "Lng:" . trim($_GET["long"]);
    echo "Percent: " . trim($_GET["percent"]);
} else {
    require_once "config.php";

    $sql = "INSERT INTO projects (name, description, percent, coord_lat, coord_long, owner) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssidds", $nameParam, $descParam, $percentParam, $latParam, $longParam, $ownParam);

        $nameParam = htmlspecialchars(trim($_GET["name"]));
        $descParam = htmlspecialchars(trim($_GET["description"]));
        $percentParam = htmlspecialchars(trim($_GET["percent"]));
        $latParam = htmlspecialchars(trim($_GET["lat"])); //43.0846
        $longParam = htmlspecialchars(trim($_GET["long"])); //-77.6743
        $ownParam = $_SESSION["username"];


        if ($stmt->execute()) {
        } else {
            echo("Statement failed: " . $stmt->error . "<br>");
            echo "Something went wrong. Please try again later.";
        }
    }
    $stmt->close();

    $sql = "SELECT * FROM projects WHERE `name`= '" . trim($_GET["name"]) . "'";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Lets create a place for images to go
            if (!is_dir(__DIR__ . "/resources/" . $row["id"])) {
                mkdir(__DIR__ . "/resources/" . $row["id"]);
            }

            $sql = "INSERT INTO members (projectId, username) VALUES (?, ?)";

            if ($stmt2 = $mysqli->prepare($sql)) {
                $stmt2->bind_param("is", $param_id, $param_username);

                $param_username = $_SESSION["username"];
                $param_id = $row["id"];

                if ($stmt2->execute()) {
                } else {
                    echo("Statement failed: " . $stmt2->error . "<br>");
                    echo "Something went wrong. Please try again later.";
                }
                $stmt2->close();
            }
            $stmt->close();
        }
    }
    $mysqli->close();
}

?>
