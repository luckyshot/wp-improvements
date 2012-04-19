<?php 
/*
Plugin Name: Wordpress Improvements
Plugin URI: http://xaviesteve.com/
Description: Wordpress Improvements is a suite of fixes and optimizations curated and selected by Senior Wordpress developer Xavi Esteve. The plugin includes several improvements for Wordpress such as: displays useful technical information on pages and posts to aid in developing Wordpress themes and complex setups, increase security against CLRF and other attacks, simplify and optimize the WYSIWYG for SEO purposes and non-technical people.
Author: Xavi Esteve
Version: 1.2.2
Author URI: http://xaviesteve.com/
*/

// Enable or disable improvements here
$wpimp_settings = array(
	'dev_helper' 		=> true, // displays useful technical information on pages and posts to aid in developing Wordpress themes and complex setups
	'seo_wysiwyg' 	=> true, // simplify and optimize the WYSIWYG for SEO purposes and non-technical people
	'security' 			=> true, // increase security against CLRF and other attacks
);


/**
 * Dev Helper
 * Displays useful technical information on pages and posts to aid in developing Wordpress themes and complex setups
 */
if ($wpimp_settings['dev_helper']) {
	add_action('wp_head', 'wpimp_head');
	add_action('wp_footer', 'wpimp_footer');

	function wpimp_head() {
		if (is_user_logged_in()) {
			echo '
		<style type="text/css">
			#adminwidget {background:#fff;font-size:10px;bottom:10px;right:5px;position:fixed;padding:10px;border:1px dashed #ccc;margin:5px;height:1em;overflow:hidden;width:80px;filter:alpha(opacity=30);-moz-opacity:0.3;-khtml-opacity: 0.3;opacity: 0.3;border-radius:10px;text-align:left;}
			#adminwidget:hover {height:auto;width:auto;filter:alpha(opacity=100);-moz-opacity:1;-khtml-opacity:1;opacity:1;}
			#adminwidget p {margin:0;line-height:1.2em;}
			#adminwidget p:first-child {color:#c00;margin:0 0 .6em 0;text-align:center;}
			#adminwidget a {color:#0085d5;}
		</style>
			';
		}else{
			echo '<style type="text/css">
				#adminwidget_login {bottom:0;display:block;height:5px;width:5px;right:0;position:absolute;}
				</style>
			';
		}
	}

	function wpimp_footer() {
		if ($wpimp_settings['dev_helper'] && is_user_logged_in()) {
			global $post;
			
			$output = "";
			$output .= '
			<div id="adminwidget">
				<p class="center"><strong>Dev Helper</strong></p>
				<p>ID: <strong>'.$post->ID.'</strong><br />
				Type: <strong>';
		
			if (is_single()) { $output .= "Post"; }
			if (is_page()) { $output .= "Page"; }
			if (is_category()) { $output .= "Category"; }
			if (is_tag()) { $output .= "Tag"; }
			if (is_tax()) { $output .= "Tax"; }
			if (is_author()) { $output .= "Author"; }
			
			if (is_archive()) { $output .= "Archive"; }
			if (is_date()) { $output .= " - Date"; }
			if (is_year()) { $output .= " (year)"; }
			if (is_month()) { $output .= " (monthly)"; }
			if (is_day()) { $output .= " (daily)"; }
			if (is_time()) { $output .= " (time)"; }
			
			if (is_search()) { $output .= "Search"; }
			if (is_404()) { $output .= "404"; }
			if (is_paged()) { $output .= " (Paged)"; }
	
			if (isset($post->ID)) {
					$output .= '</strong><br />
				Template: <strong>'.get_post_meta($post->ID,'_wp_page_template',true).'</strong><br />
				Order: '.$post->menu_order.'</p>';
			}
		
		// <a href="'.get_bloginfo('url').'/wp-admin/profile.php">Profile</a>	
		$output .= '
			<p>
				<a href="'.get_bloginfo('url').'/wp-admin/post.php?post='.$post->ID.'&action=edit&message=1">Edit this</a> / 
				<a href="'.get_bloginfo('url').'/wp-admin/" class="strong">WP admin</a> / 
				<a href="'.wp_logout_url(adminhelper_currenturl()).'">Logout</a>
			</p>
	</div>
	';
	
		}else{
			$output .= '<a id="adminwidget_login" title="Login to Wordpress" href="'.get_bloginfo('url').'/wp-login.php?redirect_to='.urlencode(wpimp_currenturl()).'"></a>';
		}
		echo $output;
	}
}



/**
 * SEO WYSIWYG
 * simplify and optimize the WYSIWYG for SEO purposes and non-technical people
 */
if ($wpimp_settings['seo_wysiwyg']) {
	add_filter("mce_buttons", "extended_editor_mce_buttons", 0);
	add_filter("mce_buttons_2", "extended_editor_mce_buttons_2", 0);
	add_filter('tiny_mce_before_init', 'extended_editor_change_mce_buttons', 0);

	function extended_editor_mce_buttons($buttons) {
		return array(
			"formatselect", "separator", 
			"bold", "italic", "separator",
			"bullist", "numlist", "blockquote", "separator",
			"link", "unlink", "separator",
			"charmap", "separator", 
			"pasteword", "separator",
			"fullscreen", "separator",
		);
	}
	
	function extended_editor_mce_buttons_2($buttons) {
	// the second toolbar line
	return array();
	}
	
	function extended_editor_change_mce_buttons( $initArray ) {
		$initArray['theme_advanced_blockformats'] = 'p,h2,h3,h4,h5,h6,pre';
		return $initArray;
	}
}




/**
 * Security
 * increase security against attacks
 */
if ($wpimp_settings['security']) {
	if (
		strpos($_SERVER['REQUEST_URI'], "eval(") ||
		strpos($_SERVER['REQUEST_URI'], "base64") ||
		strpos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
		strpos($_SERVER['REQUEST_URI'], "CONCAT")
	) {
		@header("HTTP/1.1 400 Bad Request");
		@header("Status: 400 Bad Request");
		@header("Connection: Close");
		@exit;
  }
}







// Helper functions
function wpimp_currenturl() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}


