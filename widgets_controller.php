<?php
/*
Plugin Name: Widgets Controller
Plugin URI: http://wordpress.org/extend/plugins/widgets-controller/
Description: A plugin that give you control for show or hide widgets on WordPress Categories, Posts and Pages.
Author: IndiaNIC
Author URI: http://www.indianic.com
Version: 1.0
*/
add_action('admin_head', 'widgets_controller_head');
function widgets_controller_head() { ?>
	<script type="text/javascript" language="javascript">
		var PLUGINPATH = "<?php echo plugin_dir_url( __FILE__ ); ?>";
	</script>
<?php }
add_filter('widget_display_callback', 'widgets_controller_show');
add_action('in_widget_form', 'widgets_controller_append', 10, 3);
add_filter('widget_update_callback', 'widgets_controller_update', 10, 3);
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'widgets_controller.js', array( 'jquery' ) );
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
//do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] );
//do_action( 'wp_ajax_' . $_POST['action'] );
add_action( 'wp_ajax_nopriv_myajax-submit', 'widgets_controller_submit' );
add_action( 'wp_ajax_myajax-submit', 'widgets_controller_submit' );
add_action('admin_print_styles', 'widgets_controller_css');

/*----------ADD CSS----------*/
function widgets_controller_css() {
    wp_register_style($handle = 'include_css', $src = plugins_url('widgets_controller.css', __FILE__), $deps = array(), $ver = '1.0.0', $media = 'all');
    wp_enqueue_style('include_css');
}
/*----------SHOW WIDGET BY CONDITION----------*/
function widgets_controller_show($instance) {
	if($instance['widgets_controller'] == 1) {
		$cat_id = get_the_category();
		$cat_id = $cat_id[0]->cat_ID;
		$post_id = get_the_ID();
		if(is_home() || is_front_page()) {
			if($instance['general']) { if(in_array("home", $instance['general'])) { return $instance; } }
			else { return false; }
		} elseif(is_404()) {
			if($instance['general']) { if(in_array("error", $instance['general'])) { return $instance; } }
			else { return false; }
		} elseif(is_search()) {
			if($instance['general']) { if(in_array("search", $instance['general'])) { return $instance; } }
			else { return false; }
		} elseif(is_category()) {
			if($instance['category']) { if(in_array($cat_id, $instance['category'])) { return $instance; } }
			else { return false; }
		} elseif(is_single()) {
			if($instance['posts']) { if(in_array($post_id, $instance['posts'])) { return $instance; } }
			else { return false; }
		} elseif(is_page()) {
			if($instance['pages']) { if(in_array($post_id, $instance['pages'])) { return $instance; } }
			else { return false; }
		}
	} else { return $instance; }
}
/*----------APPEND FORM----------*/
function widgets_controller_append($widget, $return, $instance) {
	$instance['widgets_controller'] = isset($instance['widgets_controller']) ? $instance['widgets_controller'] : 0;
	$instance['general'] = isset($instance['general']) ? $instance['general'] : 0;
	$instance['category'] = isset($instance['category']) ? $instance['category'] : 0;
	$instance['posts'] = isset($instance['posts']) ? $instance['posts'] : 0;
	$instance['pages'] = isset($instance['pages']) ? $instance['pages'] : 0;
	$none = $instance['widgets_controller'] == 1 ? "" : " none";
	?>
	<p><input class="checkbox widgets_controller" type="checkbox" <?php checked($instance['widgets_controller'], true) ?> id="<?php echo $widget->get_field_id('widgets_controller'); ?>" name="<?php echo $widget->get_field_name('widgets_controller'); ?>" value="1" />
	<label class="manage_label" for="<?php echo $widget->get_field_id('widgets_controller'); ?>"><?php _e('Widget controller', 'display-widgets') ?></label></p>
	<div class="widgets_controller_box<?php echo $none; ?>">
	<select multiple class="list general" size="3" name="<?php echo $widget->get_field_name('general'); ?>[]">
		<option value="home" <?php if($instance['general']) if(in_array("home", $instance['general'])) { echo "selected"; } ?>>HomePage</option>
		<option value="error" <?php if($instance['general']) if(in_array("error", $instance['general'])) { echo "selected"; } ?>>Error 404</option>
		<option value="search" <?php if($instance['general']) if(in_array("search", $instance['general'])) { echo "selected"; } ?>>Search</option>
	</select>
	<p><input title="<?php if($instance['category']) echo implode(",", $instance['category']); ?>" newtitle="<?php if($instance['posts']) echo implode(",", $instance['posts']); ?>" class="checkbox activecategory" type="checkbox" id="<?php echo $widget->get_field_id('activecategory'); ?>" name="<?php echo $widget->get_field_name('activecategory'); ?>" value="1" />
	<label class="label cat" for="<?php echo $widget->get_field_id('activecategory'); ?>"><?php _e('Category & Posts', 'display-widgets') ?></label></p>
	<select multiple class="list category none" size="5" name="<?php echo $widget->get_field_name('category'); ?>[]"></select>
	<select multiple class="list posts none" size="10" id="<?php echo $widget->get_field_id('posts'); ?>" name="<?php echo $widget->get_field_name('posts'); ?>[]"></select>
	<p><input title="<?php if($instance['pages']) echo implode(",", $instance['pages']); ?>" class="checkbox activepages" type="checkbox" id="<?php echo $widget->get_field_id('activepages'); ?>" name="<?php echo $widget->get_field_name('activepages'); ?>" value="1" />
	<label class="label page" for="<?php echo $widget->get_field_id('activepages'); ?>"><?php _e('Pages', 'display-widgets') ?></label></p>
	<p>
	<select multiple class="list pages none" size="4" id="<?php echo $widget->get_field_id('pages'); ?>" name="<?php echo $widget->get_field_name('pages'); ?>[]"></select>
	</div>
	<?php //echo "<pre>"; print_r($widget); ?>
	<?php //print_r($instance); echo "</pre>"; ?>
<?php }
/*----------UPDATA FORM----------*/
function widgets_controller_update($instance, $new_instance, $old_instance) {
	$instance['widgets_controller'] = isset($new_instance['widgets_controller']) ? $new_instance['widgets_controller'] : 0;
	$instance['general'] = isset($new_instance['general']) ? $new_instance['general'] : 0;
	$instance['activecategory'] = isset($new_instance['activecategory']) ? $new_instance['activecategory'] : 0;
	if($instance['activecategory'] == 1) {
		$instance['category'] = isset($new_instance['category']) ? $new_instance['category'] : 0;
		$instance['posts'] = isset($new_instance['posts']) ? $new_instance['posts'] : 0;
	}
	$instance['activepages'] = isset($new_instance['activepages']) ? $new_instance['activepages'] : 0;
	if($instance['activepages'] == 1) {
		$instance['pages'] = isset($new_instance['pages']) ? $new_instance['pages'] : 0;
	}
	return $instance;
}
/*----------AJAX DATA----------*/
function widgets_controller_submit() {
    $data = $_POST['data'];
    $data = split("-",$data);
	$condition_data = $data[0];
	global $wpdb;
	$cat_post_option = Array();
	if($condition_data == 'getcategory') {
		$current_cat = $data[1];
		$current_cat = isset($current_cat) ? explode(",", $current_cat) : 0;
		$cat_option = "";
		$cat_list = $wpdb->get_results( "SELECT wp_terms.term_id, wp_terms.name FROM wp_term_taxonomy INNER JOIN wp_terms ON wp_term_taxonomy.term_id = wp_terms.term_id WHERE wp_term_taxonomy.taxonomy='category'" );
		foreach($cat_list as $k => $v) {
			$cat_option .= "<option ";
			if($current_cat) if(in_array($v->term_id, $current_cat)) { $cat_option .= "selected "; }
			$cat_option .= "value='{$v->term_id}'>".substr($v->name, 0, 28)." ...{$v->term_id}</option>";
		}
		$current_posts = $data[2];
		$current_posts = isset($current_posts) ? explode(",", $current_posts) : 0;
		$posts_option = "";
		$post_list = $wpdb->get_results( "SELECT * FROM wp_posts where post_type='post' && post_status = 'publish'" );
		foreach($post_list as $k => $v) {
			$cat_id = get_the_category($v->ID);
			$cat_id = $cat_id[0]->cat_ID;
			$posts_option .= "<option title='{$cat_id}' ";
			if($posts_option) if(in_array($v->ID, $current_posts)) { $posts_option .= "selected "; }
			$posts_option .= "value='{$v->ID}'>".substr($v->post_title, 0, 28)." ...{$v->ID}</option>";
		}
		$cat_post_option = $cat_option.'|||'.$posts_option;
		echo $cat_post_option;
	} elseif($condition_data == 'getpages') {
		$current_pages = $data[1];
		$current_pages = isset($current_pages) ? explode(",", $current_pages) : 0;
		$pages_option = "";
		$page_list = $wpdb->get_results( "SELECT * FROM wp_posts WHERE post_type='page' && post_status='publish'" );
		foreach($page_list as $k => $v) {
			$cat_id = get_the_category($v->ID);
			$cat_id = $cat_id[0]->cat_ID;
			$pages_option .= "<option title='{$cat_id}' ";
			if($pages_option) if(in_array($v->ID, $current_pages)) { $pages_option .= "selected "; }
			$pages_option .= "value='{$v->ID}'>".substr($v->post_title, 0, 28)." ...{$v->ID}</option>";
		}
		echo $pages_option;
	}
    exit;
}