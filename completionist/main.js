
var mapLoaded = false;

// Must be changed because it effects creating a project
$(document).ready(function() {
   populateProjects(1, "Completionist", "314owen", "An app that allows you to find others nearby who are interested in working on projects with you.", 69, 43.0846, -77.6743);
   populateProjects(2, "GeoTrash", "Anisa", "Find that trash wooo!", 22, 43.0945, -77.6747);
   populateProjects(3, "Refugee something", "Matt", "I need refugees to work for me with no pay at all b/c you are refugees", 60, 43.0752, -77.6641);
   populateProjects(4, "I forget the name", "M2tt", "The matt that actaully does stuff", 12, 43.0894, -77.6834);

   addMarkers();
});

var projects = [];
var map;

// Basic function that populates list of projects
function populateProjects(id, projectName, userName, desc, percent, lat, long) {
    $( "#projects" ).append("<div class='p-2 m-2 request'> <h5 class='text-white'> " +
        projectName + " </h5> <p class='text-white m-0 p-0'> " + desc +
        "</p> <div class='progress my-3'> <div class='progress-bar' role='progressbar' style='width: " + percent + "%' aria-valuenow='" + percent +
        "' aria-valuemin='0' aria-valuemax='100'>" + percent + "%</div> </div> <p class='float-left text-white'> Created by <a href=''>" + userName + "</a></p> <button class='btn btn-outline-primary btn-custom float-right'> I can help! </button> </div>")
    projects.push({id: id, projectName: projectName, userName: userName, desc: desc, percent: percent, lat: lat, long: long});
}

function initMap() {
    var uloc = {lat: 43.0846, lng: -77.6743}; // User's location
    map = new google.maps.Map(
        document.getElementById('map'), {zoom: 15, center: uloc,
            disableDefaultUI: true});

    mapLoaded = true;
}

let newprojid = 5;
let projs = 0;

    $("#create").click(function (e) {
        if (projs < 2) {
        e.preventDefault();
        var ulat, ulng = 0;
        navigator.geolocation.getCurrentPosition(function (position) {
            ulat = position.coords.latitude;
            ulng = position.coords.longitude;
            projs += 1;
            populateProjects(newprojid, "Test", "test", "Test", 75, ulat, ulng);
            let newmark = new google.maps.Marker({position: {lat: ulat, lng: ulng}, map: map});
            newmark.addListener('click', function () {
                switchToInfo(newprojid);
            });
            return projs
        });

        } else {
            e.preventDefault();
            console.log('Can only add 2 projects')
        }
    });


function addMarkers() {
    if (mapLoaded) {
        for (let i = 0; i < projects.length; i++) {
            let marker = new google.maps.Marker({position: {lat: projects[i].lat, lng: projects[i].long}, map: map, title: projects[i].projectName});
            marker.addListener("click", function() {
                console.log(projects[i].id);
                switchToInfo(projects[i].id);
            });
        }
    } else {
        setTimeout(function() {
            addMarkers();
        }, 200);
    }
}

function switchToInfo(id) {
    let projectObject = projects.find(item => item.id === id);
    $("#map").hide();
    $("#info").show();
    $("#info").html("<button class='btn btn-outline-primary m-3' onclick='backToMap();'>Back</button> <div class='my-3 text-center'><h2> " +
        projectObject.projectName + " </h2>" + imageSlideShow() + "<p> " + projectObject.desc + " </p></div>");
}




function imageSlideShow() {
    return '<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">\n' +
        '  <ol class="carousel-indicators">\n' +
        '    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>\n' +
        '    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>\n' +
        '    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>\n' +
        '  </ol>\n' +
        '  <div class="carousel-inner">\n' +
        '    <div class="carousel-item active">\n' +
        '      <img class="d-block w-100" src="img/1.jpg" alt="First slide">\n' +
        '    </div>\n' +
        '    <div class="carousel-item">\n' +
        '      <img class="d-block w-100" src="img/2.jpg" alt="Second slide">\n' +
        '    </div>\n' +
        '    <div class="carousel-item">\n' +
        '      <img class="d-block w-100" src="img/3.jpg" alt="Third slide">\n' +
        '    </div>\n' +
        '  </div>\n' +
        '  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">\n' +
        '    <span class="carousel-control-prev-icon" aria-hidden="true"></span>\n' +
        '    <span class="sr-only">Previous</span>\n' +
        '  </a>\n' +
        '  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">\n' +
        '    <span class="carousel-control-next-icon" aria-hidden="true"></span>\n' +
        '    <span class="sr-only">Next</span>\n' +
        '  </a>\n' +
        '</div>';
}

function backToMap() {
    $("#map").show();
    $("#info").hide();
}