<?php
	/* Add Metabox Function */
add_action('add_meta_boxes', 'schedule_add_custom_meta_boxes');
function schedule_add_custom_meta_boxes(){
    add_meta_box(
        'schedule_settings',
        'Schedule Setting',
        'schedule_settings',
        'schedule'
    );

    add_meta_box(
        'schedule_register_user_listing',
        'Register User Listing',
        'schedule_register_user_listing',
        'schedule'
    );
}

/**
 * undocumented function
 *
 * @return void
 * @author
 **/
function schedule_settings($post){


  $post_id = $post->ID;
    $info = get_post_meta($post_id,'schedule_settings_meta',true);

   /* echo '<pre>';
  print_r($info);
  echo '</pre>';*/

  $query = array(
   'post_type'      => 'trainer',
   'post_status'    => array('publish' ),
   'post_per_page'  => -1,
  );

  $all_trainers = query_posts($query);
    // echo '<pre>';
    // print_r($all_trainers);
    // echo '</pre>';
  $time = get_post_meta($post_id,'schedule_time_meta',true);
  $trainer = get_post_meta($post_id,'trainer_name_meta',true);
  $date = get_post_meta($post_id,'schedule_date_meta',true);
  $location = get_post_meta($post_id,'schedule_location_meta',true);
	$time_length = get_post_meta($post_id, 'riding_time_meta', true);
  $time_length = (!empty($time_length)) ? explode('-', $time_length) : '';
  $ride_length = (!empty($time_length[0])) ? $time_length[0] : '';
  $time_type = (!empty($time_length[1])) ? $time_length[1] : '';
  $map_api = get_option('map_api_google');
   
	// if(!empty($time_length))
	// {
	// 	$time_length = explode('-', $time_length);
	// 	$ride_length = $time_length[0];
	// 	$time_type = $time_length[1];
	// }
  //$time = $info['schedule_time'];
  //$date = $info['schedule_date'];
  //$trainer = $info['trainer_name'];

  ?>
  <style type="text/css">
  input[type=text] {
      width: 100%;
  }

  </style>

  <div class="container">

  </div>


  <strong>Schedule Time:</strong> <strong><?php if(!empty($time)){ echo $time; } ?></strong>
    <input type="text" id="input-a" name="schedule_time" value="<?php echo $time; ?>" data-default="20:48">

    <h2>Trainer Name</h2>
    <?php if(!empty($all_trainers) && isset($all_trainers) && count($all_trainers) > 0){ ?>
    <select class="form-control" name="trainer_name">
        <option value="">Select Trainer</option>
    <?php foreach($all_trainers as $key => $data): ?>
        <option value="<?php echo $data->ID; ?>" <?php if($trainer == $data->ID) { echo "selected=selected"; } ?>><?php echo $data->post_title; ?></option>
    <?php endforeach; ?>
    </select>
    <?php } ?>

	</br></br><strong>Riding time length</strong> <strong><?php if(!empty($ride_length)){ echo $ride_length; } if(isset($time_type)){ if($time_type == 1) { echo " Sec"; } elseif ($time_type == 2) { echo " Min"; } else { echo " Hour"; } } ?></strong>
	</br>
		<table>
			<tr>
				<td><input type="text" name="riding-time-length" value="<?php echo $ride_length; ?>" class="form-control"></td>
				<td>
					<select name="time-length-type" class="form-control">
						<option value="1" <?php if($time_type == 1) { echo "selected=selected"; } ?>>Seconds</option>
						<option value="2" <?php if($time_type == 2) { echo "selected=selected"; } ?>>Minutes</option>
						<option value="3" <?php if($time_type == 3) { echo "selected=selected"; } ?>>Hours</option>
					</select>
				</td>
			</tr>
		</table>

    <h2>Schedule Date</h2>
    <input type="text" name="schedule_date" id="datepicker"  value="<?php echo $date; ?>" />


    <!-- Map Script -->
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?php echo $map_api; ?>&sensor=false&libraries=places"></script>
    <script type="text/javascript">
        google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('schedule_location'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                var address = place.formatted_address;
                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();
                var mesg = "Address: " + address;
                mesg += "\nLatitude: " + latitude;
                mesg += "\nLongitude: " + longitude;
                //alert(mesg);
            });
        });
    </script> 
    <span>Location:</span>
    <input type="text" name="schedule_location" id="schedule_location" placeholder="Enter a location" value="<?php echo $location; ?>"/>


    <script type="text/javascript">


       jQuery(document).ready(function() {
      // Datepicker Popups calender to Choose date.
      jQuery(function() {
          jQuery("#datepicker").datepicker();
          // Pass the user selected date format.
            jQuery("#format").change(function() {
            jQuery("#datepicker").datepicker("option", "dateFormat",'yy-mm-dd');
          });
        });
      });

      jQuery('#input-a').clockpicker({
          autoclose: true,
          twelvehour: true,
          donetext: 'Done'
      });

    </script>
  <?php
}

/* Save page Setting */
add_action('save_post','schedule_settings_save');
function schedule_settings_save($post_id){
  /*extract($_POST);
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  exit;
*/
  if(isset($_POST["schedule_time"])){
         //UPDATE:
        $schedule_time = $_POST['schedule_time'];
        update_post_meta($post_id, 'schedule_time_meta', $schedule_time);
    }

    if(isset($_POST["trainer_name"])){
         //UPDATE:
        $trainer_name = $_POST['trainer_name'];
        update_post_meta($post_id, 'trainer_name_meta', $trainer_name);
    }

		if(isset($_POST["riding-time-length"]) && isset($_POST["time-length-type"]))
		{
			$time_length = $_POST['riding-time-length']."-".$_POST['time-length-type'];
			update_post_meta($post_id, 'riding_time_meta', $time_length);
		}

    if(isset($_POST["schedule_date"])){
         //UPDATE:
        $schedule_date = $_POST['schedule_date'];
        $schedule_date = date('m/d/Y',strtotime($schedule_date));
        update_post_meta($post_id, 'schedule_date_meta', $schedule_date);
    }

    if(isset($_POST["schedule_location"])){
         //UPDATE:
        $schedule_location = $_POST['schedule_location'];
        update_post_meta($post_id, 'schedule_location_meta', $schedule_location);
    }
  /*$data = array(
      'schedule_time' => $schedule_time,
      'trainer_name'  => $trainer_name,
      'schedule_date' => $schedule_date,
  );*/

  /*echo '<pre>';
  print_r($data);
  echo '</pre>';*/
    //update_post_meta($post_id,'schedule_settings_meta',$data);
}

function schedule_register_user_listing($post){

/*  echo '<pre>';
  print_r($post);
  echo '</pre>';*/

  $post_id = $post->ID;
  $trainer_name = get_post_meta($post_id,'trainer_name_meta',true);
  $check_use_is_in_count = get_post_meta($post_id, 'user_count_in', true);
  ?>

  <style>
.user_listing_dsp table, .user_listing_dsp th,.user_listing_dsp td {
    /*border: 1px solid black;
    border-collapse: collapse;*/
}
.user_listing_dsp th, .user_listing_dsp td {
    padding: 5px;
    text-align: left;
}
</style>

<div class="user_listing_dsp">
  
  <?php
  if(!empty($check_use_is_in_count) && count($check_use_is_in_count) > 0){?>
    <table style="width:100%">
  <tr>
    <th>Trainer Name</th>
    <th>User Name</th> 
    <th>User Email</th>
  </tr>
    <?php
      foreach ($check_use_is_in_count as $value) {
      # code...
      foreach ($value as $key => $value1) {
        # code...
        $userid = $value1;

        $user_info  = get_userdata( $userid );
        ?><tr>
          <td><?php echo get_the_title( $trainer_name ); ?></td>
          <td><?php echo $user_info->user_login;  ?></td> 
          <td><?php echo $user_info->user_email;  ?></td>
        </tr>
        <?php
        /*echo '<pre>';
        print_r($author_obj);
        echo '</pre>';*/
      }
    }
  }else{
    echo 'No User found';
  }
    
    ?>

</table>
</div>
  <?php
  
}
?>