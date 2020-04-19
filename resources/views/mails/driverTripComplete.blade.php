<html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <title>{{env('APP_NAME')}}</title>
            </head>

            <body>
            <table width="600" style="display:block;background-color:#158da5;" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
            <td align="left" valign="top"  style="display:block;background-color:#158da5;text-align: center;margin: 5%;">
            <div style="font-family:Georgia, Times New Roman, Times, serif; font-weight:600;font-size:25px; color:#fff;">Welcome to {{env('APP_NAME')}} <span style="color:#478730;"></span></div>



            </td>
            </tr>
            <tr>
            <td align="center" valign="top" bgcolor="#158da5" style="background-color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000; padding: 9%;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:10px;">
            <tr>
            <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#525252;">

            <!-- <div style="font-family:Georgia, Times New Roman, Times, serif; font-weight:600;font-size:25px; color:#000;">Welcome to {{env('APP_NAME')}} Service<span style="color:#478730;"></span></div> -->
            <div>
            <img src="{{env('APP_URL').'/'.$logo}}" align="right" style="margin-left:10px;width:75px;height:75px;" class="CToWUd a6T" tabindex="0">
            <p style="font-family:Georgia, Times New Roman, Times, serif; font-weight:600;font-size:20px; color:#000;">Dear {{$driver_name}},</p>
            {!!$content!!}
            <table>
                  <tr>
                        <td>Customer Id</td>
                        <td>: </td>
                        <td><span style="font-weight:bold">{{$customer_id}}</span></td>
                  </tr>
                  <tr>
                        <td>Customer Name</td>
                        <td>: </td>
                        <td><span style="font-weight:bold">{{$customer_name}}</span></td>
                  </tr>
                  <tr>
                        <td>Driver Id</td>
                        <td>: </td>
                        <td><span style="font-weight:bold">{{$driver_id}}</span></td>
                  </tr>
                  <tr>
                        <td>Driver Name</td>
                        <td>: </td>
                        <td><span style="font-weight:bold">{{$driver_name}}</span></td>
                  </tr>
                  <tr>
                        <td>Booking Id</td>
                        <td>: </td>
                        <td><span style="font-weight:bold">{{$booking_id}}</span></td>
                  </tr>
                  <tr>
                        <td>Total Amount</td>
                        <td>: </td>
                        <td><span style="font-weight:bold">{{$total}}{{$currency}}</span></td>
                  </tr>
            </table>
            </div></td>
            </tr>
            </table></td>
            </tr>
            <tr>
            <td align="left" valign="top" bgcolor="#478730" style="background-color:#158da5;"><table width="100%" border="0" cellspacing="0" cellpadding="15">
            <tr>
            <td nowrap align="left" valign="top" style="color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:13px;">
            Email: <a href={{$email_to}} style="color:#ffffff; text-decoration:none;">{{$admin_mail}}</a><br>
            Website: <a href={{$website}} target="_blank" style="color:#ffffff; text-decoration:none;">{{$app_name}}</a>
            </td>
            <td>
            <a href={{$skype}} target="_blank" data-saferedirecturl="https://www.google.com/url?hl=en&amp;q=https://www.facebook.com/&amp;source=gmail&amp;ust=1533644622433000&amp;usg=AFQjCNGiZC3ONKAFGuNdkWaNVPC461SdoA"><img src="https://ci3.googleusercontent.com/proxy/78KqLDWX6UuBjkvThOa7lQJ98bBuLOuOeJPzr8untw6kM44UYtRfw9d16nrvnyx_79-vNnCxNxqyve_WlsIv=s0-d-e1-ft#http://api.tinonetic.com/images/skype.png" style="width:8%;float:right;margin:1%" class="CToWUd"></a>

<!-- <a href="https://plus.google.com/up/?continue=https://plus.google.com/people" target="_blank" data-saferedirecturl="https://www.google.com/url?hl=en&amp;q=http://Agri&amp;source=gmail&amp;ust=1533644622433000&amp;usg=AFQjCNGiZC3ONKAFGuNdkWaNVPC461SdoA"><img src="https://ci5.googleusercontent.com/proxy/aQYOMcFbEyGGu8k1h-y2oqEqvN8-m8FZwIN9e8KJpUeDsU0Ypok8IqVZC2whPxQq1Hz6FA-UE5X57Zjt0uloVg=s0-d-e1-ft#http://api.tinonetic.com/images/google.png" style="width:8%;float:right;margin:1%" class="CToWUd"></a> -->

<a href={{$twitter}}><img src="https://ci6.googleusercontent.com/proxy/vbCAMvCsphnVPsfCg1mOpdgtKOYO9hjGnzLjBS-iailyrvwErTaO7Jhf1rra8gL9964hIBMfm7ytze94J345PPk=s0-d-e1-ft#http://api.tinonetic.com/images/twitter.png" style="width:8%;float:right;margin:1%" class="CToWUd"></a>

<a href={{$facebook}} target="_blank" data-saferedirecturl="https://www.google.com/url?hl=en&amp;q=https://www.facebook.com/&amp;source=gmail&amp;ust=1533644622433000&amp;usg=AFQjCNGiZC3ONKAFGuNdkWaNVPC461SdoA"><img src="https://ci5.googleusercontent.com/proxy/WOmjfdApD_mNzisWSRDK9IEn_bT_vRgQaOv6Yz7NficnSoIQBKi-h9IkiQBhvwC6A8XE5XHAGwSRhbhJi4PB2UOz=s0-d-e1-ft#http://api.tinonetic.com/images/facebook.png" style="width:8%;float:right;margin:1%" class="CToWUd"></a>
            </td>
            </tr>
            </table></td>
            </tr>
            </table>
            </body>
            </html>