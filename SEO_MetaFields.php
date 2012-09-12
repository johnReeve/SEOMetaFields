<?php
/*
Plugin Name: Basic SEO Meta Fields
Plugin URI: http://johnreeve.us
Description: Adds a meta box to the pages, posts, and custom post types which allows some very basic metadata entry, plus a simple admin page to set defaults for these as well as a function to output them at an appropriate point in the theme. 

Version: 1.0

Version History:

1.0 Created basic functionality

NOTES:

This is setup to output the fields from a function called 

get_SimpleSEO($fieldType);

This takes the field type as a string [ "title" | "description" | "keyword"] and should be placed in an appropriate spot in the theme's headers.

*/

function reeveSEO_add_admin_meta_boxes() {

	// add this to all the public post types, but only for admins-- becasue we don't want other roles (manf, in the original case) to edit them:
	if ( current_user_can( $GLOBALS['post_type_object']->cap->edit_others_posts ) ) {
		$post_types = get_post_types(array("public" => true));
		foreach ($post_types as $post_type) {
			add_meta_box("simple-SEO-fields", __("Simple SEO Fields", "simple-seo-fields"), "reeveSEO_metaboxFields", $post_type, "normal", "default");
		}
	}
	
}
add_action('add_meta_boxes', 'reeveSEO_add_admin_meta_boxes');


// A function to create the custom field box in admin:
function reeveSEO_metaboxFields ($post) {
	
	// setup the fields:  
	
	$title = get_post_meta($post->ID, '_simpleSEO_title', true);
	$description = get_post_meta($post->ID, '_simpleSEO_description', true);
	$keywords = get_post_meta($post->ID, '_simpleSEO_keywords', true);
	
	?>
	<p>Here are some simple SEO Fields that will be echoed into the appropriate positions if they are set.</p>
	<p>The fields are not validated... in fact they are echoed straight into the page header between some quotation marks.  So use appropriate caution.</p>
	
	<p class="meta_description">Page Title</p>
	<p><input name="simpleSEO_title" type="text" size="100" value="<?php echo $title; ?>" /></p>
		
	<p class="meta_description">Description</p>
	<textarea class="large-text" cols="50" rows="6" name="simpleSEO_description" placeholder="Enter the SEO Description"><?php echo $description; ?></textarea>
	
	
	<p class="meta_description">Keywords</p>
	<textarea class="large-text" cols="50" rows="6" name="simpleSEO_keywords" placeholder="Enter the SEO Keywords"><?php echo $keywords; ?></textarea>
	
	
	<?php 
	
}


// a function to handle the product save:
function reeveSEO_save_product($post_id) {

	// Check for autosaves
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || (defined('DOING_AJAX') && DOING_AJAX) )
		return;
	
	// Check post type and caps
	if ( !current_user_can( 'edit_product', $post_id ) )
		return;
	
	
	$title = $_POST['simpleSEO_title'];
	if($title) {
		update_post_meta($post_id, '_simpleSEO_title', $title);
	} else {
		delete_post_meta($post_id, '_simpleSEO_title');
	}
	
	$description = $_POST['simpleSEO_description'];
	if($description) {
		update_post_meta($post_id, '_simpleSEO_description', $description);
	} else {
		delete_post_meta($post_id, '_simpleSEO_description');
	}
	
	$keywords = $_POST['simpleSEO_keywords'];
	if($keywords) {
		update_post_meta($post_id, '_simpleSEO_keywords', $keywords);
	} else {
		delete_post_meta($post_id, '_simpleSEO_keywords');
	}
	
}

add_action('save_post', 'reeveSEO_save_product');

// add some functions to spit these out into a tempalte:

function get_SimpleSEO( $fieldType="NULL" ) {
	// setup the fields:
	
	global $post;
	
	$title = get_post_meta($post->ID, '_simpleSEO_title', true);
	$description = get_post_meta($post->ID, '_simpleSEO_description', true);
	$keywords = get_post_meta($post->ID, '_simpleSEO_keywords', true);
	
	switch ($fieldType) { 
	case $fieldType == "title" :
		if ($title == ""){
			return  wp_title( '|', false, 'right' ) . " " . get_option("simpleSEO_default_title");
		} else {
			return $title;
		}
		break;	
	case $fieldType == "description" :
		if ($description == ""){
			return get_option("simpleSEO_default_description");
		} else {
			return $description;
		}
		break;	
	case $fieldType == "keywords" :
		if ($keywords == ""){
			return get_option("simpleSEO_default_keywords");;
		} else {
			return $keywords;
		}
		break;	
	default:
		return "";
	}
}


// Here we setup some defaults that will be accessible via the WP Admin Area::
// create custom plugin settings menu

add_action('admin_menu', 'reeveSEO_create_menu');
function reeveSEO_create_menu() {
	//create new top-level menu
	add_menu_page('Simple SEO Plugin Settings', 'Simple SEO', 'administrator', __FILE__, 'reeveSEO_settings_page' );

	//call register settings function
	add_action( 'admin_init', 'reeveSEO_register_settings' );
}


function reeveSEO_register_settings() {
	//register our settings
	register_setting( 'reeveSEO-settings-group', 'simpleSEO_default_title' );
	register_setting( 'reeveSEO-settings-group', 'simpleSEO_default_keywords' );
	register_setting( 'reeveSEO-settings-group', 'simpleSEO_default_description' );
}

function reeveSEO_settings_page() {
	
	if (!current_user_can('manage_options'))
	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	?>
	<div class="wrap">
	<h2>Simple SEO</h2>
	
	<form method="post" action="options.php">
	    <?php settings_fields( 'reeveSEO-settings-group' ); ?>
	    <?php // do_settings("reeveSEO_settings_page", 'reeveSEO-settings-group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Default Page Title<br><small>(appends to the normal wp_title() )</small></th>
	        <td><input type="text" name="simpleSEO_default_title" value="<?php echo get_option('simpleSEO_default_title'); ?>" size="120" /></td>
	        </tr>
	         
	        <tr valign="top">
	        <th scope="row">Default Keywords</th>
	        <td><input type="text" name="simpleSEO_default_keywords" value="<?php echo get_option('simpleSEO_default_keywords');?>" size="120" /></td>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row">Default Description</th>
	        <td><input type="text" name="simpleSEO_default_description" value="<?php echo get_option('simpleSEO_default_description'); ?>" size="120" /></td>
	        </tr>
	    </table>
	    
	    <p class="submit">
	    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>
	
	</form>
	</div>
<?php } 
