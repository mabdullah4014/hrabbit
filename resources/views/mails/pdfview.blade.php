<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{env('APP_NAME')}}_Invoice_V2.0</title>
	<style type="text/css">

		.clearfix:after {content: "";display: table;clear: both;margin-top:0px !Important;margin-left:-10%;}

		a {color: #0087C3;text-decoration: none;}
		body {position: relative; width:720px; height:auto; margin: 0 1%;  color: #437082; background: #004757; font-family: Arial, sans-serif; font-size: 14px; font-family: Montserrat; }
		header { padding: 10px 0; margin-bottom: 20px; border-bottom: 1px solid #AAAAAA; }

		#logo { float: left; margin-top: 8px; }
		#logo img { height: 70px; }

		#company { float: right; text-align: right; }
		#details { margin-bottom: 50px; }
		#client { padding-left: 0px; border-left: 6px solid #0087C3; float: left; }
		#client .to { color: #777777; }

		h2.name { font-size: 1.4em; font-weight: normal; margin: 0; }
		#invoice { float: right; text-align: right; }
		#invoice h1 { color: #0087C3; font-size: 2.4em; line-height: 1em; font-weight: normal; margin: 0 0 10px 0; }
		#invoice .date { font-size: 1.1em; color: #777777; }
		.title_main{background: #ccc;}

		table { width: 100%; border-collapse: collapse; border-spacing: 0; background: #bcf3ff; margin-bottom: 10px; }
		table th, table td { padding: 20px; background: #bcf3ff; text-align: center; border-bottom: 1px solid #85bfcc; font-size: 16px; color: #0e4c53; }
		table th { white-space: nowrap; font-weight: normal; }
		table th:first-child { background: #64bed2; color: #fff; font-size: 20px; width: 50%; }
		table th:last-child { background: #64bed2; color: #fff; font-size: 20px; border-left: 1px solid #fff; }
		table td { text-align: center; }
		table td span { text-align: right; font-weight: 500; font-size: 24px; color: #fff; }
		table td:first-child { }
		table td h3{ color: #57B223; font-size: 1.2em; font-weight: normal; margin: 0 0 0.2em 0; }
		table .no { color: #FFFFFF; font-size: 1.6em; background: #57B223; }
		table .desc { text-align: left; }
		table .unit { background: #DDDDDD; }
		table .qty { }
		table .total { background: #57B223; color: #FFFFFF; }
		table td.unit, table td.qty,
		table td.total { font-size: 1.2em; }
		table tbody tr:last-child td { border: none; text-align: center; padding:10px; }
		table tfoot td { padding: 10px 20px; background: #FFFFFF; border-bottom: none; font-size: 1.2em; white-space: nowrap; border-top: 1px solid #AAAAAA; }
		table tfoot tr:first-child td { border-top: none; }
		table tfoot tr:last-child td { color: #57B223; font-size: 1.4em; border-top: 1px solid #57B223; }
		table tfoot tr td:first-child { border: none; }

		#thanks{ font-size: 2em; margin-bottom: 50px; }
		#notices{ padding-left: 6px; border-left: 6px solid #0087C3; }
		#notices .notice { font-size: 1.2em; }
		.Total_value { font-weight: 500; font-size: 20px; color: #004757; }

		.table1{float: right;}

		.fonttext{font-family:Montserrat, Arial, sans-serif; font-weight:600; font-size: 38px !Important; color: #004757; text-align: left;}
		.invoicespan{text-align: right; display: block; font-size: 22px; color: #004757;}


		footer { color: #777777; width: 90%; height: 30px; position: absolute; bottom: 0; border-top: 1px solid #AAAAAA; padding: 8px 0; text-align: center; }
	</style>
   </head>
<body>
	<div class="container">
	<br/>
		<!-- <a href="{{ route('pdfview',['download'=>'pdf']) }}">Download PDF</a> -->
	    <!-- {{$DriverName}} -->
		<table>
			<tr>
				<td class="table1"><img src="{{env('APP_URL').'/'.$logo}}" width="130px;" height="auto;"></td>
				<td class="fonttext">{{env('APP_NAME')}}</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><span class="invoicespan">INVOICE</span></td>
			</tr>
		</table>
		<table width="60%">
			<tr><th>Name</th>
	        <th>Description</th>
	        </tr>
	        <tr>
	            <td>Driver Name</td>
				<td>{{$DriverName}}</td>
	        </tr>
	        <tr>
	            <td>Customer Name</td>
	            <td>{{$CustomerName}}</td>
	        </tr>
	        <tr>
	            <td>Ride Date</td>
				<td>{{$Ridedate}}</td>
			</tr>
			<tr>
	                <td>Vehicle Category</td>
	                <td>{{str_replace("_"," ",$VehicleCategory)}}</td>
	        </tr>
	        <tr>
	                <td>Pick up From</td>
	                <td>{{$PickUpFrom}}</td>
	        </tr>
	        <tr>
	                <td>Drop to</td>
	                <td>{{$Dropto}}</td>
	        </tr>
	        <tr>
	                <td>Total Distance</td>
	                <td>{{$TotalDistance}}</td>
	        </tr>
	        <tr>
	                <td><span class="Total_value">Total Amount</span></td>
	                <td><span class="Total_value">{{$currency}}{{$TotalAmount}}</span></td>
	        </tr>
		</table>
	</div>
  </body>
</html>









































<!-- <head>
    <meta charset="utf-8">
    <title>{{env('APP_NAME')}}_Invoice_V2.0</title>
<style type="text/css">
	/* table td, table th{
		border:1px solid black;
	} */
        .clearfix:after {content: "";display: table;clear: both;}

		a {color: #0087C3;text-decoration: none;}
        body {position: relative; width: 21cm; height: 29.7cm; margin: 0 auto; color: #437082; background: #004757; font-family: Arial, sans-serif; font-size: 14px; font-family: Montserrat; }
		header { padding: 10px 0; margin-bottom: 20px; border-bottom: 1px solid #AAAAAA; }

        #logo { float: left; margin-top: 8px; }
		#logo img { height: 70px; }

		#company { float: right; text-align: right; }
		#details { margin-bottom: 50px; }
		#client { padding-left: 6px; border-left: 6px solid #0087C3; float: left; }
		#client .to { color: #777777; }

		h2.name { font-size: 1.4em; font-weight: normal; margin: 0; }
		#invoice { float: right; text-align: right; }
		#invoice h1 { color: #0087C3; font-size: 2.4em; line-height: 1em; font-weight: normal; margin: 0 0 10px 0; }
		#invoice .date { font-size: 1.1em; color: #777777; }
		.title_main{background: #ccc;}
        table { width: 100%; border-collapse: collapse; border-spacing: 0; background: #bcf3ff; margin-bottom: 10px; }
		table th, table td { padding: 20px; background: #bcf3ff; text-align: center; border-bottom: 1px solid #85bfcc; font-size: 16px; color: #0e4c53; }
		table th { white-space: nowrap; font-weight: normal; }
		table th:first-child { background: #64bed2; color: #fff; font-size: 20px; width: 50%; }
		table th:last-child { background: #64bed2; color: #fff; font-size: 20px; border-left: 1px solid #fff; }
		table td { text-align: center; }
		table td span { text-align: right; font-weight: 500; font-size: 24px; color: #fff; }
		table td:first-child { }
		table td h3{ color: #57B223; font-size: 1.2em; font-weight: normal; margin: 0 0 0.2em 0; }
		table .no { color: #FFFFFF; font-size: 1.6em; background: #57B223; }
		table .desc { text-align: left; }
		table .unit { background: #DDDDDD; }
		table .qty { }
		table .total { background: #57B223; color: #FFFFFF; }
		table td.unit, table td.qty,
		table td.total { font-size: 1.2em; }
		table tbody tr:last-child td { border: none; text-align: center; padding:10px; }
		table tfoot td { padding: 10px 20px; background: #FFFFFF; border-bottom: none; font-size: 1.2em; white-space: nowrap; border-top: 1px solid #AAAAAA; }
		table tfoot tr:first-child td { border-top: none; }
		table tfoot tr:last-child td { color: #57B223; font-size: 1.4em; border-top: 1px solid #57B223; }
		table tfoot tr td:first-child { border: none; }

                #thanks{ font-size: 2em; margin-bottom: 50px; }
		#notices{ padding-left: 6px; border-left: 6px solid #0087C3; }
		#notices .notice { font-size: 1.2em; }
		.Total_value { font-weight: 500; font-size: 20px; color: #004757; }

		#table{float: right;}

		#fonttext{font-family:Montserrat, Arial, sans-serif; font-weight:600; font-size: 38px !Important; color: #004757; text-align: left;}
		#invoicespan{text-align: right; display: block; font-size: 22px; color: #004757;}


		footer { color: #777777; width: 100%; height: 30px; position: absolute; bottom: 0; border-top: 1px solid #AAAAAA; padding: 8px 0; text-align: center; }
</style>

   </head>
   <body>
<div class="container">


	<br/>

	<table>
			<tr>
				<td ><img src="logo.png" width="130px;" height="auto;"></td>
				<td >{{env('APP_NAME')}}</td>
			</tr>
		</table>
		<table>
			<tr>
				<td><span style="text-align: right; display: block; font-size: 22px; color: #004757;">INVOICE</span></td>
			</tr>
		</table>
	<!-- <a href="{{ route('pdfview',['download'=>'pdf']) }}">Download PDF</a> -->
    <!-- {{$DriverName}} -->

	<!-- <table>
		<tr><th>Name</th>
        <th>Description</th>
        </tr>
        <tr>
            <td>Driver Name</td>
			<td>{{$DriverName}}</td>
        </tr>
        <tr>
            <td>Customer Name</td>
            <td>{{$CustomerName}}</td>
        </tr>
        <tr>
            <td>Ride Date</td>
			<td>{{$Ridedate}}</td>
		</tr>
		<tr>
            <td>Vehicle Category</td>
            <td>{{$VehicleCategory}}</td>
        </tr>
        <tr>
            <td>Pick up From</td>
            <td>{{$PickUpFrom}}</td>
        </tr>
        <tr>
            <td>Drop to</td>
            <td>{{$Dropto}}</td>
        </tr>
        <tr>
            <td>Total Distance</td>
            <td>{{$TotalDistance}}</td>
        </tr>
        <tr>
            <td>Total Amount</td>
            <td>{{$TotalAmount}}</td>
        </tr>
	</table>
</div> --> -->