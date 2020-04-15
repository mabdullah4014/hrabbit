
     <style type="text/css">
     
      .autocomplete-suggestions { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
      .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
      .autocomplete-no-suggestion { padding: 2px 5px;}
      .autocomplete-selected { background: #F0F0F0; }
      .autocomplete-suggestions strong { font-weight: bold; color: #000; }
      .autocomplete-group { padding: 2px 5px; font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }

      
      
      hr {
        margin: 5px 0 !important; 
      }

     /* .navbar-nav > li > a {padding-top:5px !important; padding-bottom:5px !important;}
      .navbar {min-height:32px !important}
      .table-condensed>thead>tr>th, .table-condensed>tbody>tr>th, .table-condensed>tfoot>tr>th, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td{
          padding: 1px;

      }*/
      
      .navbar-nav > li > a {padding-top:5px !important; padding-bottom:5px !important;}
      .navbar {min-height:32px !important}
      .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td
      {
        border: 1px solid #ddd;
      }
      /*body {
          overflow:hidden;
      }*/

      #trips {
        overflow: auto;
        height: 200px;
        /*margin-top:10px;*/
      }

      .datalist {
         height:50px !important;
         max-height:80px !important;
         overflow-y:auto;
         display:block !important;
      }

      .pac-container {
          z-index: 10000 !important;
      }
      .modal-content{
        max-height: 76vh;
      }
      .modal.in .modal-dialog {
          position:fixed;
          width:400px;
          right:0px;
          margin:0px;
          max-height: 680px;
      }
      #map {
        height: 65vh;
        width: 100%;
        background-color: #CCC;
      }
      
      html, body {
          height: 100%;
          margin: 0;
          padding: 0;
      }

      #over_map {
          position: absolute;
          top: 25px;
          left: 10;
          z-index: 99;
          background-color: #FFFFFF;
          padding: 10px;
          margin-top: 6%;
          margin-left: 1%;
          box-shadow: 0px 2px 10px 3px #aaaaaa;
      }

      

      .grid-container {
        display: grid;
        grid-template-columns: auto auto;
      }

      .my-error-class {
          color: red;
      }
      .modal, .modal-backdrop{
        background-color: transparent !important;
      }
      .content-header{
        display: none;
      }
      .navbar{
        margin-bottom: 0px !important;
      }
      ::-webkit-scrollbar {
    width: 0px;  /* remove scrollbar space */
    background: transparent;  /* optional: just make scrollbar invisible */
    display: none; 
}
/* optional: show position indicator in red */
::-webkit-scrollbar-thumb {
    background: transparent;
    display: none; 
}

#cover-spin {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(0,0,0,0.7);
    z-index:9999;
    display:none;
}

@-webkit-keyframes spin {
  from {-webkit-transform:rotate(0deg);}
  to {-webkit-transform:rotate(360deg);}
}

@keyframes spin {
  from {transform:rotate(0deg);}
  to {transform:rotate(360deg);}
}

#cover-spin::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:#3498db;
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .8s linear infinite;
    animation: spin .8s linear infinite;
}

  .spinner {
    width: 70px;
    text-align: center;
  }

  .spinner > div {
    width: 18px;
    height: 18px;
    background-color: #3498db;

    border-radius: 100%;
    display: inline-block;
    -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
    animation: sk-bouncedelay 1.4s infinite ease-in-out both;
  }

  .spinner .bounce1 {
    -webkit-animation-delay: -0.32s;
    animation-delay: -0.32s;
  }

  .spinner .bounce2 {
    -webkit-animation-delay: -0.16s;
    animation-delay: -0.16s;
  }
  @media (max-width:767px) {
   #myDiv.fullscreen{
        z-index: 9999;
    width: auto;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: #fff;
    /*margin-left: 230px; */
 }
 #booking_content {
          position: absolute;
          top: 0;
          right: 0;
          z-index: 99;
          background-color: #FFFFFF;
          padding: 10px;
          width:100%;
          height: 68vh;
          overflow:scroll;
          box-shadow: 0px 0px 5px 2px #aaaaaa;
      }
}
@media (min-width:768px) {
#myDiv.fullscreen{
        z-index: 9999;
    width: auto;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: #fff;
    margin-left: 230px;
 }
 #booking_content {
          position: absolute;
          top: 0;
          right: 0;
          z-index: 99;
          background-color: #FFFFFF;
          padding: 10px;
          width:40%;
          height: 68vh;
          overflow:scroll;
          box-shadow: 0px 0px 5px 2px #aaaaaa;
      }

  }

  @-webkit-keyframes sk-bouncedelay {
    0%, 80%, 100% { -webkit-transform: scale(0) }
    40% { -webkit-transform: scale(1.0) }
  }

  @keyframes sk-bouncedelay {
    0%, 80%, 100% { 
      -webkit-transform: scale(0);
      transform: scale(0);
    } 40% { 
      -webkit-transform: scale(1.0);
      transform: scale(1.0);
    }
  }
  .padd-none{
    padding: 0;
  }
  .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
    color: #fff !important;
    background-color: #3c8dbc !important;
  }
  </style>

    
    <div id="cover-spin"></div>
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <ul class="nav navbar-nav">
          <!--<li><a href="#" data-toggle="modal" data-target="#myModal">Create Booking</a></li>-->
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Drivers
            <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#" onclick="reset();">All</a></li>
              <li><a href="#" onclick="load_drivers(1);">Online</a></li>
              <li><a href="#" onclick="load_drivers(2);">On-Trip</a></li>
              <li><a href="#" onclick="load_drivers(0);">Offline</a></li>
            </ul>
          </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <!--<li><a href="#" data-toggle="modal" data-target="#myModal" style="color:white;background-color: #367ea9;border-radius: 5px;"><span class="glyphicon glyphicon-plus"></span> Create Booking</a></li>-->
          <li><a href="#" onclick="open_booking_conetent();" style="color:white;background-color: #367ea9;border-radius: 5px;text-align: right;"><span class="glyphicon glyphicon-plus"></span> Create Booking</a></li>

        </ul>
      </div>
    </nav>
    <div id="map"></div>
    <div id="over_map">
          <div>
              <span>Total Cars: </span><span id="cars">0</span>
          </div>
          
          <div class="grid-container">
                     
            <div class="grid-item" style="background-color:#33cc33;height:15px;width:15px;margin-top:20%;margin-right: 5px;"></div>
            <div class="grid-item">Available (<span id="online">0</span>) </div>            
            <div class="grid-item" style="background-color:#cc3300;height:15px;width:15px;margin-top:20%;margin-right: 5px;"></div>
            <div class="grid-item">OnTrip (<span id="ontrip">0</span>) </div>
            <div class="grid-item" style="background-color:#000000;height:15px;width:15px;margin-top:20%;margin-right: 5px;"></div>
            <div class="grid-item">Offline (<span id="offline">0</span>) </div>
          </div>
        
      </div>
      <div id="booking_content" style="display: none;">
          <form id="form-1" method="post" action="javascript:void(0);" autocomplete="off" novalidate="novalidate">
            <div class="col-md-11 col-xs-11 padd-none">
              <h4 style="margin-top: 0px !important;"><b>Customer Details</b></h4>
              <hr><br><br>
            </div>
            <div class="col-md-1 col-xs-1 padd-none">
              <a href="#" style="color: #dd4b39;"onclick="close_booking_conetent();"><i class="fa fa-times-circle-o fa-lg" aria-hidden="true"></i></a>
            </div>
            <hr>
            <div class="col-md-6" style="margin-top:20px">
                <div class="form-group" onblur="checkuser(this.value);">
                  <label for="pwd">Phone *</label>
                  <input class="form-control" type="text" name="phone_number" id="phone_number" autocomplete="off" required="required" onkeyup="checkuser(this.value);"/>
                  <input type="hidden" name="phone_sug" id="phone_sug" value="" placeholder="enter number with country code">
                  <input type="hidden" name="booking_id" id="booking_id" value="0">
                  <span id="errmsg" class="my-error-class"></span>
                </div>
                <div class="form-group">
                  <label for="email">Customer Name *</label>
                  <input type="text" placeholder="Enter first and last name" class="form-control" id="customer_name" name="customer_name" required="required">
                  <!-- <input type="text" placeholder="Enter first and last name" class="form-control" id="customer_name" name="customer_name" onclick="customnam();" required="required"> -->
                   <input type="hidden" class="form-control" id="customer_id">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email *</label>
                  <input autocomplete="off" type="email" onclick="load_customer();" placeholder="Email" class="form-control" id="customer_email" required="required">
                </div>
                <div class="form-group">
                  <label for="email">Choose Vehicle Type *</label>
                  <select name="vehicle_id" id="vehicle_id" onchange="fare_load();" class="form-control" required="required">
                     <option value=""></option>
                    @foreach($data['vehicle'] as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                  </select>
                </div>  
              </div>
              
            <div class="col-md-12">
              <h4><b>Ride Details</b></h4>
              <hr>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                  <label for="email">From Location *</label>
                  <input type="text" placeholder="From Location" class="form-control" id="from_location" name="from_location" required="required" onfocusout="calculate_estimate();" onchange="calculate_estimate();">
                  <input type="hidden" value="" class="form-control" id="pickup_lat">
                  <input type="hidden" value="" class="form-control" id="pickup_lon" >
              </div>

              <div class="form-group">
                  <label for="pwd">To Location *</label>
                  <input type="text" placeholder="To Location" class="form-control" id="to_location" name="to_location" onfocusout="calculate_estimate();" required="required" onclick="calculate_estimate();" onchange="calculate_estimate();">
                  <input type="hidden" value="" class="form-control" id="drop_lat">
                  <input type="hidden" value="" class="form-control" id="drop_lon">
                  <input type="hidden" value="{{ Admin::user()->id }}" class="form-control" id="added_by">
              </div>
             </div>
             <div class="col-md-6">
              <div class="form-group">
                <label for="pwd">Service Type *</label>
                <!-- booking_type -->
                <select class="form-control" onchange="booking_type_onchage(this.value);" style="width: 100%;" id="booking_type" name="booking_type" data-value="">
                  <option value=""></option>
                  <option value="ridenow">Ride Now</option>
                  <option value="ridelater">Ride Later</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" id="seldate">
                  <label for="email">Pickup Date & Time</label>
                  <input type="text" placeholder="pickup date and time" class="form-control datepicker" id="datetime" required="required">
              </div>  
            </div>

            
            <div class="col-md-6">

              <div class="spinner" style="display:none;">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
              </div>

                <div class="form-group" id="driverd" style="display:none;">
                  <label for="driver">Choose Driver</label>
                  <select class="form-control" id="driver">
                  </select>
                  <span id="showerr" style="display: none;color:red;font-weight: bold;">Choose a Driver</span>
                </div>
            </div>
            <div class="col-md-12">
            <div class="col-md-6">
              <div class="form-group">
                  <label for="driver">Auto Accept</label>
                  <input class="icheckbox_flat-blue" type="checkbox" name="assign" id="assign" value="1" checked>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                  <label for="driver">Send OTP to Customer</label>
                  <input class="icheckbox_flat-blue" type="checkbox" name="send_OTP" id="send_OTP" value="0" >
              </div>
            </div>
          </div>
            <div class="col-md-12" id="estimate_block" style="display: none;">
              <div class="col-md-12 padd-none">
                  <h4><b>Fare Estimate</b></h4>
                <hr>
                <div class="col-md-2 padd-none">
                  <label><b>Fare:</b></label>
                </div>
                <div class="col-md-10 padd-none">
                  <p id="fare"></p>
                </div>
              </div>
              <div class="col-md-12 padd-none">
                <div class="col-md-2 padd-none">
                  <label><b>Distance:</b></label>
                </div>
                <div class="col-md-10 padd-none">
                  <p id="distance"></p>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="margin-top: 10px;">
              <center>
                <!-- onSubmit="create_booking();" -->
                <button  type="submit" class="btn btn-primary" id="dis_button">Submit</button>
                <button type="reset" id="reset_buton" class="btn btn-danger">Reset</button>
              </center>
            </div>
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

            <div class="col-md-12">
              <a href="" style="margin-top:10px;float: right;" >Refresh the page</a>
            </div>
          </form>
    </div>
    <div class="col-lg-12" style="background-color: #fff;">
      <div id="myDiv">
      <div class="col-md-12">
        <div id="trips" >
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#unassigned">Unassigned</a></li>
            <li><a data-toggle="tab" href="#active">Active</a></li>
            <li><a data-toggle="tab" href="#completed">Completed</a></li>
            <button style="float: right; border: 0px;margin-top: 10px;background-color: transparent;" id="expand"><i class="fa fa-desktop" aria-hidden="true">Full Screen</i></button>
          </ul>
         
                  <div class="tab-content">
                    <div id="unassigned" class="tab-pane fade in active">
                        <table class="table table-condensed table-bordered" id="live_data" style="border: 1px solid #ddd;margin-bottom: 0px !important;">
                          <thead><tr><th>Id</th><th>Vehicle Type</th><th>Customer</th><th>CustomerPhone</th><th>Pickup Location</th><th>Drop Location</th></tr></thead>
                          <tbody>
                            
                          </tbody>
                        </table>
                    </div>
                    <!-- <div id="assigned" class="tab-pane fade">
                      <h3>Menu 1</h3>
                      <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    </div> -->
                    <div id="active" class="tab-pane fade">
                      <table class="table table-condensed table-bordered" id="active_data" style="border: 1px solid #ddd">
                          <thead><tr><th>Id</th><th>CustomerID</th><th>CustomerName</th><th>CustomerPhone</th><th>DriverID</th><th>DriverName</th><th>DriverPhone</th><th>PickupLocation</th><th>DropLocation</th><th>Status</th></tr></thead>
                          <tbody>
                            
                          </tbody>
                        </table>
                    </div>
                    <div id="completed" class="tab-pane fade">
                      <table class="table table-condensed table-bordered" id="complete_data" style="border: 1px solid #ddd">
                          <thead><tr><th>Id</th><th>CustomerID</th><th>CustomerName</th><th>CustomerPhone</th><th>DriverID</th><th>DriverName</th><th>DriverPhone</th><th>PickupLocation</th><th>DropLocation</th><th>Status</th></tr></thead>
                          <tbody>

                          </tbody>
                        </table>
                    </div>
                  </div>

         
        </div>
      </div>

    </div>
      <!-- jQuery CDN -->
     <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>  -->

    <!-- Firebase -->
    <script src="https://www.gstatic.com/firebasejs/3.7.0/firebase.js"></script>
    <script type="text/javascript">
              $(document).ready(function(){  
                    $('#expand').click(function(e){
                        $('#myDiv').toggleClass('fullscreen'); 
                        $("#trips").css("height", "100vh");
                    });  
                    
                  //Check if the current URL contains '#'
                  if(document.URL.indexOf("#")==-1){
                      // Set the URL to whatever it was plus "#".
                      url = document.URL+"#";
                      location = "#";

                      //Reload the page
                      location.reload(true);
                  }
                  
              });
              setTimeout(function(){ document.getElementById('booking_content').style.display='block'; }, 5000);
              //setTimeout(document.getElementById('booking_content').style.display='block', 5000);
              var url_string = window.location.href ; //window.location.href
              var url = new URL(url_string);
              var driver_filter = url.searchParams.get("id");
              
              function open_booking_conetent(){
                document.getElementById('booking_content').style.display='block';
              }

              function close_booking_conetent(){
                document.getElementById('booking_content').style.display='none';
              }
    </script>
    <!-- GeoFire -->
    <script src="https://cdn.firebase.com/libs/geofire/4.1.2/geofire.min.js"></script>
        <script>

          var config = {
            apiKey: "{{env('GOOGLE_MAP_API_KEY')}}",
            authDomain: "{{env('FIREBASE_AUTHDOMAIN')}}",
            databaseURL: "{{env('FIREBASE_DB')}}",
            projectId: "{{env('FIREBASE_PROJECT_ID')}}",
            storageBucket: "{{env('FIREBASE_STORAGE_BUCKET')}}",
            messagingSenderId: "{{env('FIREBASE_SENDER_ID')}}"
          };
          firebase.initializeApp(config);
                
            // counter for online cars...
            var cars_count = 0;
            var online = 0;
            var ontrip = 0;
            var offline = 0;

            // markers array to store all the markers, so that we could remove marker when any car goes offline and its data will be remove from realtime database...
            var markers = [];
            var map;
            const myLatLng = {lat: 9.921374, lng: 78.092214};
            function initMap() { // Google Map Initialization... 
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 18,
                    center: myLatLng,
                    mapTypeId: 'terrain',

                    fullscreenControl: true,
                    fullscreenControlOptions: {
                    position: google.maps.ControlPosition.LEFT_BOTTOM
                    },
                    zoomControl:true,
                    zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_BOTTOM
                    },
                    streetViewControl: true,
                    streetViewControlOptions: {
                        position: google.maps.ControlPosition.LEFT_BOTTOM
                    },
                   
                });
                google.maps.event.addDomListener(window, 'load', initialize);
            }

            function initialize() {
              var input = document.getElementById('from_location');
              
              autocomplete = new google.maps.places.Autocomplete(input);
              google.maps.event.addListener(autocomplete, 'place_changed', function(e) {
                var place = autocomplete.getPlace();

              if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                return;
              }

              var request = get_lat_and_long(place.place_id);
                $('#pickup_lat').val(request.lat);
                $('#pickup_lon').val(request.lng);
              });
              
              var input1 = document.getElementById('to_location');
              autocomplete1 = new google.maps.places.Autocomplete(input1);
              google.maps.event.addListener(autocomplete1, 'place_changed', function(e) {
                var place1 = autocomplete1.getPlace();
                if (!place1.geometry) {
                  window.alert("No details available for input: '" + place1.name + "'");
                  return;
                }
                var request1 = get_lat_and_long(place1.place_id);
                $('#drop_lat').val(request1.lat);
                $('#drop_lon').val(request1.lng);
              });
            }

            // This Function will create a car icon with angle and add/display that marker on the map
            function AddCar(data) {
                
                var car_color = "";
                var status = "";
                var name = "";
                var driver_id = data.key;
                
                if(data.val().status == 1){
                    car_color = "#33cc33";
                    status = "Available";
                }else if(data.val().status == 2){
                    car_color = "#cc3300";
                    status = "On Trip";
                }else{
                    car_color = "#000000";
                    status = "Offline";
                }

                var icon = { // car icon
                    path: 'M29.395,0H17.636c-3.117,0-5.643,3.467-5.643,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759   c3.116,0,5.644-2.527,5.644-5.644V6.584C35.037,3.467,32.511,0,29.395,0z M34.05,14.188v11.665l-2.729,0.351v-4.806L34.05,14.188z    M32.618,10.773c-1.016,3.9-2.219,8.51-2.219,8.51H16.631l-2.222-8.51C14.41,10.773,23.293,7.755,32.618,10.773z M15.741,21.713   v4.492l-2.73-0.349V14.502L15.741,21.713z M13.011,37.938V27.579l2.73,0.343v8.196L13.011,37.938z M14.568,40.886l2.218-3.336   h13.771l2.219,3.336H14.568z M31.321,35.805v-7.872l2.729-0.355v10.048L31.321,35.805',
                    scale: 0.6,
                    fillColor: car_color, //<-- Car Color, you can change it 
                    fillOpacity: 1,
                    strokeWeight: 1,
                    anchor: new google.maps.Point(0, 5),
                    rotation: data.val().bearing //<-- Car angle
                };

                var uluru = { lat: data.val().l[0], lng: data.val().l[1] };
                if(driver_filter == null || driver_filter == data.val().status){
                  var marker = new google.maps.Marker({
                      position: uluru,
                      icon: icon,
                      map: map
                  });
                }

                var contentString = '<div id="content" style="width:200px;">'+
                    '<p>Driver Name: '+data.val().driverFName+'</p>'+'<p>Driver Id: '+driver_id+'</p>'+'<p>Status: '+status+'</p>';

                if(data.val().status == 2){
                    contentString += '<p>Departure: '+data.val().trips.dropLoc+'</p>' + '<p>Destination: '+data.val().trips.pickupLoc+'</p>';
                }

                contentString += '</div>';
                var infowindow = new google.maps.InfoWindow({
                  content: contentString
                });

                marker.addListener('click', function() {
                  infowindow.open(map, marker);
                });

                markers[data.key] = marker; // add marker in the markers array...
                document.getElementById("cars").innerHTML = cars_count;
                document.getElementById("online").innerHTML = online;
                document.getElementById("ontrip").innerHTML = ontrip;
                document.getElementById("offline").innerHTML = offline;
            }

            // get firebase database reference...
            var cars_Ref = firebase.database().ref('/drivers_location');

            // this event will be triggered when a new object will be added in the database...
            /*cars_Ref.on('child_added', function (data) {
                alert(data.key);
                cars_count++;
                AddCar(data);
            });*/
            cars_Ref.on('child_added', function (data1) {
                var child_data = firebase.database().ref('/drivers_location/'+data1.key);
                child_data.on('child_added', function (data) {
                    cars_count++;
                    if(data.val().status == 1){
                          online++;
                      }else if(data.val().status == 2){
                          ontrip++;
                      }else{
                          offline++;
                      }
                    AddCar(data);
                });
            });

            // this event will be triggered on location change of any car...
            cars_Ref.on('child_changed', function (data1) {
                var child_data = firebase.database().ref('/drivers_location/'+data1.key);
                child_data.on('child_changed', function (data) {
                    markers[data.key].setMap(null);
                    AddCar(data);
                });
            });

            // If any car goes offline then this event will get triggered and we'll remove the marker of that car...  
            cars_Ref.on('child_removed', function (data1) {
                var child_data = firebase.database().ref('/drivers_location/'+data1.key);
                child_data.on('child_changed', function (data) {
                    markers[data.key].setMap(null);
                    cars_count--;
                    document.getElementById("cars").innerHTML = cars_count;
                });
                
            });

        </script>
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create New Booking</h4>
        </div> -->
        <div class="modal-body" style="height: 76vh;overflow: hidden;width: 100%;overflow-y: scroll;background-color: #ecf0f58f;">
         <!--<form id="form-1" method="post" action="javascript:void(0);" autocomplete="off" novalidate="novalidate">
            <div class="col-md-12">
              <h4 style="margin-top: 0px !important;"><b>Customer Details</b></h4>
              <hr>
            </div>
            <div class="col-md-6">
                <div class="form-group" onblur="checkuser(this.value);">
                  <label for="pwd">Phone *</label>
                  <input class="form-control" type="text" name="phone_number" id="autocomplete" autocomplete="off" required="required" onkeyup="checkuser(this.value);"/>
                  <input type="hidden" name="phone_sug" id="phone_sug" value="" placeholder="enter number with country code">
                </div>
                <div class="form-group">
                  <label for="email">Customer Name *</label>
                  <input type="text" placeholder="Enter first and last name" class="form-control" id="customer_name" name="customer_name" onclick="customnam();" required="required">
                   <input type="hidden" class="form-control" id="customer_id">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email *</label>
                  <input autocomplete="off" type="email" onclick="load_customer();" placeholder="Email" class="form-control" id="customer_email" required="required">
                </div>
                <div class="form-group">
                  <label for="email">Choose Vehicle *</label>
                  <select name="vehicle_id" id="vehicle_id" class="form-control" required="required">
                     <option value=""></option>
                    @foreach($data['vehicle'] as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                  </select>
                </div>  
              </div>
              
            <div class="col-md-12">
              <h4><b>Ride Details</b></h4>
              <hr>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                  <label for="email">From Location *</label>
                  <input type="text" placeholder="From Location" class="form-control" id="from_location" required="required" onchange="calculate_estimate();">
                  <input type="hidden" value="" class="form-control" id="pickup_lat">
                  <input type="hidden" value="" class="form-control" id="pickup_lon" >
              </div>

              <div class="form-group">
                  <label for="pwd">To Location *</label>
                  <input type="text" placeholder="To Location" class="form-control" id="to_location" required="required" onchange="calculate_estimate();">
                  <input type="hidden" value="" class="form-control" id="drop_lat">
                  <input type="hidden" value="" class="form-control" id="drop_lon">
                  <input type="hidden" value="{{ Admin::user()->id }}" class="form-control" id="added_by">
              </div>
             </div>
             <div class="col-md-6">
              <div class="form-group">
                <label for="pwd">Service Type *</label>
                
                <select class="form-control" style="width: 100%;" id="booking_type" name="booking_type" data-value="">
                  <option value=""></option>
                  <option value="ridenow">Ride Now</option>
                  <option value="ridelater">Ride Later</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" id="seldate">
                  <label for="email">Pickup date & Time</label>
                  <input type="text" placeholder="pickup date and time" class="form-control datepicker" id="datetime" required="required">
              </div>  
            </div>

            <div class="col-md-6">
              <div class="form-group">
                  <label for="driver">Auto Accept</label>
                  <input class="icheckbox_flat-blue" type="checkbox" name="assign" id="assign" value="1" checked>
              </div>
            </div>
            <div class="col-md-6">

              <div class="spinner" style="display:none;">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
              </div>

                <div class="form-group" id="driverd" style="display:none;">
                  <label for="driver">Choose Driver</label>
                  <select class="form-control" id="driver">
                  </select>
                </div>
            </div>
            <div class="col-md-12" id="estimate_block" style="display: none;">
              <div class="col-md-12">
                  <h4><b>Fare Estimate</b></h4>
                <hr>
                <div class="col-md-4">
                  <label><b>Fare:</b></label>
                </div>
                <div class="col-md-8">
                  <p id="fare"></p>
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-4">
                  <label><b>Distance:</b></label>
                </div>
                <div class="col-md-8">
                  <p id="distance"></p>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="margin-top: 10px;">
              <center>
               
                <button  type="submit" class="btn btn-primary" id="dis_button">Submit</button>
                <button type="reset" id="reset_buton" class="btn btn-danger">Reset</button>
              </center>
            </div>
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

            <div class="col-md-12">
              <a href="" style="margin-top:10px;float: right;" >Refresh the page</a>
            </div>
          </form>-->
        </div>
       
      </div>
      
    </div>
  </div>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>

  <script data-exec-on-popstate>
    
    $(function () {
      $(".booking_type").select2({"allowClear":true,"placeholder":{"id":"","text":"Booking Type"}});     

    });

    function load_customer(){
      var phone = $('#phone_number').val();

      if(phone!=''){
        $.post("dispatch/load_customer",{ phone : phone, _token : $('#token').val() },function(data){
          var obj = JSON.parse(data);
          $("#customer_name").val(obj.name);
          $("#customer_email").val(obj.email);
          $("#customer_id").val(obj.id);
          $("#phone_number").val(obj.phone_number);
        });
      }  
    }
$(document).ready(function () {
  //called when key is pressed in textbox
  $("#phone_number").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});
    // function create_booking(){
      
    $("#form-1").validate({
      errorClass: "my-error-class",
      validClass: "my-valid-class",
      rules: {
          customer_name: "required",
          customer_email: {
            required: true,
            email: true
          },
          from_location: "required",
          to_location: "required",
          booking_type: "required"
      },
      messages: {
          customer_name: "Please specify your name",
          customer_email: {
            required: "We need your email address to contact you",
            email: "Your email address must be in the format of name@domain.com"
          },
          from_location: "Choose From Location",
          to_location: "Choose To Location"
      },submitHandler: function() {

        if($("#datetime").val()!=''){
          var dat = $("#datetime").val().split(" ");
          var date = dat[0];
          var pick_up_time = dat[1];
        }
        $(".spinner").show();
        //$('#cover-spin').show();
        $("#dis_button").attr('disabled',true);
        $("#reset_buton").attr('disabled',true);
        if($("#assign"). prop("checked")==true){
          var driver = $('#driver').val();
          if(driver == '' || driver == 'NULL'){
            $('#showerr').show();
            $(".spinner").hide();
            $("#dis_button").attr('disabled',false);
          $("#reset_buton").attr('disabled',false);
            
            return false;
          }else{
            $('#showerr').hide();
            $(".spinner").show();
            $("#dis_button").attr('disabled',true);
          $("#reset_buton").attr('disabled',true);
            
          }
        } else {
          var driver = 0;
        }
        if($("#send_OTP"). prop("checked")==true){
          var send_OTP = 1;
        } else {
          var send_OTP = $('#send_OTP').val();
        }


         
        var path;
        if(driver!='0' && driver!==undefined && $('#booking_type').val()!='ridelater'){
          mode = 'admin_assign';
        } else {
          mode = 'add';
        }

        $.post("../api/booking/"+mode,{phone_num : $('#phone_number').val(), customer_email : $('#customer_email').val(), customer_name : $('#customer_name').val(),  customer_id : $('#customer_id').val(), pick_up : $('#from_location').val(), pickup_lat : $('#pickup_lat').val(),pickup_lon : $('#pickup_lon').val(),drop_location : $('#to_location').val(),drop_lat : $('#drop_lat').val(),drop_lon : $('#drop_lon').val(),mode : $('#booking_type').val(), _token : $('#token').val(), vehicle_id : $("#vehicle_id").val() , added_by : $('#added_by').val(), driver_id : $("#driver").val(),send_OTP : send_OTP,date: date,pick_up_time : pick_up_time,booking_id : $("#booking_id").val()},function(data){

          //$('#cover-spin').hide();
          $(".spinner").hide();
          $("#dis_button").attr('disabled',false);
          $("#reset_buton").attr('disabled',false);

          if(data.message=='This driver is check out recently'){
            toastr.error(data.message);
          } else if(data.message=='This driver is currently on trip'){
            toastr.error(data.message);
          } else if(data.message=='Driver account is blocked,kindly contact Admin'){
            toastr.error(data.message);
          } else if(data.message=='Your account is blocked,kindly contact Admin') {
            toastr.error(data.message);
          } else {
            toastr.success('Booking Added Successfully');
            $("#phone_number").val('');
            $("#customer_email").val('');
            $("#customer_name").val('');
            $("#vehicle_id").val('');
            $("#from_location").val('');
            $("#to_location").val('');
            $("#booking_type").val('');
            $("#datetime").val('');
             
            $("#datetime").css('display','block');
            $("#driverd").css('display','none');
            $("#estimate_block").css('display','none');
          }
          
        });
      }
    });

   // }

    function checkuser(str){
      var phone = $("#phone_number").val();
      if(phone!=''){
        $.post("dispatch/load_phone",{ phone : str, _token : $('#token').val() },function(data){
          if(data!='Failure'){
            var obj = JSON.parse(data);
            $("#customer_name").val(obj.name);
            $("#customer_email").val(obj.email);
            $("#customer_id").val(obj.id);
            
          } else {
            var obj = JSON.parse(data);
            $("#customer_name").val('');
            $("#customer_email").val('');
            $("#customer_id").val('');
            $("#phone_number").val('');
          }
        });
      } else {
        $("#customer_name").val('');
        $("#customer_email").val('');
        $("#customer_id").val('');
        $("#phone_number").val('');
      }  
    }

    function customnam(str){
      var phone = $('#phone_number').val();
      $.post("dispatch/load_number",{ phone : str, _token : $('#token').val() },function(data){

        if(data!='Failure'){
          var obj = JSON.parse(data);
          $("#customer_name").val(obj.name);
          $("#customer_email").val(obj.email);
          $("#customer_id").val(obj.id);
          $("#phone_number").val(obj.phone_number);
        } else {
          $("#customer_name").val('');
          $("#customer_email").val('');
          $("#customer_id").val('');
          $("#phone_number").val('');
        }  
      });
    }

</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key={{env('GOOGLE_MAP_API_KEY')}}&callback=initMap"></script>

<script>
  load_unassigned();

  load_active();

  load_complete();

  function load_unassigned(){
   $("#live_data tbody").html('');
    var unassigned_table = '';
      var unassigned = firebase.database().ref('unassigned');
        unassigned.on('value', function(snapshot){
          unassigned_table = '';
            snapshot.forEach(function(childSnapshot){
              $("#live_data tbody tr").html('');

              unassigned_table+= '<tr><td>'+childSnapshot.val().booking_id+'</td><td>'+childSnapshot.val().vehicle_type+'</td><td>'+childSnapshot.val().customer_name+'</td><td>'+childSnapshot.val().phone_num+'</td><td>'+childSnapshot.val().pick_up+'</td><td>'+childSnapshot.val().drop_location+'</td><td><button type="button" class="btn btn-primary btn-xs" onclick="automatic_assign('+childSnapshot.val().booking_id+');" >Automatic</button></td><td><button onclick="manual_assign('+childSnapshot.val().booking_id+');" type="button" class="btn btn-success btn-xs">Manual</button></td></tr>';

            });
        $("#live_data tbody tr").html('');
        $("#live_data tbody").append(unassigned_table);
        });
  }


  function automatic_assign(id){
      $.post("../api/automatic_assign",{ booking_id : id,  _token : $('#token').val() },function(data){
           alert(data.message);
      });
  }

  function manual_assign(id){
      $.post("../api/get_booking_data",{ booking_id : id,  _token : $('#token').val() },function(data){
           document.getElementById('booking_content').style.display="block";
           var obj = JSON.parse(data);             
              /*autocomplete2 = new google.maps.places.Autocomplete(obj.pickup_location);
              google.maps.event.addListener(autocomplete2, 'place_changed', function(e) {
                var place = autocomplete2.getPlace();

              if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                return;
              }

              var request = get_lat_and_long(place.place_id);
                $('#pickup_lat').val(request.lat);
                $('#pickup_lon').val(request.lng);
              });
              
              
              autocomplete3 = new google.maps.places.Autocomplete(obj.drop_location);
              google.maps.event.addListener(autocomplete3, 'place_changed', function(e) {
                var place1 = autocomplete3.getPlace();
                if (!place1.geometry) {
                  window.alert("No details available for input: '" + place1.name + "'");
                  return;
                }
                var request1 = get_lat_and_long(place1.place_id);
                $('#drop_lat').val(request1.lat);
                $('#drop_lon').val(request1.lng);
              });*/
           $('#booking_type').val('ridenow');
           booking_type_onchage('ridenow');
           $('#pickup_lat').val(obj.pickup_lat);
           $('#pickup_lon').val(obj.pickup_lon);
           $('#drop_lat').val(obj.drop_lat);
           $('#drop_lon').val(obj.drop_lon);
           $("#booking_id").val(obj.booking_id);
           $("#phone_number").val(obj.phone_number);
           $("#customer_name").val(obj.customer_name);
           $("#customer_id").val(obj.customer_id);
           $("#customer_email").val(obj.customer_email);
           $("#vehicle_id").val(obj.vehicle_id);
           $("#from_location").val(obj.pickup_location);
           $("#to_location").val(obj.drop_location);
           setTimeout( function(){
                fare_load();
              }, 1000 );
         });
      //alert(id);
  }
  
  function load_active(){

    $("#active_data tbody").html('');
    var unassigned_table = '';
    var stats;
      var unassigned = firebase.database().ref('drivers_trips');
      unassigned.on('value', function(snapshot){
        unassigned_table = '';
          snapshot.forEach(function(childSnapshot){
            if(childSnapshot.val().BookingId!='' && childSnapshot.val().Status!='0'  && (childSnapshot.val().Status=='2' || childSnapshot.val().Status== '3' || childSnapshot.val().Status== '4')){

              if(childSnapshot.val().Status=='2'){
                stats = '<p style="color:#357321">Driver Accept Trip</p>';
              } else if(childSnapshot.val().Status=='3'){
                stats = '<p style="color:#337ab7">Trip Started</p>';
              } else if(childSnapshot.val().Status=='4'){
                stats = '<p style="color:#283b80">Trip Completed waiting for payment</p>';
              }

              $("#active_data tbody tr").html('');
              unassigned_table+= '<tr><td>'+childSnapshot.val().BookingId+'</td><td>'
              +childSnapshot.val().CustomerId+'</td><td>'
              +childSnapshot.val().CustomerName+' '+childSnapshot.val().CustomerLastName+'</td><td>'
              +childSnapshot.val().PhoneNumber+'</td><td>'
              +childSnapshot.key+'</td><td>'
              +childSnapshot.val().DriverName+' '+childSnapshot.val().DriverLastName+'</td><td>'
              +childSnapshot.val().DriverPhoneNumber+'</td><td>'
              +decodeURIComponent(childSnapshot.val().PickupLocation)+'</td><td>'
              +decodeURIComponent(childSnapshot.val().DropLocation)+'</td><td>'
              +stats+'</td></tr>';
            }   
          });
      $("#active_data tbody tr").html('');
      $("#active_data tbody").append(unassigned_table);
    });
  }

  function load_complete(){

    $("#complete_data tbody").html('');
    var unassigned_table = '';
    var stats;
      var unassigned = firebase.database().ref('drivers_trips');
      unassigned.on('value', function(snapshot){
        unassigned_table = '';
          snapshot.forEach(function(childSnapshot){

            if(childSnapshot.val().Status=='6'){

              stats = '<p style="color:#283b80">Trip Completed</p>';
              
              $("#complete_data tbody tr").html('');
              unassigned_table+= '<tr><td>'+childSnapshot.val().BookingId+'</td><td>'
              +childSnapshot.val().CustomerId+'</td><td>'
              +childSnapshot.val().CustomerName+' '+childSnapshot.val().CustomerLastName+'</td><td>'
              +childSnapshot.val().PhoneNumber+'</td><td>'
              +childSnapshot.key+'</td><td>'
              +childSnapshot.val().DriverName+' '+childSnapshot.val().DriverLastName+'</td><td>'
              +childSnapshot.val().DriverPhoneNumber+'</td><td>'
              +decodeURIComponent(childSnapshot.val().PickupLocation)+'</td><td>'
              +decodeURIComponent(childSnapshot.val().DropLocation)+'</td><td>'
              +stats+'</td></tr>';
            }   
          });
      $("#complete_data tbody tr").html('');
      $("#complete_data tbody").append(unassigned_table);
    });

  }



  function calculate_estimate(){

    var timer;
    var delay = 2000;
    window.clearTimeout(timer);
    timer = window.setTimeout(function(){
    from = document.getElementById('from_location').value;
    to = document.getElementById('to_location').value;        
    var flag;
    if(from=='' || to==''){
      flag = 1;
      $('#estimate_block').css('display','none');
    } else {
      flag = 0;
      $('#estimate_block').css('display','block');
    }
      
    if(flag==0){
      var plat = $('#pickup_lat').val();
      var plon = $('#pickup_lon').val();
      var dlat = $('#drop_lat').val();
      var dlon = $('#drop_lon').val();
      if(plat!='' && plon!='' && dlat!='' && dlon!=''){
        $.post("../api/calculate_estimate",{ from_lat : plat , from_lng : plon, to_lat : dlat, to_lng : dlon, _token : $('#token').val(), vehicle_id:document.getElementById('vehicle_id').value },function(data){
           result = JSON.parse(data);
           document.getElementById('estimate_block').style.display='block';
           document.getElementById('fare').innerHTML = '$'+result.fare;
           document.getElementById('distance').innerHTML = result.distance;
        });
      }  
    }
   }, delay);
  }

  function get_lat_and_long(address) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", 'https://maps.googleapis.com/maps/api/geocode/json?place_id='+address+'&key={{env('GOOGLE_MAP_API_KEY')}}', false ); 
    xmlHttp.send( null );

    response = JSON.parse(xmlHttp.responseText);

    return response.results[0].geometry.location;
  }

$(document).ready(function(){
  $('.datepicker').datetimepicker({"format":"YYYY-MM-DD HH:mm","minDate":"now"});
    $("#assign").on('click',function(e){
      if($(this). prop("checked")==false){
        $('#driver').val('');
        $('#driverd').css('display','none');
      } else {
        $('#driverd').css('display','block');
      }
  });

  /*$("#booking_type").on('change',function(e){
      var str = $(this).val();
      if(str=='ridenow'){
        $(".spinner").show();
        $("#seldate").css('display','none');
         var pickup_lat = $('#pickup_lat').val();
         var pickup_lon = $('#pickup_lon').val();
         var drop_lat = $('#drop_lat').val();
         var drop_lon = $('#drop_lon').val();

        $.post("dispatch/drivercheck",{ customer_id : $('#customer_id').val(),_token : $('#token').val(), vehicle_id : $('#vehicle_id').val(),pickup_lat:pickup_lat,pickup_lon:pickup_lon,drop_lat:drop_lat,drop_lon:drop_lon},function(data){

          $(".spinner").hide();

          if(data!='error'){
            $('#driver').html(data);

            if($("#assign"). prop("checked")==true){
              $('#driverd').css('display','block');
            } else {
              $('#driverd').css('display','block');
            }  
          } else {
            $('#driverd').css('display','none');    
          }
        });
      } else {

        $("#seldate").css('display','block');
        $('#driverd').css('display','none');
      }

   });*/

  /* $("#vehicle_id").on('click',function(e){

      var from_loc = $("#from_location").val();
      var to_loc = $("#to_location").val();

      if(from_loc!='' && to_loc!=''){

        var book_type = $("#booking_type").val();
        
        var pickup_lat = $('#pickup_lat').val();
        var pickup_lon = $('#pickup_lon').val();
        var drop_lat = $('#drop_lat').val();
        var drop_lon = $('#drop_lon').val();
        
        if(book_type=='ridenow'){ 
          $(".spinner").show();
          $('#driverd').css('display','none');
          $.post("dispatch/drivercheck",{ customer_id : $('#customer_id').val(),_token : $('#token').val(), vehicle_id : $('#vehicle_id').val(),pickup_lat:pickup_lat,pickup_lon:pickup_lon,drop_lat:drop_lat,drop_lon:drop_lon},function(data){

              $(".spinner").hide();

              if(data!='error'){
                $('#driver').html(data);
                if($("#assign"). prop("checked")==true){
                  $('#driverd').css('display','block');
                } else {
                  $('#driverd').css('display','block');
                }  
              } else {
                $('#driverd').css('display','none');    
              }
          });
        }

        $.post("../api/calculate_estimate",{ from_lat : pickup_lat, from_lng : pickup_lon, to_lat : drop_lat, to_lng : drop_lon , _token : $('#token').val(), vehicle_id:$('#vehicle_id').val() },function(data){
            result = JSON.parse(data);
            $('#estimate_block').css('display','block');
            $('#fare').html('<b>$</b>'+result.fare);
            $('#distance').html(result.distance);
        });

      } else {
        $("#estimate_block").css('display','none');
      }

   });*/

});

  function booking_type_onchage(val){
    //alert('hi');
    var str = val;
      if(str=='ridenow'){
        $(".spinner").show();
        $("#seldate").css('display','none');
         var pickup_lat = $('#pickup_lat').val();
         var pickup_lon = $('#pickup_lon').val();
         var drop_lat = $('#drop_lat').val();
         var drop_lon = $('#drop_lon').val();

        $.post("dispatch/drivercheck",{ customer_id : $('#customer_id').val(),_token : $('#token').val(), vehicle_id : $('#vehicle_id').val(),pickup_lat:pickup_lat,pickup_lon:pickup_lon,drop_lat:drop_lat,drop_lon:drop_lon},function(data){

          $(".spinner").hide();

          if(data!='error'){
            $('#driver').html(data);

            if($("#assign"). prop("checked")==true){
              $('#driverd').css('display','block');
            } else {
              $('#driverd').css('display','none');
            }  
          } else {
            $('#driverd').css('display','none');    
          }
        });
      } else {

        $("#seldate").css('display','block');
        $('#driverd').css('display','none');
      }
  }

function fare_load(){

      var from_loc = $("#from_location").val();
      var to_loc = $("#to_location").val();

      if(from_loc!='' && to_loc!=''){

        var book_type = $("#booking_type").val();
        
        var pickup_lat = $('#pickup_lat').val();
        var pickup_lon = $('#pickup_lon').val();
        var drop_lat = $('#drop_lat').val();
        var drop_lon = $('#drop_lon').val();
        //alert(pickup_lat);
        if(book_type=='ridenow'){ 
          $(".spinner").show();
          $('#driverd').css('display','none');
          $.post("dispatch/drivercheck",{ customer_id : $('#customer_id').val(),_token : $('#token').val(), vehicle_id : $('#vehicle_id').val(),pickup_lat:pickup_lat,pickup_lon:pickup_lon,drop_lat:drop_lat,drop_lon:drop_lon},function(data){

              $(".spinner").hide();

              if(data!='error'){
                $('#driver').html(data);
                if($("#assign"). prop("checked")==true){
                  $('#driverd').css('display','block');
                } else {
                  $('#driverd').css('display','block');
                }  
              } else {
                $('#driverd').css('display','none');    
              }
          });
        }
        $.post("../api/calculate_estimate",{ from_lat : pickup_lat, from_lng : pickup_lon, to_lat : drop_lat, to_lng : drop_lon , _token : $('#token').val(), vehicle_id:$('#vehicle_id').val() },function(data){
            result = JSON.parse(data);
            $('#estimate_block').css('display','block');
            $('#fare').html('<b>$</b>'+result.fare);
            $('#distance').html(result.distance);
        });

      } else {
        $("#estimate_block").css('display','none');
      }

}

function load_drivers(val){
  url = location.protocol + '//' + location.host + location.pathname+"?id="+val+"#";
  window.location.href = url;
}

function reset(){
  url = location.protocol + '//' + location.host + location.pathname+"#";
  window.location.href = url;
}
</script>
    
    <script type="text/javascript" src="{{asset('js/jquery.autocomplete.js')}}"></script>   
    <script type="text/javascript" src="{{asset('js/demo.js')}}"></script>
</div>