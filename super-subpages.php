<?php
/**
* Plugin Name: Super Subpages
* Plugin URI: http://jessemorgan.me/super-subpages
* Description: A simple plugin for displaying subpages of the current page, with the options for depth and list style. Can be used as a widget, placed anywhere in a template file using <code>superSubpages()</code>, or inserting the shortcode <code>[super_subpages]</code> on any page and custom styles can be applied using the class <code>super-subpages</code>.
* Version: 1.1
* Author: Jesse Morgan
* Author URI: http://jessemorgan.me/
* License: GPL12
*/

// can be added as a widget under Appearance > widgets
// or
// placed in a tempolate file using the function: displaySubpages()

// add subpage display options to nav
add_action( 'admin_menu', 'super_subpages_nav' );
function super_subpages_nav() {
add_menu_page('Super Subpages Options', 'Super Subpages Options', 10, 'super-subpages-options', 'super_subpages_options_page'/*, '/wp-content/themes/smp/images/note.png'*/);
	//call register settings function
	//add_action( 'admin_init', 'custom_nav_settings');
}


// check for user permissions, else, continue and build the page
function super_subpages_options_page(){
	if(!current_user_can('manage_options')){
		wp_die( __('You do not have sufficient permissions to access this page.'));
	}

    global $wpdb;

	// check to see if table exists. if not, create it and insert deafult values
	$sql = "SHOW TABLES LIKE 'super_subpages'";
	$results = $wpdb->get_results($sql, ARRAY_N);
	if(!$results){

		$charset_collate = $wpdb->get_charset_collate();

		mysql_query("CREATE TABLE super_subpages (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			levels int(9) NOT NULL,
            style_one VARCHAR(160) NOT NULL,
            style_two VARCHAR(160) NOT NULL,
            style_three VARCHAR(160) NOT NULL,
            style_four VARCHAR(160) NOT NULL,
			UNIQUE KEY id (id)
			) $charset_collate;");

        mysql_query("INSERT INTO super_subpages (id,levels,style_one,style_two,style_three,style_four) VALUES('','1','disc','','','')");

    }

    if(!empty($_POST['update-super-subpages-submit'])){

		$styles = $_POST['style'];

		foreach($styles as $style){
			$cleanStyles[] = sanitize_text_field(preg_replace('/[0-9]+/', '', str_replace(' ', '-', strtolower($style))));
		}
		/*echo '<pre>';
		var_dump($cleanStyles);
		echo '</pre>';*/

		$levelsDeep = absint(intval($_POST['pages-deep']));
		if($levelsDeep < 5 && $levelsDeep > 0){
	        $wpdb->update(
	        	'super_subpages',
	        	array(
	        		'levels' => $levelsDeep,
	                'style_one'  => $cleanStyles[0],
	                'style_two'  => $cleanStyles[1],
	                'style_three'  => $cleanStyles[2],
	                'style_four'  => $cleanStyles[3]
	        	),
	            array( 'id' => 1 ));
	            echo '<div class="updated"><p>Settings updated successfully!</p></div>';
		}
    }

    $getCurrentOptions = $wpdb->get_results( "SELECT * FROM super_subpages WHERE id = 1");

    $levelCount = $getCurrentOptions[0]->levels;

?>

	<div class="wrap">
		<h2>&Congruent; Super Subpages Options</h2>
	</div>

    <form method="post" action="" id="super-subpages-form">
        <label for="pages-deep">How many levels deep?</label>
            <input type="number" name="pages-deep" id="pages-deep" min="1" max="4" value="<?php echo esc_attr($getCurrentOptions[0]->levels);?>" /><br />
<?php
    for($i = 1; $i <= $levelCount; $i++){
        $level = array('','style_one','style_two','style_three','style_four');
?>
    <label for="subpages-list-style-<?php echo $i; ?>">List display style for level <?php echo $i; ?></label>
        <select name="style[]" id="subpages-list-style-<?php echo $i; ?>">
            <option value="<?php echo esc_attr($getCurrentOptions[0]->$level[$i]);?>"><?php echo esc_html(ucwords(str_replace('-',' ',$getCurrentOptions[0]->$level[$i])));?></option>
            <option value="armenian">Armenian</option>
            <option value="circle">Circle</option>
			<option value="disc">Disc</option>
            <option value="cjk-ideographic">Ideographic</option>
            <option value="decimal">Decimal</option>
            <option value="decimal-leading-zero">Decimal Leading Zero</option>
            <option value="georgian">Georgian</option>
            <option value="hebrew">Hebrew</option>
            <option value="hiragana">Hiragana</option>
            <option value="hiragana-iroha">Hiragana Iroha</option>
            <option value="katakana">Katakana</option>
            <option value="katakana-iroha">Katakana Iroha</option>
            <option value="lower-alpha">Lower Alpha</option>
            <option value="lower-greek">Lower Greek</option>
            <option value="lower-roman">Lower Roman</option>
            <option value="none">None</option>
            <option value="square">Square</option>
            <option value="upper-alpha">Upper Alpha</option>
            <option value="upper-roman">Upper Roman</option>
        </select><br />
<?php
    }
?>
            <p><small><a href="#" class="style-descriptions-link">List style descriptions</a></small></p>
            <div class="style-descriptions" style="display:none;">
                <dl>
                    <dt>Disc</dt>
                        <dd>Default value. The marker is a filled circle</dd>
                    <dt>Armenian</dt>
                        <dd>The marker is traditional Armenian numbering</dd>
                    <dt>Circle</dt>
                        <dd>The marker is a circle</dd>
                    <dt>Ideographic</dt>
                        <dd>The marker is plain ideographic numbers</dd>
                    <dt>Decimal</dt>
                        <dd>The marker is a number</dd>
                    <dt>Decimal Leading Zero</dt>
                        <dd>The marker is a number with leading zeros (01, 02, 03, etc.)</dd>
                    <dt>Georgian</dt>
                        <dd>The marker is traditional Georgian numbering</dd>
                    <dt>Hebrew</dt>
                        <dd>The marker is traditional Hebrew numbering</dd>
                    <dt>Hiragana</dt>
                        <dd>The marker is traditional Hiragana numbering</dd>
                    <dt>Hiragana Iroha</dt>
                        <dd>The marker is traditional Hiragana iroha numbering</dd>
                    <dt>Katakana</dt>
                        <dd>The marker is traditional Katakana numbering</dd>
                    <dt>Katakana Iroha</dt>
                        <dd>The marker is traditional Katakana iroha numbering</dd>
                    <dt>Lower Alpha</dt>
                        <dd>The marker is lower-alpha (a, b, c, d, e, etc.)</dd>
                    <dt>Lower Greek</dt>
                        <dd>The marker is lower-greek</dd>
                    <dt>Lower Roman</dt>
                        <dd>The marker is lower-roman (i, ii, iii, iv, v, etc.)</dd>
                    <dt>None</dt>
                        <dd>No marker is shown</dd>
                    <dt>Square</dt>
                        <dd>The marker is a square</dd>
                    <dt>Upper Alpha</dt>
                        <dd>The marker is upper-alpha (A, B, C, D, E, etc.)</dd>
                    <dt>Upper Roman</dt>
                        <dd>The marker is upper-roman (I, II, III, IV, V, etc.)</dd>
                </dl>
            </div>
        <div class="clear"></div>
        <input type="submit" class="button-primary" name="update-super-subpages-submit" value="Update Options" style="margin-top:10px;" />
    </form>
<style>
    .clear {
        clear:both;
        height:.01em;
    }
    .style-descriptions dl {
        width:100%;
        max-width:600px;
    }
    .style-descriptions dl dt, .style-descriptions dl dd { float:left; margin:0; padding:0; border-bottom:1px solid rgba(0,0,0,.3); }
    .style-descriptions dl dt {
        width:33.3%;
    }
    .style-descriptions dl dd {
        width:66.6%;
    }
</style>
<script>
    jQuery(document).ready(function($){

        $('.style-descriptions-link').click(function(e){
            e.preventDefault();
            $('.style-descriptions').slideToggle();
        });

    });
</script>
<?php

}




// Creating the widget
class wpb_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'wpb_widget',

// Widget name will appear in UI
__('Super Subpages', 'wpb_widget_domain'),

// Widget description
array( 'description' => __( 'Display subpages of current page', 'wpb_widget_domain' ), )
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// run the code and display the output
//echo __( 'Hello, World!', 'wpb_widget_domain' );
global $wpdb;
$getCurrentOptions = $wpdb->get_results( "SELECT * FROM super_subpages WHERE id = 1");

$sidelinks = wp_list_pages("title_li=&echo=0&depth=".$getCurrentOptions[0]->levels."&child_of=".$post->ID);

//$levelCount = $getCurrentOptions[0]->levels;
?>
<style>
	.super-subpages { list-style-type:<?php echo $getCurrentOptions[0]->style_one; ?>; }
<?php
	if($getCurrentOptions[0]->style_two != ''){
		echo '.super-subpages li ul { list-style-type:'.$getCurrentOptions[0]->style_two.'; }';
	}
	if($getCurrentOptions[0]->style_three != ''){
		echo '.super-subpages li ul li ul { list-style-type:'.$getCurrentOptions[0]->style_three.'; }';
	}
	if($getCurrentOptions[0]->style_four != ''){
		echo '.super-subpages li ul li ul li ul { list-style-type:'.$getCurrentOptions[0]->style_four.'; }';
	}
?>
</style>
<ul class="super-subpages">
  <?php echo $sidelinks; ?>
</ul>
<?php
echo $args['after_widget'];
}

// Widget Backend
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Subpages', 'wpb_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );




function superSubpages(){
    global $wpdb;
    $getCurrentOptions = $wpdb->get_results( "SELECT * FROM super_subpages WHERE id = 1");

    $sidelinks = wp_list_pages("title_li=&echo=0&depth=".$getCurrentOptions[0]->levels."&child_of=".$post->ID);

    //$levelCount = $getCurrentOptions[0]->levels;
    ?>
	<style>
		.super-subpages { list-style-type:<?php echo $getCurrentOptions[0]->style_one; ?>; }
	<?php
		if($getCurrentOptions[0]->style_two != ''){
			echo '.super-subpages li ul { list-style-type:'.$getCurrentOptions[0]->style_two.'; }';
		}
		if($getCurrentOptions[0]->style_three != ''){
			echo '.super-subpages li ul li ul { list-style-type:'.$getCurrentOptions[0]->style_three.'; }';
		}
		if($getCurrentOptions[0]->style_four != ''){
			echo '.super-subpages li ul li ul li ul { list-style-type:'.$getCurrentOptions[0]->style_four.'; }';
		}
	?>
	</style>
    <ul class="super-subpages">
		<?php echo $sidelinks; ?>
    </ul>
<?php
}

add_shortcode('super_subpages','superSubpages');

?>
