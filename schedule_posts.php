<?php 
    add_action( 'init', 'schedule_init' );

/**
 * Register a Blog post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function schedule_init() {
  $labels = array(
    'name'               => _x( 'Schedules', 'post type general name', 'treadfitt' ),
    'singular_name'      => _x( 'Schedule', 'post type singular name', 'treadfitt' ),
    'menu_name'          => _x( 'Schedules', 'admin menu', 'treadfitt' ),
    'name_admin_bar'     => _x( 'Schedule', 'add new on admin bar', 'treadfitt' ),
    'add_new'            => _x( 'Add New', 'schedule', 'treadfitt' ),
    'add_new_item'       => __( 'Add New Schedule', 'treadfitt' ),
    'new_item'           => __( 'New Schedule', 'treadfitt' ),
    'edit_item'          => __( 'Edit Schedule', 'treadfitt' ),
    'view_item'          => __( 'View Schedule', 'treadfitt' ),
    'all_items'          => __( 'All Schedules', 'treadfitt' ),
    'search_items'       => __( 'Search Schedules', 'treadfitt' ),
    'parent_item_colon'  => __( 'Parent Schedules:', 'treadfitt' ),
    'not_found'          => __( 'No Schedules found.', 'treadfitt' ),
    'not_found_in_trash' => __( 'No Schedules found in Trash.', 'treadfitt' )
  );

  $args = array(
    'labels'             => $labels,
                'description'        => __( 'Description.', 'treadfitt' ),
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'schedules' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'menu_icon'      => 'dashicons-welcome-write-blog',
    'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt')
  );

  register_post_type( 'schedule', $args );
}

// Register Post type trainers
if(!function_exists('schedule_reg_trainer'))
{
    function schedule_reg_trainer() {
      $labels = array(
        'name'               => _x( 'Trainers', 'post type general name', 'treadfitt' ),
        'singular_name'      => _x( 'Trainer', 'post type singular name', 'treadfitt' ),
        'menu_name'          => _x( 'Trainers', 'admin menu', 'treadfitt' ),
        'name_admin_bar'     => _x( 'Trainer', 'add new on admin bar', 'treadfitt' ),
        'add_new'            => _x( 'Add New', 'trainer', 'treadfitt' ),
        'add_new_item'       => __( 'Add New Trainer', 'treadfitt' ),
        'new_item'           => __( 'New Trainer', 'treadfitt' ),
        'edit_item'          => __( 'Edit Trainer', 'treadfitt' ),
        'view_item'          => __( 'View Trainer', 'treadfitt' ),
        'all_items'          => __( 'All Trainers', 'treadfitt' ),
        'search_items'       => __( 'Search Trainers', 'treadfitt' ),
        'parent_item_colon'  => __( 'Parent Trainers:', 'treadfitt' ),
        'not_found'          => __( 'No Trainers found.', 'treadfitt' ),
        'not_found_in_trash' => __( 'No Trainers found in Trash.', 'treadfitt' )
      );

      $args = array(
        'labels'             => $labels,
                    'description'        => __( 'Description.', 'treadfitt' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'trainers' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'      => 'dashicons-universal-access',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt')
      );

      register_post_type( 'trainer', $args );
    }
    add_action( 'init', 'schedule_reg_trainer' );
}

// create custom plugin settings menu
add_action('admin_menu', 'schedule_plugin_create_menu');

function schedule_plugin_create_menu() {

    //create new top-level menu
    add_menu_page('Schedule Plugin Settings', 'Schedule Settings', 'administrator', __FILE__, 'schedule_plugin_settings_page' , plugins_url('/images/icon.png', __FILE__) );

    //call register settings function
    add_action( 'admin_init', 'register_schedule_plugin_settings' );
}


function register_schedule_plugin_settings() {
    //register our settings
    register_setting( 'schedule-plugin-settings-group', 'activation_key' );
    register_setting( 'schedule-plugin-settings-group', 'map_api_google' );
}

function schedule_plugin_settings_page() {

    $key = get_option('activation_key');
    $activation_key = 'vivid!@#123';
    if(!empty($key) && $key == $activation_key){
        $status = '<label class="description" for="edd_sample_license_key">Activated</label>';
    }else{
        $status = '<label class="description" for="edd_sample_license_key">Invalid license key</label>';
    }
?>
<div class="wrap">
<h1>Schedule</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'schedule-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'schedule-plugin-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Activation Key</th>
        <td><input type="text" name="activation_key" value="<?php echo esc_attr( get_option('activation_key') ); ?>" /><?php echo $status ;?></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Google Map API key</th>
        <td><input type="text" name="map_api_google" value="<?php echo esc_attr( get_option('map_api_google') ); ?>" /></td>
        </tr>

    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>
