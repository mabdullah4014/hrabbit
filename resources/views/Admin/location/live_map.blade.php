     <style type="text/css">
          #map {
          height: 700px;
          width: 100%;
          background-color: #CCC;
      }
     </style>
  
    <div id="map"></div>
    <script src="https://www.gstatic.com/firebasejs/3.7.0/firebase.js"></script>
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
    <!-- GeoFire -->
    <script src="https://cdn.firebase.com/libs/geofire/4.1.2/geofire.min.js"></script>
        <script>
          var config = {
            apiKey: "{{env('FIREBASE_APIKEY')}}",
            authDomain: "{{env('FIREBASE_AUTHDOMAIN')}}",
            databaseURL: "{{env('FIREBASE_DB')}}",
            projectId: "{{env('FIREBASE_PROJECT_ID')}}",
            storageBucket: "{{env('FIREBASE_STORAGE_BUCKET')}}",
            messagingSenderId: "{{env('FIREBASE_SENDER_ID')}}"
          };

          /*firebase.initializeApp(config);
            var leadsRef = firebase.database().ref('drivers_location');

            function initMap() {
            var myLatLng = {lat: 9.939093, lng: 78.121719};

            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: 4,
              center: myLatLng
            });

            var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

             var red_icon = {
                          path: car,
                          scale: .7,
                          strokeColor: 'white',
                          strokeWeight: .10,
                          fillOpacity: 1,
                          fillColor: "#000000",
                          offset: '5%',
                          scaledSize: [25, 25]
                        }

                var green_icon = {
                    path: car,
                    scale: .7,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: "#008000",
                    offset: '5%',
                    scaledSize: [25, 25]
                    }

            leadsRef.on('value', function(snapshot) {
                snapshot.forEach(function(childSnapshot) {
                  var childData = childSnapshot.val();
                  var i=0;
                  for(var x in childData){

                    //alert(JSON.stringify(childData[x].l[0]));
                    var contentString = "";
                    var urlRef = firebase.database().ref("drivers_status/"+x);
                    urlRef.once("value", function(snapshot) {
                      snapshot.forEach(function(child) {
                        if(child.key == "fname"){
                           contentString = '<p>Driver Name : '+child.val()+'</p>';
                        }
                      });
                    });
                    

                    if(childData[x].status == 0){
                        

                        var pin = red_icon;

                    }else{
                   
                        var pin = green_icon;
                    }
                    

                    var infowindow = new google.maps.InfoWindow({
                      content: contentString
                    });



                    marker = new google.maps.Marker({
                      icon: pin,
                      position: new google.maps.LatLng(childData[x].l[0], childData[x].l[1]),
                      map: map,
                      title: 'Hello World!'
                    });

                     google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                          infowindow.setContent(contentString);
                          infowindow.open(map, marker);
                        }
                      })(marker, i));

                    /*marker.addListener('click', function() {
                      infowindow.open(map, marker);
                    }); 
                    i++;
                  }
                  
                });
            });

           /* var contentString = '<p>Driver Name : Sarath</p><p>Vehicle Number : 12456</p><p>Status : <span style="color:green;">On Trip</span></p>';

            var infowindow = new google.maps.InfoWindow({
              content: contentString
            });


            var marker = new google.maps.Marker({
              position: myLatLng,
              map: map,
              title: 'Hello World!'
            });

            marker.addListener('click', function() {
              infowindow.open(map, marker);
            });
          }*/


          firebase.initializeApp(config);

        function initMap() {
    var locations = [
      @foreach($latlon as $k=>$d) 
        [{{$d['status']}},"{{ $d['driver_name'] }}",{{ $d['driver_current_lat'] }},{{ $d['driver_current_lon'] }}],
      @endforeach
      ];


           var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

             var black_icon = {
                          path: car,
                          scale: .7,
                          strokeColor: 'white',
                          strokeWeight: .10,
                          fillOpacity: 1,
                          fillColor: "#000000",
                          offset: '5%',
                          scaledSize: [25, 25]
                        }

                var green_icon = {
                    path: car,
                    scale: .7,
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: "#008000",
                    offset: '5%',
                    scaledSize: [25, 25]
                    }      


      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 2 ,
        center: new google.maps.LatLng(0,0),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });

      var infowindow = new google.maps.InfoWindow();

      var marker, i;

      

      for (i = 0; i < locations.length; i++) {  

        if(locations[i][0]==0){
          var pin = black_icon;
        } else {
         var pin = green_icon;
        }

        marker = new google.maps.Marker({
          position: new google.maps.LatLng(locations[i][2], locations[i][3]),
          map: map,
          icon: pin
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
          return function() {
            infowindow.setContent(locations[i][1]);
            infowindow.open(map, marker);
          }
        })(marker, i));
      } 
      
}


        </script>
        <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}&callback=initMap">
        </script>
        <!-- Firebase -->
        
        <script>
             //Connection to your firebase database
             /*firebase.initializeApp({
                 apiKey: "AIzaSyDgmp1xJHJU5T7GZDGhn6rHcoIherLH1IM",
                 databaseURL: "https://firgun-uplogic-demo.firebaseio.com"
             });
             
            var leadsRef = firebase.database().ref('drivers_location');
            leadsRef.on('value', function(snapshot) {
                snapshot.forEach(function(childSnapshot) {
                  var childData = childSnapshot.val();
                  for(var x in childData){
                    //alert(JSON.stringify(childData[x].l[0]));
                    var contentString = '<p>Driver Name : Sarath</p><p>Vehicle Number : 12456</p><p>Status : <span style="color:green;">On Trip</span></p>';

                    var infowindow = new google.maps.InfoWindow({
                      content: contentString
                    });


                    var marker[x] = new google.maps.Marker({
                      position: myLatLng,
                      map: map,
                      title: 'Hello World!'
                    });

                    marker[x].addListener('click', function() {
                      infowindow.open(map, marker[x]);
                    });
                  }
                  
                });
            });*/
            </script>
 