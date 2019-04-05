<?php
/**
 * @package schedule
 */
/*
Plugin Name: Schedule Plugin
Plugin URI: 
Description: The ultimate Scheduler plugin for WordPress. Easily add Schedules that users can book through your site using short code [schedule].
Version: 1.1.1
Author: Siteassembly
Author URI: 
Text Domain: schedule
*/

define( 'VIVID_SCHEDULE_DIR', plugin_dir_path( __FILE__ ) );

function schedule_plugin_scripts() {

global $post;
 if( is_a( $post, 'WP_Post' ) && (has_shortcode( $post->post_content, 'schedule') ) ){
     /* css */
    wp_enqueue_style('bootstrap-modal-css',plugins_url('/local-cdns/bootstrap-modal.min.css',__FILE__));
    wp_enqueue_style('bootstrap-modal-bs3-css',plugins_url('/local-cdns/bootstrap-modal-bs3patch.min.css',__FILE__));
    wp_enqueue_style('custom-style-css',plugins_url('/css/custom-style.css',__FILE__));

    /* js */
    wp_enqueue_script('schedule-custom',plugins_url('/js/schedule_script.js',__FILE__));
    wp_enqueue_script('bootstrap-modal-js',plugins_url('/local-cdns/bootstrap-modal.min.js',__FILE__));
    wp_enqueue_script('bootstrap-modal-bs3-js',plugins_url('/local-cdns/bootstrap-modalmanager.min.js',__FILE__));

    wp_localize_script( 'schedule-custom', 'treadfit', array(
        'ajax_url' =>  admin_url("admin-ajax.php") ,
        'templateUrl' => plugins_url(),
        'home_url' => home_url(),
    ) );
}



}
add_action( 'wp_enqueue_scripts', 'schedule_plugin_scripts', 20 );

/**
 * Styling: loading stylesheets for the plugin.
 */
function wpa_styles( $page ) {
	//schedule_script.js
    //wp_enqueue_style( 'schedule-style.css', plugins_url('css/schedule-style.css', __FILE__));

    /* Js File */
    wp_enqueue_script( 'wickedpicker-js', plugins_url('/js/wickedpicker.min.js', __FILE__));
    wp_enqueue_script( 'datepicker-js', plugins_url('/js/datepicker.min.js', __FILE__));

    /* CSS File*/
    wp_enqueue_style( 'wickedpicker-css', plugins_url('/css/wickedpicker.min.css', __FILE__));
    wp_enqueue_style( 'datepicker-css', plugins_url('/css/datepicker.min.css', __FILE__));

}

add_action( 'admin_enqueue_scripts', 'wpa_styles');

require_once( VIVID_SCHEDULE_DIR . 'schedule_shortcode.php' );
require_once( VIVID_SCHEDULE_DIR . 'schedule_settings.php' );
require_once( VIVID_SCHEDULE_DIR . 'schedule_ajax.php' );
require_once( VIVID_SCHEDULE_DIR . 'schedule_posts.php' );

function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}