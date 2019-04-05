<?php
add_shortcode('schedule','schedule_task');
function schedule_task(){
  ob_start();

	$current_user_id = get_current_user_id();

$args = array(
  'post_type' => 'schedule',
  'post_status' => 'publish',
  'post_per_page' =>-1,
  'post_status' =>'publish',
  'orderby'          => 'name',
  'order'            => 'asc',
  'meta_query' => array(
       array(
           'key' => 'schedule_date_meta',
           'compare' => 'EXISTS',
       )
   )
);

/*echo '<pre>';
print_r($args);
echo '</pre>';*/

$schedule_arr = get_posts($args);


/*echo '<pre>';
print_r($schedule_arr);
echo '</pre>';*/

  $today_date = date('Y-m-d');
  $week_end = strtotime("+6 days",strtotime($today_date));
  $week_date  = date('Y-m-d',$week_end);


  $date_arr = createDateRangeArray($today_date,$week_date);


  $schedule_date_arr = strtotime("+13 days",strtotime($today_date));
  $week_date_new  = date('Y-m-d',$schedule_date_arr);



  $schedule_date_new = createDateRangeArray($today_date,$week_date_new);

  $schedule_data = array();
  // echo '<pre>';
  // print_r($schedule_date_new);
  // echo '</pre>';

  foreach ($schedule_date_new as $key => $date) {
    # code...

    $new_date = date('m/d/Y',strtotime($date));

/*  echo '<pre>';
  print_r($new_date);
  echo '</pre>';*/

    $args = array(
      'post_type' => 'schedule',
      'post_status' => 'publish',
      'post_per_page' =>-1,
      'post_status' =>'publish',
      'orderby'          => 'name',
      //'order'            => 'asc',
      'fields' => 'ids',
      'orderby'   => 'meta_value_num',
      'meta_key'  => 'schedule_time_meta',
      'meta_query' => array(
           array(
               'key' => 'schedule_date_meta',
               'value' => $new_date,
               'compare' => '=',
           )
       )
    );

  /*  echo '<pre>';
  print_r($args);
  echo '</pre>';*/

    $schedule_arr = new WP_Query($args);
    $postids = $schedule_arr->posts;


    if(!empty($postids)){
      $schedule_data[$new_date] = array(
        'post_ids' => $postids,
      );
    }

/*
    echo '<pre>';
  print_r($schedule_data);
  echo '</pre>';*/


  }

?>
<div id="scheduler">
<div class="all-dates-container">
    <div class="scheduler-title">Schedule</div>
    <div class="container main_date_section1">
      <div class="date_content">
        <a class="date-prev" onclick="generate_week_prev_click();"><i class="icon-arrow-left8"></i></a>
        <ul class="list-inline date_ul">
        <?php
        $i=1;
          foreach ($date_arr as $key => $value) {
          ?>
            <li data-date="<?php echo $value;?>">
              <a class="<?php if($i == 1){ echo 'day_active'; } ?>" href="javascript:void(0);" id="<?php echo $value;?>" onclick="filter_date(this.id);">
                <div class="abcd">
                  <div class="week">
                    <?php echo date('D',strtotime($value)); ?>
                  </div>
                  <div class="day">
                    <?php echo date('d',strtotime($value)); ?>
                  </div>
                </div>
              </a>
            </li>
          <?php
          $i++;
          }
        ?>
        </ul>
        <a class="date-next" onclick="generate_week_next_click();"><i class="icon-arrow-right8"></i></a>

        <!-- <div class="date_week">
            <ul>
                <li><a class="date-prev disable" onclick="generate_week_prev_click();"><i class="icon-arrow-left8"></i></a></li>
                <li><a class="date-next disable" onclick="generate_week_next_click();"><i class="icon-arrow-right8"></i></a></li>
            </ul>
        </div> -->
      </div>



    </div>
</div>

<div class="sch_append_schedule">
  <?php
$count_active_deactive = 0;
foreach ($schedule_data as $option_key => $option_value){
  //$post_id = $option_value->ID;
  $post_id = $option_value['post_ids'];

  //echo '---->'.$post_id;

  $new_date = date('Y-m-d',strtotime($option_key));
  ?>
  <div class="today_dt text-center-" id="div-<?php echo $new_date; ?>">
      <h4 id="" class="current_date <?php if($count_active_deactive > 0) { echo "deactive"; } else { echo "active"; } ?>"><?php echo date('l, F dS',strtotime($new_date)); ?></h4>

  <?php


  foreach ($post_id as $key => $value) {

  $schedule_time = get_post_meta($value,'schedule_time_meta',true);
  $trainer_name = get_post_meta($value,'trainer_name_meta',true);
  $schedule_date = get_post_meta($value,'schedule_date_meta',true);
  $feat_image= wp_get_attachment_image_src(get_post_thumbnail_id($trainer_name),'full')[0];

	$check_use_is_in_count = get_post_meta($value, 'user_count_in', true);

  $people_couted_in = get_post_meta($value, 'user_count_in', true);
  if(!empty($feat_image)){
    $img_url = $feat_image;
  }else{
    $img_url = 'http://www.personalbrandingblog.com/wp-content/uploads/2017/08/blank-profile-picture-973460_640-300x300.png';
  }
    ?>
      <div class="schedule_main">
        <a href="javascript:void(0);" data-id="<?php echo $value; ?>" data-trainer="<?php echo $trainer_name; ?>">
            <div class="schedule_main_inner">
                <div>
                    <div class="sch_time"><?php echo $schedule_time; ?></div>
                    <!-- <p class="sc-dHaUqb pxJoJ" id="d4d55a4e534c443cbb30612dee15f357" title="5 min Post-Ride Stretch" kind="LIVE">ENCORE</p> -->
                </div>
                <div class="sch_info">
                    <div class="sch_img"><img src="<?php echo $img_url; ?>" data-test-id="instructorImage" height="60" width="60"></div>
                    <div class="sch_title"><?php echo get_the_title($value); ?></div>
                    <div class="sch_trainer"><?php echo get_the_title($trainer_name); ?></div>
                    <!-- <div class="sc-ljUfdc bHUAIM" id="d4d55a4e534c443cbb30612dee15f357" title="5 min Post-Ride Stretch" kind="LIVE">First aired Wed 02/14/18 @ 1:30 AM</div> -->
                </div>
                <div class="sch_counter">
                    <div class="sch_counter_btn">
                      <?php if(!empty($check_use_is_in_count)){ ?>
												<?php if(in_array($current_user_id, $check_use_is_in_count[$trainer_name])){ 
                          ?>
													<button class="btn-count-me-out" id="<?php echo $value; ?>" title="<?php echo get_the_title($value); ?>" data-trainer-id = "<?php echo $trainer_name; ?>" >
	                            You're in
	                        </button>
												<?php } 
												else
												{
												?>
	                        <button class="btn-count-me-in" id="<?php echo $value; ?>" title="<?php echo get_the_title($value); ?>" >
	                            Count me in
	                        </button>
											<?php }

                    }else{
                      ?>
                          <button class="btn-count-me-in" id="<?php echo $value; ?>" title="<?php echo get_the_title($value); ?>" >
                              Count me in
                          </button>
                      <?php 
                    } ?>
                    </div>
                    <div class="count">
                        <!-- react-text: 169 -->
                        <?php
                            if(!empty($people_couted_in[$trainer_name]) && count($people_couted_in[$trainer_name]) > 0)
                            {
                                echo count($people_couted_in[$trainer_name]);
                            }
                            else
                            {
                                echo "0";
                            }
                        ?>
                        <!-- /react-text -->
                        <!-- react-text: 170 -->counted in
                        <!-- /react-text -->
                    </div>
                </div>
            </div>
        </a>
    </div>
      <?php
  }

  ?>
    </div>
  <?php
  $count_active_deactive++;
}
?>
</div>

  <div class="container"> <!-- -> This class was added here hidden -->
  <!-- Trigger the modal with a button -->

  <div id="vividModal" class="vividmodal">

  <!-- Modal content -->
  <div class="vividmodal-content">
    <span class="vividclose">&times;</span>
    <div class="vividmodal-body">
        <div class="loader"><img src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/img/ajax-loader.gif" class="hide_loader"></div>
    </div>
  </div>

</div>


  <div id="form_vividModal" class="form_vividmodal">

  <!-- Modal content -->
  <div class="form_vividmodal-content">
    <span class="vividclose">&times;</span>
    <div class="form_vividmodal-body">
        <div class="form_model_content">

          <h3>Enter your info to confirm your booking </h3>

          <form action="" name="form_schedule_notification" method="post" id="form_schedule_notification">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="firstname" placeholder="Your name.." required="">

            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lastname" placeholder="Your last name.." required="">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email.." required="">
            
            <div class="form_submit_btn">
              <input data-post="" data-trainer="" type="submit" value="Submit" name="form_vividModal_submit" id="form_vividModal_submit">
            </div>
          </form>
        </div>
    </div>
  </div>

</div>

</div>
</div>
<?php
  return ob_get_clean();
}
?>