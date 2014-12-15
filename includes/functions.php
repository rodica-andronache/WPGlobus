<?php

/**
 * Added title fields for enabled languages at post.php page
 */
add_action( 'edit_form_after_title', 'on_title' );
function on_title( $post ) {
	
	if ( ! post_type_supports($post->post_type, 'title') ) {
		return;
	}

	/** @global WPGlobus_Config $WPGlobus_Config */
	global $WPGlobus_Config;

	foreach( $WPGlobus_Config->enabled_languages as $language ) :
	
		if ( $language == $WPGlobus_Config->default_language ) { 
			
			continue; 
		
		} else {	?>	
		
			<div id="titlediv-<?php echo $language;?>">
				<div id="titlewrap-<?php echo $language;?>">
					<label class="screen-reader-text" id="title-prompt-text-<?php echo $language; ?>" for="title_<?php echo $language; ?>"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
					<input type="text" name="post_title_<?php echo $language; ?>" size="30" value="<?php echo esc_attr( htmlspecialchars( __wpg_text_filter($post->post_title, $language) ) ); ?>" id="title_<?php echo $language;?>" autocomplete="off" />
				</div> <!-- #titlewrap -->
				<div class="inside">
					<div id="edit-slug-box-<?php echo $language; ?>" class="hide-if-no-js">
						<b>Slug will be here</b>
					</div>
				</div> <!-- .inside -->
			</div>	<!-- #titlediv -->	<?php				

		}
		
	endforeach;
}

 
/**
 * Join post content for enabled languages in func wp_insert_post
 *
 * @see action in wp-includes\post.php:3326
 */
add_action( 'wp_insert_post_data' , 'on_save_post_data', 10, 2 );
function on_save_post_data($data, $postarr) {

	/** @global WPGlobus_Config $WPGlobus_Config */	
	global $WPGlobus_Config;
	
	$data['post_content'] = trim($data['post_content']);
	if ( !empty($data['post_content']) ) {
		$data['post_content'] = "<!--:{$WPGlobus_Config->default_language}-->" . $data['post_content'] . "<!--:-->";
	}
	
	$data['post_title'] = trim($data['post_title']);
	if ( !empty($data['post_title']) ) {
		$data['post_title'] = "<!--:{$WPGlobus_Config->default_language}-->" . $data['post_title'] . "<!--:-->";
	}	
	
	
	foreach( $WPGlobus_Config->enabled_languages as $language ) :
		if ( $language == $WPGlobus_Config->default_language ) { 
			
			continue; 
		
		} else {
			$content = isset($postarr['content-' . $language]) ? trim($postarr['content-' . $language]) : '';
			if ( !empty($content) ) {
				$data['post_content'] .= "<!--:{$language}-->" . $postarr['content-' . $language] . "<!--:-->";
			}
			
			/**
			 * Join post title for enabled languages
			 */
			$title = isset($postarr['post_title_' . $language]) ? trim($postarr['post_title_' . $language]) : '';
			if ( !empty($title) ) {
				$data['post_title'] .= "<!--:{$language}-->" . $postarr['post_title_' . $language] . "<!--:-->";
			}
			
		}
	endforeach;
	
	return $data;
	
}

/**
 * Added wp_editor for enabled languages at post.php page
 *
 * @see action edit_form_after_editor in wp-admin\edit-form-advanced.php:542
 */
add_action( 'edit_form_after_editor', 'on_add_editors');
function on_add_editors($post) {
	
	if ( ! post_type_supports($post->post_type, 'editor') ) {
		return;
	}
	
	/** @global WPGlobus_Config $WPGlobus_Config */
	global $WPGlobus_Config;

	foreach( $WPGlobus_Config->enabled_languages as $language ) :
		if ( $language == $WPGlobus_Config->default_language ) { 
			
			continue; 
		
		} else {	?>
		
			<div id="postdivrich-<?php echo $language; ?>" class="postarea">	<?php
				wp_editor( __wpg_text_filter($post->post_content, $language ), 'content-' . $language, array(
					'dfw' => true,
					'drag_drop_upload' => true,
					'tabfocus_elements' => 'insert-media-button,save-post',
					'editor_height' => 300,
					'tinymce' => array(
						'resize' => false,
						'wp_autoresize_on' => true,
						'add_unload_trigger' => false,
					),
				) ); ?>
			</div> <?php
		
		}
	endforeach;	
} 



/**
 * Common filters
 */
add_filter( 'the_title', 'wpg_text_filter', 0 );
add_filter( 'the_content', 'wpg_text_filter', 0 );


add_filter( 'wp_title', 'wpg_text_filter', 0 );
add_filter( 'single_post_title', 'wpg_text_filter', 0 );

/**
 * @param string $text
 *
 * @return string
 */
function wpg_text_filter( $text = '' ) {

	/**
	 * @see function qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage
	 */
	if ( empty( $text ) ) {
		// Nothing to do
		return $text;
	}
	
	$text = __wpg_text_filter( $text );
	
	return $text;

}

function __wpg_text_filter( $text = '', $language = '' ) {
	
	
	/**
	 * Fix for case
	 * &lt;!--:en--&gt;Hello world!&lt;!--:--&gt;&lt;!--:ru--&gt;Привет, мир!&lt;!--:--&gt;&lt;!--:de--&gt;Hallo Welt!&lt;!--:--&gt
	 * 
	 * @todo need careful investigation
	 */
	$text = htmlspecialchars_decode($text);

	/** @global string $wpg_default_language */	
	//global $wpg_default_language;
	
	/** @global string $wpg_current_language */	
	//global $wpg_current_language;
	
	global $WPGlobus_Config;	
	
	if ( empty( $text ) ) {
		// Nothing to do
		return $text;
	}
	
	if ( empty( $language ) ) {
		$language = $WPGlobus_Config->language;
	}

	/**
	 * QA
	 */
	//	$text = '<!--:en-->English C<!--:--><!--:ru-->Russian C<!--:-->';
	//	$text = '[:en]English S[:ru]Russian S';
	//	$text = '[:ru]Russian S1[:en]English S1';
	//	$text = '[:ru]Russian S2';
	//	$text = 'Garbage[:en]English S3[:ru]Russian S3';
	//	$text = 'Just заголовок';
	//	$text = "<!--:en-->English\n\n ML<!--:--><!--:ru-->Russian \nML\n<!--:-->";
	//	$text = "[:en]English\n\n ML[:ru]Russian \nML\n<!--:-->";

	/**
	 * qTranslate uses these two types of delimeters
	 * @example
	 * <!--:en-->English<!--:--><!--:ru-->Russian<!--:-->
	 * [:en]English S[:ru]Russian S
	 * The [] delimiter does not have the closing tag, so we will look for the next opening [: or
	 * take the rest until end of end of the string
	 */	
	$possible_delimiters =
		[
			[
				'start' => "<!--:{$language}-->",
				'end'   => '<!--:-->',
			],
			[
				'start' => "[:{$language}]",
				'end'   => '[:',
			],
		];

	/**
	 * We do not know which delimiter was used, so we'll try both, in a loop
	 */
	foreach ( $possible_delimiters as $delimiters ) {
		
		$pos_start = false; 

		/**
		 * Try the starting position. If not found, continue the loop to the next set of delimiters
		 */
		$pos_start = mb_strpos( $text, $delimiters['start'] );
		if ( $pos_start === false ) {
			continue;
		}

		/**
		 * The starting position found..adjust the pointer to the text start
		 * (Do not need mb_strlen here, because we expect delimiters to be Latin only)
		 */
		$pos_start = $pos_start + strlen( $delimiters['start'] );

		/**
		 * Try to find the ending position.
		 * If could not find, will extract the text until end of string by passing null to the `substr`
		 */
		$pos_end = mb_strpos( $text, $delimiters['end'], $pos_start );
		if ( $pos_end === false ) {
			// - Until end of string
			$length = null;
		} else {
			$length = $pos_end - $pos_start;
		}
		
		/**
		 * Extract the text and end the loop
		 */
		$text = mb_substr( $text, $pos_start, $length );
		break;

	}

	if ( false === $pos_start && $language != $WPGlobus_Config->default_language ) {
		$text = __wpg_text_filter($text, $WPGlobus_Config->default_language);	
	}
	
	return $text;

}



add_filter( 'locale', 'wpg_locale', 99 );
function wpg_locale($locale){

	global $WPGlobus_Config;
	
	// try to figure out the correct locale
	/*
	$locale = array();
	$locale[] = $q_config['locale'][$q_config['language']].".utf8";
	$locale[] = $q_config['locale'][$q_config['language']]."@euro";
	$locale[] = $q_config['locale'][$q_config['language']];
	$locale[] = $q_config['windows_locale'][$q_config['language']];
	$locale[] = $q_config['language'];
	
	// return the correct locale and most importantly set it (wordpress doesn't, which is bad)
	// only set LC_TIME as everyhing else doesn't seem to work with windows
	setlocale(LC_TIME, $locale);
	// */

	return $WPGlobus_Config->locale[$WPGlobus_Config->language];
}

add_action('init', 'wpg_init', 2);
function wpg_init() {


	// check if it isn't already initialized
	if( defined('WPGLOBUS_INIT') ) {
		return;
	}	

	define('WPGLOBUS_INIT', true);

	global $WPGlobus_Config;
	
	//wp_redirect('http://wpml2.dev/ru/news/hello-world');
	//exit();	
	
	//wpg_loadConfig();
	/*
	if(isset($_COOKIE['qtrans_cookie_test'])) {
		$q_config['cookie_enabled'] = true;
	} else  {
		$q_config['cookie_enabled'] = false;
	}
	// */
	
	// init Javascript functions
	//qtrans_initJS();
	
	// update Gettext Databases if on Backend
	//if(defined('WP_ADMIN') && $q_config['auto_update_mo']) qtrans_updateGettextDatabases();
	
	// update definitions if neccesary
	//if(defined('WP_ADMIN') && current_user_can('manage_categories')) qtrans_updateTermLibrary();
	
	// extract url information
	//$q_config['url_info'] = wpg_extractURL($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	
	/** @todo check at class-wpglobus.php:103 for set url_info */
	$WPGlobus_Config->url_info = WPGlobus_Utils::extract_url($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	
	//error_log( print_r( $WPGlobus_Config->url_info, true ));
	
	/**
	 * Add hack for support AJAX
	 */
	/*
	if ( defined('DOING_AJAX') && DOING_AJAX && isset( $_SERVER['HTTP_REFERER'] ) ) {
		$referer_info = wpg_parseURL( $_SERVER['HTTP_REFERER'] );
		$q_config['url_info'] = wpg_extractURL(
			$referer_info['path'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	} */
	/* end hack code	*/

	// set test cookie
	//setcookie('qtrans_cookie_test', 'qTranslate Cookie Test', 0, $q_config['url_info']['home'], $q_config['url_info']['host']);
	
	// check cookies for admin
	
	/**
	 * Add hack in 1 line for support AJAX
	 * if(defined('WP_ADMIN')) {}
	 */
	/* 
	if(defined('WP_ADMIN') && !(defined('DOING_AJAX') && DOING_AJAX) ) {
		if(isset($_GET['lang']) && wpg_isEnabled($_GET['lang'])) {
			$q_config['language'] = $q_config['url_info']['language'];
			setcookie('qtrans_admin_language', $q_config['language'], time()+60*60*24*30);
		} elseif(isset($_COOKIE['qtrans_admin_language']) && wpg_isEnabled($_COOKIE['qtrans_admin_language'])) {
			$q_config['language'] = $_COOKIE['qtrans_admin_language'];
		} else {
			$q_config['language'] = $q_config['default_language'];
		}
	} else {
		// $q_config['language'] = $q_config['url_info']['language'];
		$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];
	}
	// */
	
	//$q_config['language'] = apply_filters('qtranslate_language', $q_config['language']);


	/*
	// detect language and forward if needed
	//if($q_config['detect_browser_language'] && $q_config['url_info']['redirect'] && !isset($_COOKIE['qtrans_cookie_test']) && $q_config['url_info']['language'] == $q_config['default_language']) {
		$target = false;
		$prefered_languages = array();
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && preg_match_all("#([^;,]+)(;[^,0-9]*([0-9\.]+)[^,]*)?#i",$_SERVER["HTTP_ACCEPT_LANGUAGE"], $matches, PREG_SET_ORDER)) {
			$priority = 1.0;
			foreach($matches as $match) {
				if(!isset($match[3])) {
					$pr = $priority;
					$priority -= 0.001;
				} else {
					$pr = floatval($match[3]);
				}
				$prefered_languages[$match[1]] = $pr;
			}
			arsort($prefered_languages, SORT_NUMERIC);
			foreach($prefered_languages as $language => $priority) {
				if(strlen($language)>2) $language = substr($language,0,2);
				if(qtrans_isEnabled($language)) {
					if($q_config['hide_default_language'] && $language == $q_config['default_language']) break;
					$target = qtrans_convertURL(get_option('home'),$language);
					break;
				}
			}
		}
		//$target = apply_filters("qtranslate_language_detect_redirect", $target);
		if($target !== false) {
			//error_log( 'target is HERE' );
			wp_redirect($target);
			exit();
		} else {
			//error_log( 'target is FALSE' );
		}
	}
	// */
	
	/*
	// Check for WP Secret Key Missmatch
	global $wp_default_secret_key;
	if(strpos($q_config['url_info']['url'],'wp-login.php')!==false && defined('AUTH_KEY') && isset($wp_default_secret_key) && $wp_default_secret_key != AUTH_KEY) {
		global $error;
		$error = __('Your $wp_default_secret_key is mismatchting with your AUTH_KEY. This might cause you not to be able to login anymore.','qtranslate');
	}
	*/
	
	// Filter all options for language tags
	/*
	if(!defined('WP_ADMIN')) {
		$alloptions = wp_load_alloptions();
		foreach($alloptions as $option => $value) {
			add_filter('option_'.$option, 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage',0);
		}
	} // */
	
	// load plugin translations
	//load_plugin_textdomain('qtranslate', false, dirname(plugin_basename( __FILE__ )).'/lang');
	
	// remove traces of language (or better not?)
	//unset($_GET['lang']);

	
	$_SERVER['REQUEST_URI'] = 	$WPGlobus_Config->url_info['url'];
	$_SERVER['HTTP_HOST']   = 	$WPGlobus_Config->url_info['host'];
	
	// fix url to prevent xss
	//$q_config['url_info']['url'] = qtrans_convertURL(add_query_arg('lang',$q_config['default_language'],$q_config['url_info']['url']));
}

 /*
add_filter( 'the_posts', 'wpg_postsFilter', 0 );
function wpg_postsFilter($posts) {
	if(is_array($posts)) {
		foreach($posts as $post) {
			$post->post_content = __wpg_text_filter($post->post_content);
			
			# @todo make function for translating $post object 	
			#$post = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post);
		}
	}
	return $posts;
} // */