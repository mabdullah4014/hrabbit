
<div class="cat" style="float:right;">
<!-- <h4>Category Colour</h4>
<p>Hatchback:  Red</p>
<p>Sedan: Blue</p>
<p>SUV: Green</p>
<p>14seatervan: Block</p> -->

</div><!-- /.page-header -->

  <div class="row">
    <div class="col-xs-12">

        <div  id="map"></div>

        <script src="/js/firebase_364.js"></script>
        <!-- GeoFire -->
        <script src="/js/geofire.min_410.js"></script>

        <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}"></script>
            <!-- GeoFire -->
        <script type="text/javascript">
              $(document).ready(function(){
                  //Check if the current URL contains '#'
                  if(document.URL.indexOf("#")==-1){
                      // Set the URL to whatever it was plus "#".
                      url = document.URL+"#";
                      location = "#";

                      //Reload the page
                      location.reload(true);
                  }
              });
        </script>

        <script type="text/javascript">
                            // Initialize Firebase
          var config = {
            apiKey: {{env('FIREBASE_APIKEY')}},
            authDomain: {{env('FIREBASE_AUTHDOMAIN')}},
            databaseURL: {{env('FIREBASE_DB')}},
            projectId: {{env('FIREBASE_PROJECT_ID')}},
            storageBucket: {{env('FIREBASE_STORAGE_BUCKET')}},
            messagingSenderId: {{env('FIREBASE_SENDER_ID')}}
          };

          var image = "uploads/car.svg";

          firebase.initializeApp(config);
           var firebaseRef = firebase.database().ref();
           var map ;
            var marker = [];
           map = new google.maps.Map(document.getElementById('map'), {
                center: {
                  lat: 0,
                  lng: 0
                },
                zoom: 2,
                styles: [{
                  featureType: 'poi',
                  stylers: [{
                      visibility: 'off'
                    }] // Turn off points of interest.
                }, {
                  featureType: 'transit.station',
                  stylers: [{
                      visibility: 'off'
                    }] // Turn off bus stations, train stations, etc.
                }],
                disableDoubleClickZoom: false,
                   mapTypeId: google.maps.MapTypeId.ROADMAP,
                  streetViewControl: true,
                   mapTypeControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_TOP
                  },
                  zoomControlOptions: {
                     position: google.maps.ControlPosition.RIGHT_BOTTOM
                 }
          });

          var baseurl = {{env('APP_URL')}};
          var markers = [];
          var markers_name = [];
          var markers_temp = [];

          var vector_arr = [];

          var category_arr = [<?php echo $cat_implode; ?>];


          var vectorC_arr = ["#4f2a0f","#060606","#f27310","#676568"];

          //category name
          var uid_name = [<?php echo $All_driver_Name; ?>];

          //driver id all
          var uid_mob = [];
          //driver id all

          var uid = [<?php echo $driverid; ?>];

          //firebase latitude and longtitude
          var u_loc = <?php echo $finalData; ?>

          // var drivername=[""]
          //alert(drivername);
          var i, s, caArray = category_arr, len = caArray.length;

          var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

          var i, s, caArray = category_arr, len = caArray.length;

          if (jQuery.inArray('14seatervan', caArray)!='-1') {
                var icon = {
              path: car,
              scale: .7,
              strokeColor: 'white',
              strokeWeight: .10,
              fillOpacity: 1,
              fillColor: "#000000",
              offset: '5%',
              scaledSize: [25, 25]
              }

          } else if (jQuery.inArray('Hatchback', caArray)!='-1') {
                var icon = {
              path: car,
              scale: .7,
              strokeColor: 'white',
              strokeWeight: .10,
              fillOpacity: 1,
              fillColor: "#FF0000",
              offset: '5%',
              scaledSize: [25, 25]
              }

          } else if(jQuery.inArray('SUV', caArray)!='-1') {

           var icon = {
              path: car,
              scale: .7,
              strokeColor: 'white',
              strokeWeight: .10,
              fillOpacity: 1,
              fillColor: "#008000",
              offset: '5%',
              scaledSize: [25, 25]
              }

          } else if(jQuery.inArray('Sedan', caArray)!='-1') {

           var icon = {
              path: car,
              scale: .7,
              strokeColor: 'white',
              strokeWeight: .10,
              fillOpacity: 1,
              fillColor: "#0000FF",
              offset: '5%',
              scaledSize: [25, 25]
              }

          } else {
            var icon = {
              path: car,
              scale: .7,
              strokeColor: 'white',
              strokeWeight: .10,
              fillOpacity: 1,
              fillColor: "#008000",
              offset: '5%',
              scaledSize: [25, 25]
              }
          }


        function load_map() {
          for (i=0; i<len; i++) {
            var firebaseRef_new = firebase.database().ref('drivers_location/'+caArray[i]);
            firebaseRef_new.on("child_added", function(snapshot, prevChildKey) {

              var firebaseRef_new1 = firebase.database().ref('drivers_status/'+snapshot.V.path.o[2]);

              firebaseRef_new1.on("child_added", function(snapshot1, prevChildKey1){
                firebaseRef_new1.once("value", function(snapshot2) {
                  snapshot2.forEach(function(child) {
                  var getid = snapshot.getKey();
                  var newPosition = snapshot.val();
                  //console.log(newPosition[i]);

                  //console.log(newPosition['geolocation']['l'][1]);
                  var latLng = new google.maps.LatLng(newPosition['l'][0],

                  newPosition['l'][1]);
                  marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    icon: icon
                  });
                  markers.push(marker);

                   google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {

                      firebase.database().ref('drivers_status/' + getid).once('value').then(function(snapshot) {
                        var driver_name = snapshot.val().fname;

                        var displaytext= 'ID:'+ getid+',Driver Name:'+ driver_name;

                        //infowindow.setContent(snapshot.V.path.o[2]);
                        infowindow.setContent(displaytext);
                        });
                        infowindow.open(map, marker);
                        }
                    })(marker, i));

                  });
                });
              });
            });
            i++;
          }
        }

        load_map();

        function map_data() {
          for (i=0; i<len; i++)  {
            var len_uid = u_loc.length;
            var i, s, caArray = category_arr, len = caArray.length;
            for (j=0; j<len_uid; j++) {
                //icon.fillColor = '#b28b29';
                icon.fillColor = vectorC_arr[i];
                icon.path = vector_arr[i];
                var latLng = new google.maps.LatLng(u_loc);
                var infodata = uid_name+"<br>"+uid_mob;
                var markerid = uid;
                console.log(markerid);
                 marker[markerid] = new google.maps.Marker({
                  position: latLng,
                  map: map,
                  title: infodata,
                  icon: icon
                });

                 marker[markerid].id = markerid;
                 markers.push(marker[markerid]);
                 markers_name.push(infodata);

                google.maps.event.addListener(marker[markerid], 'click', function(){
                  infowindow.setContent(this.title);
                      infowindow.open(map, this);
                 }
                );

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        firebase.database().ref('drivers_status/' + markerid).once('value').then(function(snapshot) {
                        var driver_name = snapshot.val().fname;
                        var displaytext= 'ID:'+ markerid+',Driver Name:'+ driver_name;
                        //infowindow.setContent(snapshot.V.path.o[2]);
                        infowindow.setContent(displaytext);
                      });
                      infowindow.open(map, marker);
                    }
                })(marker, i));
            }
          }
        }

        map_data();

        function setMapOnAll(map) {
          for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
          }
        }

        // Removes the markers from the map, but keeps them in the array.
        function clearMarkers()  {
         setMapOnAll(null);
        }

        function deleteMarkers() {
          clearMarkers();
          markers = [];
        }

        function DeleteMarker(id) {
          //Find and remove the marker from the Array
          for (var i = 0; i < markers.length; i++) {
              if (markers[i].id == id) {
                //alert(markers[i].id);
                console.log(markers);
                //Remove the marker from Map
                markers[i].setMap(null);
                //Remove the marker from array.
                // alert(markers_name);
                markers.splice(i, 1);
                return markers_name[i];
              }
          }
        };

          //var commentsRef = firebase.database().ref().child('KZaEvq_mipwphn6wgmJ');
          var infowindow = new google.maps.InfoWindow({ });
          var m_name = "";

          function update_map() {
            var i, s, caArray = category_arr, len = caArray.length;
            for (i=0; i<len; ++i) {
                if (i in caArray) {
                  var geoFire = new GeoFire(firebaseRef);
                  if(caArray[i] !='null') {
                    var firebaseRef_ch = firebase.database().ref('drivers_location/'+caArray[i]);
                  }

                  firebaseRef_ch.on('child_changed', function(snapshot, prevChildKey) {
                    var getid = snapshot.getKey();
                    //var getname= drivername;
                    //alert(getname);
                    var newPosition =snapshot.val();
                    // console.log(newPosition);
                    var arr = Object.keys(newPosition).map(function(k) { return newPosition[k] });
                    if( 'l' in arr[caArray[i]][0] )  {
                        markers_temp = markers ;
                        deleteMarkers();
                    }
                    DeleteMarker(getid);
                    m_name = getid;
                    firebase.database().ref('/drivers_location/' + getid).once('value').then(function(snapshot) {
                    });
                    //console.log(newPosition.l[0],newPosition.l[1]);
                    // console.log(newPosition.bearing);
                    icon.rotation = parseInt(newPosition.bearing);
                    marker[getid] = new google.maps.Marker({

                    position: new google.maps.LatLng(newPosition.l[0],newPosition.l[1]),
                    map: map,
                    title : m_name,
                     icon: icon
                    });

                    marker[getid] = new google.maps.Marker({

                    position: new google.maps.LatLng(newPosition.l[0],newPosition.l[1]),
                    map: map,
                    title : m_name,
                     icon: icon
                    });
                    marker[getid].id = getid;
                    markers.push(marker[getid]);
                    google.maps.event.addListener(marker[getid], 'click', function(){
                      firebase.database().ref('/drivers_status/' + getid).once('value').then(function(snapshot) {
                        var driver_name = snapshot.val().fname;
                        var displaytext= 'ID:'+ getid+',Driver Name:'+ driver_name;
                        infowindow.setContent(displaytext);
                      });

                      infowindow.open(map, this);
                    });
                  });
              }
            }
          }
          setInterval(function(){update_map(); }, 3000);
          update_map();
          google.maps.event.addDomListener(window, "load");
      </script>


</script>

      <style type="text/css">
          #map {
          height: 700px;
          width: 100%;
          background-color: #CCC;
      }
      </style>
    </div>
  </div>
</div>