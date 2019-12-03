<?php
session_start();
require_once "config.php";

$id = -1;
if (is_numeric(htmlspecialchars($_GET["id"]))) {
    $id = htmlspecialchars($_GET["id"]);
}

$sql = "SELECT * FROM projects WHERE `id`= " . $id;
$project = (object)[];
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
    }
}

$_SESSION["currentId"] = $id;

?>

<div class='text-center p-3'>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <h1 class="text-white"> <?php echo $project->name; ?></h1>
    <p class="text-white"> Project ID: <?php echo $id; ?></p>

    <?php
    if ($project->owner != -1) {
        echo '<p class="text-white">' . $project->description . '</p>';
    } else {
        echo '<textarea id="desc" style="resize: none;" class="form-control my-3">' . $project->description . '</textarea>';
    }
    ?>

    <div id="slideshow" class="carousel slide" data-ride="carousel" style="height: 400px;">
        <div class="carousel-inner" style="height: 100%;">
            <?php
            $directory = __DIR__ . "/resources/" . $id;
            $isActive = true;
            if (is_dir($directory)) {
                foreach (scandir($directory) as $file) {
                    if ($file !== '.' && $file !== '..') {
                        if ($isActive) {
                            echo "<div class='carousel-item active'><img style='object-fit: cover; object-position: center; width:100%; height: 100%;' src='resources/" . $id . "/" . $file . "' alt=''/></div>";
                            $isActive = false;
                        } else {
                            echo "<div class='carousel-item'><img style='object-fit: cover; object-position: center; width:100%; height: 100%;' src='resources/" . $id . "/" . $file . "' alt=''/></div>";
                        }
                    }
                }
            }
            if ($isActive) {
                echo "<div class='carousel-item active'><img style='object-fit: cover; object-position: center; width:100%; height: 100%; alt='' src='img/default.png' alt=''/></div>";
            }
            ?>
        </div>

        <a class="carousel-control-prev" href="#slideshow" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#slideshow" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <?php
    if ($project->owner != -1) {
        echo '<button class="btn btn-custom mt-3 p-3 mb-5" onclick="joinProject(' . $project->id . ')"> I can help! </button>';
    } else {
        echo '<form style="all:unset" id="imgForm" action="uploadImage.php" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload" style="display:none;" accept="image/png, image/jpeg"/>
            <label class="btn btn-custom text-white p-3 mr-3 mt-2" for="fileToUpload">Add Image</label>
          </form>';
        echo '<button class="btn btn-custom p-3" onclick="resetImages(' . $project->id . ')"> Reset Images </button>';
    }
    ?>

    <?php
    if ($project->owner != -1) {
        echo '<div class="progress mx-5" style="height: 20px;">
        <div class="progress-bar" role="progressbar" style="width: ' . $project->percent . '%;" aria-valuenow="'.$project->percent . '" aria-valuemin="0" aria-valuemax="100"></div>
    </div>';
    } else {
        echo '<h5 class="text-white mt-2"> How much progress left? </h5><input type="range" min="1" max="100" value="' . $project->percent . '" class="slider mt-3" id="percentComplete"/><p class="text-center text-white" id="percentView">'.$project->percent.'%</p>';
    }
    ?>

    <?php
    if ($project->owner == -1) {
        echo '<button class="btn btn-custom mt-3 p-3" onclick="updateProject(' . $project->id . ')"> Update Project </button>';
    }
    ?>
</div>
<script>
    document.getElementById("fileToUpload").onchange = function () {
        document.getElementById("imgForm").submit();
    };
    $("#percentComplete").on('input', function () {
        $("#percentView").text($("#percentComplete")[0].value + "%");
    });
</script>