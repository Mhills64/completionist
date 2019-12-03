<?php
session_start();
require_once "config.php";

$stuff = array();

$sql = "SELECT * FROM projects";

$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $project = (object)[];
        $project->id = $row["id"];
        $project->name = $row["name"];
        $project->description = $row["description"];
        $project->percent = $row["percent"];
        $project->coord_lat = $row["coord_lat"];
        $project->coord_long = $row["coord_long"];
        if ($_SESSION["username"] == $row["owner"]) {
            $project->owner = -1;
        } else {
            $project->owner = $row["owner"];
        }

        array_push($stuff, $project);

    }
}

echo json_encode(array_values($stuff));
$mysqli->close();

?>
