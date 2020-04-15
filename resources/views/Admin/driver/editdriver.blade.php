    <section class="content">

        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


        <div class="row"><div class="col-md-12"><div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Edit</h3>

        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
    <a href="{{$base_url}}/admin/drivers" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;List</a>
</div> <div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
</div>
        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start-->
            <form action="{{$url}}" method="post" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" pjax-container>

        <div class="box-body">

                            <div class="fields-group">

                                                                        <div class="form-group ">
    <label class="col-sm-2 control-label">ID</label>
    <div class="col-sm-8">
        <div class="box box-solid box-default no-margin">
            <!-- /.box-header -->
            <div class="box-body">
                {{$driver->id}}&nbsp;
            </div><!-- /.box-body -->
        </div>


    </div>
</div>
                                                    <div class="form-group  ">

    <label for="first_name" class="col-sm-2 control-label">First Name</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="first_name" name="first_name" value="{{$driver->name}}" class="form-control first_name" placeholder="Input First Name" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

    <label for="last_name" class="col-sm-2 control-label">Last Name</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="last_name" name="last_name" value="{{$driver->last_name}}" class="form-control last_name" placeholder="Input Last Name" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

    <label for="email" class="col-sm-2 control-label">Email</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>

            <input type="email" id="email" name="email" value="{{$driver->email}}" class="form-control email" placeholder="Input Email" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

    <label for="phone" class="col-sm-2 control-label">Phone Number</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="phone" name="phone" value="{{$driver->phone_number}}" class="form-control phone" placeholder="Input Phone Number" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

    <label for="dob" class="col-sm-2 control-label">Date Of Birth</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
            <input style="width: 110px" type="text" id="dob" name="dob" value="{{ $driver->dob !='0000-00-00' ? $driver->dob : date('Y-m-d') }}" class="form-control dob" placeholder="Input Date Of Birth" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

    <label for="vehicle_number" class="col-sm-2 control-label">Vehicle Number</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="vehicle_number" name="vehicle_number" value="{{$driver->vehicle_num}}" class="form-control vehicle_number" placeholder="Input Vehicle Number" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

<label for="vehicle_type" class="col-sm-2 control-label">Service Category</label>

    <div class="col-sm-8">


        <input type="hidden" name="vehicle_type"/>
        <select class="form-control vehicle_type" name="vehicle_type">
           @if(count($vehicle) > 0)
              @foreach($vehicle as $role=>$val)
               <option value="{{$role}}" {{ $driver->vehicle_type == $role ? 'selected="selected"' : '' }}>{{$role}}</option>
              @endForeach
            @endif
        </select>
    </div>
</div>

                                                    <div class="form-group  ">

    <label for="license_number" class="col-sm-2 control-label">Licence Number</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="license_number" name="license_number" value="{{$driver->license_no}}" class="form-control license_number" placeholder="Input Licence Number" />


        </div>


    </div>
</div>
<div class="form-group  ">

    <label for="id_proof" class="col-sm-2 control-label">Id proof</label>
    <?php
//$external_link       = $_ENV['API_URL'] . "/images/driver/" . $driver->id_proof;
//$external_link_admin = $_ENV['APP_URL'] . "/uploads/" . $driver->id_proof;
/*if (@getimagesize($external_link)) {
$path = $external_link;
} elseif (@getimagesize($external_link_admin)) {
$path = $external_link_admin;
} else {
$path = $external_link;
}*/
$path = "/".$driver->id_proof;

?>
    <div class="col-sm-8">

       <input type="file" class="id_proof" name="id_proof" data-initial-preview="{{$path}}" data-initial-caption="{{$driver->id_proof}}" />


    </div>
</div>

 <div class="form-group  ">
    <?php
/*$external_link = $_ENV['API_URL'] . "/images/driver/" . $driver->photo;
$external_link_admin = $_ENV['APP_URL'] . "/uploads/" . $driver->photo;
if (@getimagesize($external_link)) {
$path = $external_link;
} elseif (@getimagesize($external_link_admin)) {
$path = $external_link_admin;
} else {
$path = $external_link;
}*/
$path = "/".$driver->photo;

?>
    <label for="photo" class="col-sm-2 control-label">Photo</label>
    <div class="col-sm-8">
        <input type="file" class="photo" name="photo" id="driverPhoto" data-initial-preview="{{$path}}" data-initial-caption="{{$driver->photo}}" />
    </div>
</div>

<div class="form-group  ">

    <label for="address" class="col-sm-2 control-label">Residence Address</label>

    <div class="col-sm-8">

        <textarea name="address" class="form-control address" rows="5" placeholder="Input Residence Address"  >{{$driver->address}}</textarea>


    </div>
</div>

                                                    <div class="form-group  ">

    <label for="postal_code" class="col-sm-2 control-label">Postal Code</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="postal_code" name="postal_code" value="{{$driver->postal_code}}" class="form-control postal_code" placeholder="Input Postal Code" />


        </div>


    </div>
</div>
                                                    <div class="form-group  ">

<label for="country_name" class="col-sm-2 control-label">Country</label>

    <div class="col-sm-8">


        <input type="hidden" name="country_name"/>
        <select class="form-control country_name" name="country_name" placeholder="Country">
            <option value=""> </option>
           @if(count($country) > 0)
              @foreach($country as $role=>$val)
               <option value="{{$val}}" {{ $driver->country_name == $val ? 'selected="selected"' : '' }}>{{$role}}</option>
              @endForeach
            @endif
        </select>

    </div>
</div>

                                                    <div class="form-group  ">

<label for="state_id" class="col-sm-2 control-label">Province</label>

    <div class="col-sm-8">


        <input type="hidden" name="state_id"/>
        <select class="form-control state_id" name="state_id">
           @if(count($status) > 0)
              @foreach($states as $role=>$val)
               <option value="{{$val}}" {{ $driver->state_id == $val ? 'selected="selected"' : '' }}>{{$role}}</option>
              @endForeach
            @endif
        </select>



    </div>
</div>

                                                    <div class="form-group  ">

    <label for="city" class="col-sm-2 control-label">City</label>

    <div class="col-sm-8">


        <div class="input-group">

                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>

            <input type="text" id="city" name="city" value="{{$driver->city}}" class="form-control city" placeholder="Input City" />


        </div>


    </div>
</div>
<div class="form-group  ">

<label for="status" class="col-sm-2 control-label">Status</label>

    <div class="col-sm-8">


        <input type="hidden" name="status"/>
        <select class="form-control vehicle_type" name="status">
           @if(count($status) > 0)
              @foreach($status as $role=>$val)
               <option value="{{$val}}" {{ $driver->status == $val ? 'selected="selected"' : '' }}>{{$role}}</option>
              @endForeach
            @endif
        </select>

    </div>
</div>

 <div class="form-group ">
    <label class="col-sm-2 control-label">Created At</label>
    <div class="col-sm-8">
        <div class="box box-solid box-default no-margin">
            <!-- /.box-header -->
            <div class="box-body">
                {{$driver->created_at}}&nbsp;
            </div><!-- /.box-body -->
        </div>


    </div>
</div>
                                                    <div class="form-group ">
    <label class="col-sm-2 control-label">Updated At</label>
    <div class="col-sm-8">
        <div class="box box-solid box-default no-margin">
            <!-- /.box-header -->
            <div class="box-body">
                {{$driver->updated_at}}&nbsp;
            </div><!-- /.box-body -->
        </div>


    </div>
</div>


                </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
                            {{ csrf_field() }}
                           <!-- <input type="hidden" name="_token" value="LPXjiOOXffg6o8x952t4ZoZEpaUbtalofRJKJr7v"> !-->
                        <div class="col-md-2">

            </div>
            <div class="col-md-8">

                <div class="btn-group pull-right">
    <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Save">Save</button>
</div>

                <div class="btn-group pull-left">
    <button type="reset" class="btn btn-warning">Reset</button>
</div>

            </div>

        </div>

                    <input type="hidden" name="_method" value="PUT" class="_method"  />


        <!-- /.box-footer -->
    </form>
</div>

</div></div>

    </section>
        <script data-exec-on-popstate>

$(function () {
    $(document).off('change', ".country_name");
    $(document).on('change', ".country_name", function () {
        var target = $(this).closest('.fields-group').find(".state_id");
        $.get("/admin/province?q="+this.value, function (data) {
            target.find("option").remove();
            $(target).select2({
                data: $.map(data, function (d) {
                    d.id = d.id;
                    d.text = d.state_name;
                    return d;
                })
            }).trigger('change');
        });
    });

    $('.form-history-back').on('click', function (event) {
        event.preventDefault();
        history.back(1);
    });

    $('.dob').parent().datetimepicker({"format":"YYYY-MM-DD","locale":"en","allowInputToggle":true});

    $(".vehicle_type").select2({"allowClear":true,"placeholder":"Service Category"});
    var token = document.getElementsByName('_token')[0].value;
    $("input.id_proof").fileinput({"overwriteInitial":true,"initialPreviewAsData":true,"browseLabel":"Browse","showRemove":false,"showUpload":false,"deleteExtraData":{"id_proof":"_file_del_","_file_del_":"","_token":token,"_method":"PUT"},"deleteUrl":"{{$path}}","allowedFileTypes":["image"]});


    $("input.photo").fileinput({"overwriteInitial":true,"initialPreviewAsData":true,"browseLabel":"Browse","showRemove":false,"showUpload":false,"deleteExtraData":{"photo":"_file_del_","_file_del_":"","_token":token,"_method":"PUT"},"deleteUrl":"{{$path}}","allowedFileTypes":["image"]});

    $(".country_name").select2({"allowClear":true,"placeholder":"Country"});
    $(".state_id").select2({"allowClear":true,"placeholder":"Province"});
    $(".status").select2({"allowClear":true,"placeholder":"Status"});
});
</script>
