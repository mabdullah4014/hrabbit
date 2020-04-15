
<div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-navy">
            <div class="inner">
              <h3>{{$driverTotal}}</h3>

              <p>Total Drivers</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="{{ env('APP_URL').'/admin/drivers'}}" class="small-box-footer mainhead"  data-id="{{admin_base_path('drivers')}}">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-olive">
            <div class="inner">
              <h3>{{$driversCheckedin}}
              <!--<sup style="font-size: 20px">%</sup> !-->
              </h3>

              <p>Drivers Checkedin</p>
            </div>
            <div class="icon">
              <i class="fa fa-car"></i>
            </div>
            <a href="{{ env('APP_URL').'/admin/driverschekin'}}" class="small-box-footer mainhead" data-id="{{admin_base_path('driverschekin')}}">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-maroon">
            <div class="inner">
              <h3>{{$customerTotal}}</h3>

              <p>Total Customers</p>
            </div>
            <div class="icon">
              <i class="fa fa-group"></i>
            </div>
            <a href="{{ env('APP_URL').'/admin/customers'}}" class="small-box-footer mainhead"  data-id="{{admin_base_path('customers')}}">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-purple">
            <div class="inner">
              <h3>{{$tripCount}}</h3>

              <p>Completed Trips</p>
            </div>
            <div class="icon">
              <i class="fa fa-car"></i>
            </div>
            <a href="{{ env('APP_URL').'/admin/completed'}}" class="small-box-footer mainhead" data-id="{{admin_base_path('completed')}}">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <div class="col-sm-12">
      <div class="col-sm-6">
        <!-- AREA CHART -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Drivers Growth</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body">
                  <div class="chart">
                    <canvas id="areaChart" style="height: 250px; width: 512px;" width="512" height="250"></canvas>
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
        </div>
        <div class="col-sm-6">
         <div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title">Drivers</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body">
                  <canvas id="pieChart" style="height: 266px; width: 532px;" width="532" height="266"></canvas>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
        </div>
        </div>





<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script>
$(function () {

    $('.mainhead').on('click', function() {
      $('a').parents('li,ul').removeClass('active');
      $('a[href="' + $(this).attr('data-id') + '"]').parents('li,ul').addClass('active');
    });

    var ctx = document.getElementById("areaChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "June","July","Aug","Sep","Oct","Nov","Dec"],
        datasets: [
            {
                label: 'Driver Growth',
                data: [{{ $dgrowth }}],
            },
            {
                label: 'User Growth',
                data: [ {{ $cgrowth }} ],
            }
        ],
    },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });



    var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
    var myChart = new Chart(pieChartCanvas, {
        type: 'doughnut',
        data : {
            datasets: [{
                data: [{{ $offlinedrivers }}, {{ $driversCheckedin }}, {{ $driverTotal  }}],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                ]
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: [
                'Checked-Out',
                'Checked-In',
                'Total Drivers'
            ]
            }
    });

});

</script>