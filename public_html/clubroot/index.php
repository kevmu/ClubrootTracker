<!DOCTYPE html>
<html>
<head>
	
	<title>ClubrootTracker</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js" integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA==" crossorigin=""></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
		html, body {
			height: 100%;
		}
		#map {
			width: 100vw;
			height: 100%;
		
		}
		.container {
    width: 100%;
    padding-right: 30px;
    padding-left: 30px;
    margin-right: 0;
    margin-left: 0;
			
		}
	</style>

	
</head>
<body>
<div class="container">
<!--<img src="images/clubroot-marker.png" alt="marker" height="50px" width="50px">
</div>
-->
<div class="row">
<div class="col-xs-12">

  <form role="search">
    <div class="input-group add-on">
      <input type="text" class="form-control" placeholder="Search" name="addr" value="" id="addr">
      <div class="input-group-btn">
        <button id="search_button" class="btn btn-default" type="button" onclick="addr_search()";\><i class="fa fa-search"></i> Search</button>
      </div>
    </div>
  </form>


<div id="results"></div>
</div>
</div>
</div>
<div id='map'></div>

<script src="tracker_geojson.js" type="text/javascript"></script>

<script>
	var map = L.map('map').setView([52.146973, -106.647034], 4);

	//L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
		id: 'map'
	}).addTo(map);

	function onEachFeature(feature, layer) {
		var popupContent = "<p>Location: " + feature.properties.location + ", " + feature.properties.province + "</p>";

		if (feature.properties && feature.properties.popupContent) {
			popupContent += "<p>Description: " + feature.properties.popupContent + "</p>";
		}

		layer.bindPopup(popupContent);
	}

	L.geoJSON([clubroot_tracker_coords], {

		style: function (feature) {
			return feature.properties && feature.properties.style;
		},

		onEachFeature: onEachFeature,

		pointToLayer: function (feature, latlng) {
			return L.circleMarker(latlng, {
				radius: 6,
				fillColor: "#ff7800",
				color: "#000",
				weight: 1,
				opacity: 1,
				fillOpacity: 0.8
			});
		}
	}).addTo(map);



function chooseAddr(lat1, long1, display_name, province){
//function chooseAddr(lat1, long1, province){

    var features = clubroot_tracker_coords["features"];
    //console.log(province);
    var within_radius = [];
    var hdist_max = 200;
    var hdist_curr = hdist_max;
    var hdist_curr_index = 0;
    var j = 0;

    // Clear results of div tag text as it isn't required anymore.
    document.getElementById('results').innerHTML = "";
    for(i = 0; i < features.length; i++){
	    var json_province = features[i].properties.province;
	    if(province == json_province){
		    console.log(features[i].properties.province);
            var long2 = features[i].geometry.coordinates[0];
            var lat2 = features[i].geometry.coordinates[1];
            var hdist = haversine_km(lat1, long1, lat2, long2);
            
            if(hdist <= hdist_max){
                
                console.log(display_name);
                console.log(features[i].properties.location + ", " + features[i].properties.province);
                console.log(hdist);
                console.log(lat2 + ", " + long2);
                features[i].hdist = hdist;
                within_radius.push(features[i]);
                if(hdist < hdist_curr){
                    
                    hdist_curr = hdist;
                    hdist_curr_index = j;
                }
                j++;
                
            }
        }
    }

    if(within_radius.length > 0){
        var marker = L.marker([lat1, long1]).addTo(map);
        map.setView([lat1, long1],10);
        
        var popupContent = "<p>Location: " + display_name + "</p>";
//         var popupContent = "<p style=\"font-weight: bold;color:red;\">" + within_radius[0].properties.pathogenCommonName + " Pathogen Detected!" + "</p><p>Location: " + province + "</p><p>Coordinates: " + lat1 + ", " + long1 + "</p>";
        if (within_radius[hdist_curr_index].properties && within_radius[hdist_curr_index].properties.popupContent) {
            popupContent += "<p>Description: " + within_radius[hdist_curr_index].properties.popupContent + "</p><p>Closest Location is " + within_radius[hdist_curr_index].hdist.toFixed(2) + " kms away in " + within_radius[hdist_curr_index].properties.location + ", " + within_radius[hdist_curr_index].properties.province + "</p><p>To perform sanitation please check this guide: <a href=\"https://www.canolacouncil.org/media/530963/clubroot_sanitation_guide.pdf\" target=\"_blank\">[PDF]</a></p><p>For more on Clubroot Control: Prevention and Management <a href=\"https://www.canolacouncil.org/canola-encyclopedia/diseases/clubroot/control-clubroot\" target=\"_blank\">[URL]</a></p>";
        }
        marker.bindPopup(popupContent);
        marker.openPopup();
    }else{
	document.getElementById('results').innerHTML = "";	
	document.getElementById('results').innerHTML += "<p>No pathogen detected within " + hdist_max + " kms.<p>";
    }
}

function myFunction(arr){
 
    var out = "<br />";
    var i;
 
    document.getElementById('results').innerHTML = "";   
    if(arr.length > 0){

        for(i = 0; i < arr.length; i++){
            
            console.log(arr[i]);
            out += "<div class='address' title='Show Location and Coordinates' onclick='chooseAddr(" + arr[i].lat + ", " + arr[i].lon + ", \"" + arr[i].display_name + "\", \"" + arr[i].address.state + "\");return false;'>" + arr[i].display_name + "</div>";
        }
        document.getElementById('results').innerHTML = out;
    }
    else{
        document.getElementById('results').innerHTML = "Sorry, no results...";
    }
    
}

function addr_search(){
    var inp = document.getElementById("addr");
    var xmlhttp = new XMLHttpRequest();
    var url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&addressdetails=1&q=" + inp.value;
    xmlhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var myArr = JSON.parse(this.responseText);
            myFunction(myArr);
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

//calculate haversine distance for linear distance
function haversine_km(lat1, long1, lat2, long2){
    var deg2rad = (Math.PI / 180.0);
    var dlong = (long2 - long1) * deg2rad;
    var dlat = (lat2 - lat1) * deg2rad;
    var a = Math.pow(Math.sin(dlat/2.0), 2) + Math.cos(lat1*deg2rad) * Math.cos(lat2*deg2rad) * Math.pow(Math.sin(dlong/2.0), 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = 6367 * c;
    
    return d;
}

//function haversine_mi(lat1, long1, lat2, long2){
//    var dlong = (long2 - long1) * deg2rad;
//    var dlat = (lat2 - lat1) * deg2rad;
//    var a = pow(sin(dlat/2.0), 2) + cos(lat1*deg2rad) * cos(lat2*deg2rad) * pow(sin(dlong/2.0), 2);
//    var c = 2 * atan2(sqrt(a), sqrt(1-a));
//    var d = 3956 * c;
//    
//    return d;
//}

document.getElementById("pw")
    .addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("search_button").click();
    }
});
    
</script>



</body>
</html>
