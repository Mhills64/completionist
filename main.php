<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /");
    exit;
}

include "pageSetup.php";

?>

<div class="box">
    <nav class="navbar navbar-expand-lg navbar-light bg-light" style="background-color: #5BBECC!important">
        <a class="navbar-brand text-white" href="#" style="font-family: 'Kalam', cursive; font-size: 2em;">Completionist</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <form class="form-inline my-2 my-lg-0">
                    <button id="create" type="button" class="btn btn-custom my-2 mr-4" data-toggle="modal"
                            data-target="#info_create">
                        Create Project
                    </button>

                    <div class="text-right">
                        <p style="display: block" class="text-white my-0 py-0"> Welcome <?php echo $_SESSION["username"] ?>!</p>
                        <p style="display: block" class="text-white my-0 py-0"> Not you? <a href="logout"> Logout </a></p>
                    </div>
                </form>
            </ul>
        </div>
    </nav>

    <div class="row m-0">
        <div id="projects" class="col-4 hidden-md-down"
             style="background-color: #5B6363; border-right: 4px solid #FBB13C;">
        </div>

        <div id="stuff" class="col col-sm-10 col-md-10 col-lg-8 m-0 p-0">
            <div id="map" style="background-color: #5B6363;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="info_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" id="modal-create" style="background-color: #5BBECC;">

        </div>
    </div>
</div>

<div class="modal fade" id="info" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-info" style="background-color: #5BBECC;">

        </div>
    </div>
</div>

<div id="bottomChat">
    <button class="btn p-2 text-white btn-custom-full" style="position: relative; right: 10px; bottom: 10px;"
            type="button" data-toggle="collapse" data-target="#chatStuff"
            aria-expanded="false"
            aria-controls="chatStuff"> Chat
    </button>
    <div id="chatStuff" class="collapse">
        <div id="chatbox">
            <form class="chatForm" id="chat" autocomplete="off">
                <div id="chatSelect" class="py-3 mb-3">
                    <?php
                    $sql = 'SELECT * FROM members WHERE username="' . $_SESSION["username"] . '"';

                    $result = $mysqli->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            $sql2 = 'SELECT * FROM projects WHERE id=' . $row['projectId'];
                            $result2 = $mysqli->query($sql2);
                            if ($result2->num_rows > 0) {
                                while ($row2 = $result2->fetch_assoc()) {
                                    echo "<a id='chatOption" . $row["projectId"] . "' class='chat-button-link mr-2' tabindex='-1' onclick='setValue(" . $row["projectId"] . ")'> " . $row2["name"] . "</a>";
                                }
                            }

                        }
                    }
                    $mysqli->close();

                    ?>
                </div>
                <div style="white-space:pre-wrap; overflow-y: scroll; overflow-wrap: break-word;" id="chatArea"
                     class="form-control"></div>
                <input autocomplete="off" id="textToAdd" type="text"/>
                <button id="sendButton" class="chat-button" type="submit">Send</button>
            </form>
        </div>
    </div>
</div>

</body>

<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>

<script src="main.js"></script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyuILFrehFIu_fpRoGFuhsru29B5_HZ2Y&callback=initMap">
</script>
</html>