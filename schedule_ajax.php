<?php 

add_action( 'wp_ajax_schedule_popup_data', 'schedule_popup_data' );

add_action( 'wp_ajax_nopriv_schedule_popup_data', 'schedule_popup_data' );



function schedule_popup_data(){

  if(!is_user_logged_in())

  {

    $login_url = home_url()."/login";

     // wp_redirect("www.google.com");

    $json['error'] = true;

    $json['redirect'] = $login_url;

    echo json_encode($json);

    exit();

}

else

{

  $data = $_POST;



  /*echo '<pre>';

  print_r($data['schedule_id']);

  echo '</pre>';*/

  $post   = get_post( $data['schedule_id'] );

  $title = $post->post_title;

  $current_user_id = get_current_user_id();

  $post_id = $data['post_id'];



  $trainer_id = $data['trainer_id'];

  $post_content = $post->post_content;



  $trainer_post   = get_post( $data['trainer_id'] );

  $trainer_content = $trainer_post->post_content;

  $trainer_title = $trainer_post->post_title;



  $schedule_time = get_post_meta($data['schedule_id'],'schedule_time_meta',true);

  $trainer_name = get_post_meta($data['schedule_id'],'trainer_name_meta',true);

  $schedule_date = get_post_meta($data['schedule_id'],'schedule_date_meta',true);

  $feat_image= wp_get_attachment_image_src(get_post_thumbnail_id($data['schedule_id']),'full')[0];

  $training_time = get_post_meta($data['schedule_id'],'riding_time_meta',true);

  $training_time = explode('-', $training_time);

  $additional_time = $training_time[0];



  $location = get_post_meta($data['schedule_id'],'schedule_location_meta',true);



  if($training_time[1] == 1)

  {

    $additional_time = "+".$additional_time." second";

}

elseif ($training_time[1] == 2) {

    $additional_time = "+".$additional_time." minute";

}

else {

    $additional_time = "+".$additional_time." hour";

}

$google_calendar_start_date_time = $schedule_date.' '.$schedule_time;



$converted_schedule_time = date("H:i:s", strtotime($schedule_time));

$end_time = date('g:i a', strtotime($additional_time, strtotime($converted_schedule_time)));



$google_calendar_end_date_time = $schedule_date.' '.str_replace(' ', '', strtoupper($end_time));;

date_default_timezone_set('Asia/Kolkata');

$google_converted_start_datetime = gmdate("Ymd\THis\Z", strtotime($google_calendar_start_date_time));

$google_converted_end_datetime = gmdate("Ymd\THis\Z", strtotime($google_calendar_end_date_time));



  //exit;



  // Get user counter

$get_user_counter = get_post_meta($post_id, 'user_count_in', true);



    // Add user in count me in array

if(empty($get_user_counter))

{

  $count_me_arr = array();

  $count_me_arr[$trainer_id][] = $current_user_id;

  if(!empty($count_me_arr) && count($count_me_arr) > 0)

  {

    update_post_meta($post_id, 'user_count_in', $count_me_arr);

    $json['update_counter'] = "successfull";

}

}

else

{

    if(!in_array($current_user_id, $get_user_counter[$trainer_id]))

    {

        $get_user_counter[$trainer_id][] = $current_user_id;

        if(!empty($get_user_counter) && count($get_user_counter) > 0)

        {

            update_post_meta($post_id, 'user_count_in', $get_user_counter);

            $json['update_counter'] = "successfull";

        }

    }

}



$blog_title = get_bloginfo( 'name' );



if(!empty($feat_image)){

  $img_url = $feat_image;

}else{

  $img_url = 'http://www.personalbrandingblog.com/wp-content/uploads/2017/08/blank-profile-picture-973460_640-300x300.png';

}



$html = '<div ng-transclude="ng-transclude" class="pelo-modal__body snug-modal__body">

<pelo-ng-react-modal component="component" class="ng-scope ng-isolate-scope">

<div data-reactroot="" class="sc-jvjHmY akCPy sc-jMMfwr ieHcmp">

<div class="sc-zDqdV dPPLLi sc-jdeSqf jnnFGt">

<div class="sc-jdeSqf jnnFGt">

<h4 class="sc-kRCAcj uvGLZ schedule-popup-heading" data-test-id="countedInText">Can we count you in?</h4>

<div class="sc-jgwFWF ebSqhe">

<a target="_blank" href="https://calendar.google.com/calendar/r/eventedit?text='.$title.'&dates='.$google_converted_start_datetime.'/'.$google_converted_end_datetime.'&details='.$post_content.'&sprop&sprop=name:"><button class="sc-dVhcbM kvMyDf schedule-popup-book-class" data-test-id="countedInButton">SYNC WITH GOOGLE CALENDAR</button></a>

</div>

<div class="schedule-popup-calender-types hide-custom">

</div>

</div>



<h4 class="sc-kRCAcj uvGLZ schedule-popup-heading1" data-test-id="countedInText">'.get_the_title($post_id).'</h4>

<p>'.$post_content.'</p>

<hr class="full-w bg-bordergray sc-fIIFii bRmepl" style="border: 0px; float: none; height: 1px;">



<ul class="popup_ul"><li><i class="icon-clock"></i> '.$schedule_date.' at '.$schedule_time.'</li><li><i class="icon-location4"></i> '.$location.'</li></ul>



<hr class="full-w bg-bordergray sc-fIIFii bRmepl" style="border: 0px; float: none; height: 1px;">

<div class="sch_info">

<div class="sch_img"><img src="'.$img_url.'" data-test-id="instructorImage" height="120" width="120"></div>

<div class="sch_title_p"><span>'.$trainer_title.'</span></div>

<div class="sch_trainer_p"><p>'.$trainer_content.'</p></div>



</div>



<hr class="full-w bg-bordergray sc-fIIFii bRmepl" style="border: 0px; float: none; height: 1px;">

<ul class="popup_social">

<li><a href="#"><i class="icon-facebook"></i></a></li>

<li><a href="#"><i class="icon-twitter"></i></a></li>

<li><a href="#"><i class="icon-pinterest"></i></a></li>

<li><a href="#"><i class="icon-instagram3"></i></a></li>

</ul>

</div>



</div>

</pelo-ng-react-modal>

</div>';

    // $json['error'] = false;

$json['error'] = false;

$json['html'] = $html;



echo json_encode($json);

  //echo $redirect_to;

exit();

}

}





if(!function_exists('vivid_count_me_out'))

{

  function vivid_count_me_out()

  {

    $post_id = $_POST['post_id'];

    $trainer_id = $_POST['trainer_id'];

    $current_user_id = get_current_user_id();


    $new_count_array = array();



    if(!empty($post_id) && !empty($trainer_id))

    {

      $get_user_counter = get_post_meta($post_id, 'user_count_in', true);

      if(!empty($get_user_counter) && count($get_user_counter) > 0)

      {

        foreach ($get_user_counter[$trainer_id] as $key => $value) {

          // code...

          if($value != $current_user_id)

          {

            $new_count_array[$trainer_id][] = $value;

        }

    }

}

update_post_meta($post_id, 'user_count_in', $new_count_array);

$json['msg'] = "success";

echo json_encode($json);

}

exit();

}

add_action( 'wp_ajax_vivid_count_me_out', 'vivid_count_me_out' );

add_action( 'wp_ajax_nopriv_vivid_count_me_out', 'vivid_count_me_out' );

}







add_action( 'wp_ajax_schedule_notification', 'schedule_notification' );

add_action( 'wp_ajax_nopriv_schedule_notification', 'schedule_notification' );



function schedule_notification(){



  /*echo '<pre>';

  print_r($_POST);

  echo '</pre>';*/



  $trainer = $_POST['trainer_id'];

  $post_id = $_POST['post_id'];

  $trainer_name = get_the_title($trainer);

  $schedule_name = get_the_title($post_id);

  $fname = $_POST['fname'];

  $lname = $_POST['lname'];

  $email = $_POST['email'];

  $blog_title = get_bloginfo( 'name' );

  $admin_email = get_option( 'admin_email' );

  $schedule_date = get_post_meta($post_id,'schedule_date_meta',true);

  // echo '<pre>';

  // print_r($schedule_date);

  // echo '</pre>';



  // exit;

  

  //$html_content = 'Hello , Bingooo' ;

  $html = '<table bgcolor="#ffffff" width="100%" border="0" cellspacing="0" cellpadding="0" class="tableContent" align="center"  style="font-family:Helvetica, Arial,serif;">

  <tbody>

  <tr>

  <td><table width="600" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" class="MainContainer">

  <tbody>

  <tr>

  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tbody>

  <tr>

  <td valign="top" width="40">&nbsp;</td>

  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tbody>

  <!-- =============================== Header ====================================== -->   

  <tr>

  <td height="75" class="spechide"></td>



  <!-- =============================== Body ====================================== -->

  </tr>

  <tr>

  <td class="movableContentContainer " valign="top">

  <div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">



  <tr>

  <td align="left">

  <div class="contentEditableContainer contentTextEditable">

  <div class="contentEditable">

  <h2 >Schedule Notification</h2>

  </div>

  </div>

  </td>

  </tr>



  <tr><td height="15"> </td></tr>



  <tr>

  <td align="left">

  <div class="contentEditableContainer contentTextEditable">

  <div class="contentEditable">

  <table style="width:100%">

  <tr >

  <td align="left"><b>Schedule Name</b></td>

  <td align="left">'.$schedule_name.'</td>

  </tr>

  <tr >

  <td align="left"><b>User Name</b></td>

  <td align="left">'.$fname.'&nbsp;'.$lname.'</td>

  </tr>

  <tr >

  <td align="left"><b>User Email</b></td>

  <td align="left">'.$email.'</td>

  </tr>

  <tr >

  <td align="left"><b>Schedule Date</b></td>

  <td align="left">'.$schedule_date.'</td>

  </tr>

  <tr >

  <td align="left"><b>Trainer Name</b></td>

  <td align="left">'.$trainer_name.'</td>

  </tr>

  </table>

  </div>

  </div>

  </td>

  </tr>



  </table>

  </div>

  <div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">

  <table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tbody>

  <tr>

  <td height="65">

  </tr>

  <tr>

  <td  style="border-bottom:1px solid #DDDDDD;"></td>

  </tr>

  <tr><td height="25"></td></tr>

  <tr>

  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tbody>

  <tr>

  <td valign="top" class="specbundle"><div class="contentEditableContainer contentTextEditable">

  <div class="contentEditable" align="center">

  <p  style="text-align:left;color:#CCCCCC;font-size:12px;font-weight:normal;line-height:20px;">

  <span style="font-weight:bold;">'.$blog_title.'</span>

  <br>

  '.$admin_email.'

  <br>

  </p>

  </div>

  </div></td>

  <td valign="top" width="30" class="specbundle">&nbsp;</td>

  <td valign="top" class="specbundle"><table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tbody>

  </tbody>

  </table>

  </td>

  </tr>

  </tbody>

  </table>

  </td>

  </tr>

  <tr><td height="88"></td></tr>

  </tbody>

  </table>



  </div>

  <!-- =============================== footer ====================================== -->



  </td>

  </tr>

  </tbody>

  </table>

  </td>

  <td valign="top" width="40">&nbsp;</td>

  </tr>

  </tbody>

  </table>

  </td>

  </tr>

  </tbody>

  </table>

  </td>

  </tr>

  </tbody>

  </table>

  ';

  /* sent notification to admin */





  $mail_subject = 'Schedule Notification'; 

  $message = $html;

  $subject= $mail_subject;

  $sender =  $admin_email;

  $user_email = $email;

  $admin_mail = $admin_email;

  $headers[] = 'MIME-Version: 1.0' . "\r\n";

  $headers[] = 'Content-type: text/html; charset=utf-8' . "\r\n";

  $headers[] = "X-Mailer: PHP \r\n";

  $headers[] = 'From: '.$sender.' < '.$admin_email.'>' . "\r\n";

  add_filter( 'wp_mail_content_type', 'schedule_quiz_set_content_type' );



  //echo $mail_subject;

  $u_mail = wp_mail( $user_email, $subject, $message, $headers );

  $a_mail = wp_mail( $admin_mail, $subject, $message, $headers );

  if($a_mail)

  {

      $json['msg'] = "Mail Send Successfully";

      $json['error'] = false;

      echo json_encode($json);

  } 

  else 

  {

   $json['msg'] = "Failed";

   $json['error'] = false;

   echo json_encode($json);

}



die();

}



function schedule_quiz_set_content_type( $content_type ) {

    return 'text/html';

}



if(!function_exists('vvd_append_schedule'))

{

  function vvd_append_schedule()

  {

    if(isset($_POST['data']) && !empty($_POST['data']))
    {
        $date_arr = $_POST['data'];

        $today_date = $date_arr[0];

        $week_date_new = end($date_arr);
        $current_user_id = get_current_user_id();


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

        }


        if(!empty($schedule_data) && count($schedule_data) > 0)
        {
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
        }
        else
        {
            $new_date = $schedule_date_new[0];
            $html = '';
            $html = '
                    <div class="today_dt text-center-" id="div-'.$new_date.'">
                        <h4 id="" class="current_date">'.date('l, F dS',strtotime($new_date)).'</h4>
                        <center style="padding: 15px 0px;">No Classes Found</center>
                    </div>
            ';
            echo $html;
        }
    }

    if(isset($_POST['current_date']) && !empty($_POST['current_date']))
    {
        $current_date = date('m/d/Y',strtotime($_POST['current_date']));

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

                       'value' => $current_date,

                       'compare' => '=',

                   )

               )

            );

        $schedule_arr = new WP_Query($args);
        $postids = $schedule_arr->posts;

        if(!empty($postids))
        {
            $schedule_data[$current_date] = array(
                'post_ids' => $postids,
            );
        }
        if(!empty($schedule_data) && count($schedule_data) > 0)
        {
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
        }
        else
        {
            $current_date = date('m/d/Y',strtotime($_POST['current_date']));
            $html = '';

            $html = '
                <div class="today_dt text-center-" id="div-'.$current_date.'">
                    <h4 id="" class="current_date">'.date('l, F dS',strtotime($current_date)).'</h4>
                    <center style="padding: 15px 0px;">No Classes Found</center>
                </div>
            ';
            echo $html;
        }
    }

    exit();

  }

  add_action( 'wp_ajax_vvd_append_schedule', 'vvd_append_schedule' );

  add_action( 'wp_ajax_nopriv_vvd_append_schedule', 'vvd_append_schedule' );

}



?>