<div class="table-list">
    <div class="row">
        <div class="col-lg-12">
        	<div class="box">
        	<div class="box-header"></div>
           <div class="box-body table-responsive no-padding">
                <table class="table table-hover"  >
                    <thead>
                    	<tr>
                            <th>Driver ID</th>
                    		<th>Driver Name</th>
                    		<th>Driver Rating</th>
                    		<th>Cab Rating</th>
                    		<th>Overall Rating</th>
                    		<th>Action</th>
                    	</tr>
                    </thead>
                    <tbody>
                    	@foreach($driverRatings as $dr)
                    		<tr>
                                <td>{{$dr->driver_id}}</td>
                    			<td>{{$dr->driver->name}} {{$dr->driver->last_name}}</td>
                    			<td>{{number_format($dr->drating,1)}}</td>
                    			<td>{{number_format($dr->crating,1)}}</td>
                    			<td>{{number_format($dr->orating,1)}}</td>
                    			<td><a href="/admin/detailRating/{{$dr->driver_id}}" title="Detail"><i class="fa fa-info" aria-hidden="true"></i></a> </td>
                    		</tr>
                    	@endforeach
                    </tbody>
                 </table>
            </div>
            </div>
        </div>
    </div>
 </div>
