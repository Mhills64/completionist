let mapLoaded = false; // Don't try to put markers on a map that doesn't exist yet
let projects = []; // List of projects, loaded from server
let markers = [];
let map; // Map element
let isActive = false;
let numLines = 0;
var selectedChat = -1;
var isRunning = false;

$(document).ready(function () {
    // Load projects from server
    $.ajax({
        url: '/getProjects.php',
        type: 'GET',
        data: {},
        success: function (r) {
            // This file returns parsable json, loop through it and populate projects with data
            let results = JSON.parse(r);
            for (let i = 0; i < results.length; i++) {
                populateProjects(results[i].id, results[i].name, results[i].owner, results[i].description, results[i].percent, results[i].coord_lat, results[i].coord_long)
            }
            // After projects list has been populated, add markers to map with each of their long and lat coords
            addMarkers();
        }
    });
});

// Basic function that populates list of projects
function populateProjects(id, projectName, username, desc, percent, lat, long) {
    if (username === -1) { // If its your project, you want to show a delete button
        $("#projects").append("<div id='project" + id + "' class='px-5 py-3 my-3 request'> <h5 class='text-white'> " +
            projectName + " </h5> <p class='text-white m-0 p-0'> " + desc +
            "</p> <div class='progress my-3'> <div class='progress-bar' role='progressbar' style='width: " + percent + "%' aria-valuenow='" + percent +
            "' aria-valuemin='0' aria-valuemax='100'>" + percent + "%</div> </div> <p class='float-left text-white'> Created by you </p> <button class='btn btn-custom float-right mx-1' onclick='deleteProject(" + id + ")'> Delete </button> <button class='btn btn-custom float-right mx-1' onclick='switchToInfo(" + id + ")'> Manage</button> </div>");

    } else {
        $("#projects").append("<div id='project" + id + "' class='px-5 py-3 my-3 request'> <h5 class='text-white'> " +
            projectName + " </h5> <p class='text-white m-0 p-0'> " + desc +
            "</p> <div class='progress my-3'> <div class='progress-bar' role='progressbar' style='width: " + percent + "%' aria-valuenow='" + percent +
            "' aria-valuemin='0' aria-valuemax='100'>" + percent + "%</div> </div> <p class='float-left text-white'> Created by <a href=''>" + username + "</a></p> <button class='btn btn-custom float-right' onclick='switchToInfo(" + id + ")'> See more details </button> </div>");
    }
    projects.push({
        id: id,
        projectName: projectName,
        userName: username,
        desc: desc,
        percent: percent,
        lat: lat,
        long: long
    });
}

function createProjectFromLocation(projectName, projectDescription, percent, address) {
    $.ajax({
        url: 'https://maps.googleapis.com/maps/api/geocode/json?address=' + address + '&key=AIzaSyCyuILFrehFIu_fpRoGFuhsru29B5_HZ2Y',
        type: 'GET',
        success: function (r) {
            if (r != null) {
                if (r.status === "OK") {
                    let coords = r.results[0].geometry.location;
                    $.ajax({
                        url: '/createProject.php',
                        type: 'GET',
                        data: {
                            name: projectName,
                            description: projectDescription,
                            percent: percent,
                            lat: coords.lat,
                            long: coords.lng
                        },
                        success: function (e) {
                            $("#modal-create").html('<div class="mx-auto text-center"><h3 class="text-white m-3"> Success! </h3> </div>');
                            setTimeout(function() {
                                window.location.reload();
                            }, 500);
                        }
                    });
                } else {
                    $("#modal-create").html('<div class="mx-auto text-center"><h3 class="text-danger m-3"> Error </h3><p class="text-white"> That location cannot be found! </p> <button type="button" class="btn btn-custom m-3" data-dismiss="modal" aria-label="Close"> Close </button> </div>');
                }
            }
        }
    });
}

function deleteProject(id) {
    $.ajax({
        url: "/deleteProject.php",
        type: 'GET',
        data: {id: id},
        success: function(r) {
            window.location.reload();
        }
    });
}

function joinProject(id) {
    $.ajax({
        url: "/joinProject.php",
        type: 'GET',
        data: {id: id},
        success: function(r) {
            $("#modal-info").html('<div class="mx-auto text-center"><h3 class="text-white m-3"> Joined! </h3> </div>');
            setTimeout(function() {
                window.location.reload();
            }, 500);
        }
    });
}

// Initial Map
function initMap() {
    var uloc = {lat: 43.0846, lng: -77.6743}; // User's location
    map = new google.maps.Map(
        document.getElementById('map'), {
            zoom: 12, center: uloc,
            disableDefaultUI: true
        });

    mapLoaded = true;
}

$("#create").click(function (f) {
    $("#modal-create").html('<div class="modal-body py-3"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h3 class="text-white mb-3" style="border-bottom: 2px solid white"> Let\'s create a project! </h3> <input id="projectName" class="form-control" type="text" placeholder="Project Name" aria-label="name"/> <textarea id="desc" placeholder="Project Description" style="resize: none;" class="form-control mt-3"></textarea> <input id="location" class="form-control mt-3" type="text" placeholder="Location" aria-label="location"/><h5 class="text-white mt-2"> How much progress have you made so far? </h5><input type="range" min="1" max="100" value="50" class="slider mt-3" id="percentComplete"/><p class="text-center text-white" id="percentView">50%</p><button id="createProject" class="btn my-3 btn-custom float-right" type="submit">Submit</button></div>');
    $("#percentComplete").on('input', function () {
       $("#percentView").text($("#percentComplete")[0].value + "%");
    });

    $("#createProject").click(function (f) {
        f.preventDefault(); // don't reload page

        // Default project coords are RIT
        let projectName = $("#projectName").val();
        let projectDescription = $("#desc").val();
        let address = $("#location").val();
        let percent = $("#percentComplete")[0].value;

        // Show loading
        $("#modal-create").html('<div class="mx-auto text-center"><h3 class="text-white m-3"> Creating your project... </h3><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');

        createProjectFromLocation(projectName, projectDescription, percent, address); // Tries to create a project

    });
});


function updateProject(i) {
    let projectDescription = $("#desc").val();
    let percent = $("#percentComplete")[0].value;

    $.ajax({
        url: "/updateProject.php",
        type: 'GET',
        data: {id: i, percent: percent, description: projectDescription},
        success: function(r) {
            $("#modal-info").html('<div class="mx-auto text-center"><h3 class="text-white m-3"> Updated! </h3> </div>');
            setTimeout(function() {
                window.location.reload();
            }, 500);
        }
    });
}

function addMarkers() {
    if (mapLoaded) { // Map does not load instantly and neither do projects so if its not loaded yet, try again in
        // 0.1 seconds
        for (let i = 0; i < projects.length; i++) {
            markers[projects[i].id] = new google.maps.Marker({
                position: {
                    lat: Number(projects[i].lat),
                    lng: Number(projects[i].long)
                }, map: map, title: projects[i].projectName
            });
            markers[projects[i].id].addListener("click", function () {
                switchToInfo(projects[i].id);
            });
        }
    } else {
        setTimeout(function () {
            addMarkers();
        }, 100);
    }
}

function switchToInfo(id) {
    let p = projects.find(item => item.id == id);

    $("#modal-info").load("/project?id=" + id, function () {
        $("#info").modal({show: true});
    });
}

function update() {
    setInterval(function () {
        if (isActive) { return; }
        isActive = true;
        try {
            $.ajax({ url: '/readChat.php',
                type: 'GET',
                data: {'id': selectedChat},
                success:function(result) {
                    $("#chatArea").html(result);
                    if (numLines !== result.split("</b>"|"</p>").length) {
                        document.getElementById("chatArea").scrollTop = document.getElementById("chatArea").scrollHeight;
                        numLines = result.split("</b>"|"</p>").length;
                    }
                    isActive = false;
                }
            });
        } catch (ex) { isActive = false;}
    }, 200);
}

$("#chat").submit(function(e) {
    e.preventDefault();
    console.log("you've been clonked");


    console.log("addingg: " + $("#textToAdd").val() + " to " + selectedChat);
    if ($("#textToAdd").val().trim() !== "") {
        $.ajax({ url: '/addMessage.php',
            data: {'id': selectedChat, 'message': $("#textToAdd").val()},
            type: 'GET',
            error:function(exception){alert('Exception:'+exception);}
        });
    }
    $("#textToAdd").val("");
});

// Used for setting chat partner integer
function setValue(i) {
    console.log("changing to " + i);
    $("#chatOption" + selectedChat).removeClass("disabled");
    selectedChat = i;
    $("#chatOption" + selectedChat).addClass("disabled");
    if (!isRunning) {
        isRunning = true;
        update();
    }
}


function resetImages(id) {
    alert("resetting " + id);
    $.ajax({
        url: "/resetImages.php",
        type: 'GET',
        data: {id: id},
        success: function(r) {
            window.location.reload();
        }
    });
}