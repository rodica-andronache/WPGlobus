<?php
/**
 * @package   WPGlobus
 * @copyright Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WPGlobus_Config' ) ) {
	return;
}

/**
 * WPGlobus_Config class
 */
class WPGlobus_Config {

	/**
	 * Plugin name
	 */
	const GLOBUS_PLUGIN_NAME = 'wpglobus';

	/**
	 *    Url mode: query (questionmark)
	 */
	const GLOBUS_URL_QUERY = 1;

	/**
	 *    Url mode: pre-path
	 */
	const GLOBUS_URL_PATH = 2;

	/**
	 *    Url mode: pre-domain
	 */
	const GLOBUS_URL_DOMAIN = 3;

	/**
	 * Current language
	 * @var string
	 */
	public $language = 'en';

	/**
	 * Language by default
	 * @var string
	 */
	public $default_language = 'en';

	/**
	 * Enabled languages
	 * @var array
	 */
	public $enabled_languages = array(
		'en',
		'ru',
		'de'
	);

	/**
	 * Hide from URL language by default
	 * @var bool
	 */
	public $hide_default_language = true;

	/**
	 * URL mode
	 * query || pre-path || pre-domain
	 * @var int
	 */
	public $url_mode;

	/**
	 *    URL information
	 * @var array
	 */
	public $url_info = array();

	/**
	 *    Flag images configuration
	 *    Look in /flags/ directory for a huge list of flags for usage
	 * @var array
	 */
	public $flag = array();

	/**
	 * Location of flags (needs trailing slash!)
	 * 'plugins/globus/flags/';
	 * @var string
	 */
	public $flags_url = '';

	/**
	 * Stores languages in  pairs code=>name
	 * @var array
	 */
	public $language_name = array();

	/**
	 * Stores languages names in English
	 * @var array
	 */
	public $en_language_name = array();

	/**
	 * Stores locales
	 * @var array
	 */
	public $locale = array();

	/**
	 * Use flag name for navigation menu : 'name' || 'code' || ''
	 * @var string
	 */
	public $show_flag_name = 'code';

	/**
	 * Use navigation menu by slug
	 * for use in all nav menu set value to ''
	 * @var string
	 */
	public $nav_menu = '';

	/**
	 * Custom CSS
	 * @var string
	 */
	public $custom_css = '';

	/**
	 * WPGlobus option key
	 * @var string
	 */
	public $option = 'wpglobus_option';

	/**
	 * WPGlobus option key for $language_name
	 * @var string
	 */
	public $option_language_names = 'wpglobus_option_language_names';

	/**
	 * WPGlobus option key for $en_language_name
	 * @var string
	 */
	public $option_en_language_names = 'wpglobus_option_en_language_names';

	/**
	 * WPGlobus option key for $locale
	 * @var string
	 */
	public $option_locale = 'wpglobus_option_locale';


	/**
	 * WPGlobus option key for $flag
	 * @var string
	 */
	public $option_flags = 'wpglobus_option_flags';

	/**
	 * @var string
	 */
	public $css_editor = '';

	/**
	 * Constructor
	 */
	function __construct() {

		add_action( 'plugins_loaded', array(
			$this,
			'on_load_textdomain'
		) );

		$this->_get_options();
	}

	/**
	 * Load textdomain
	 * @return void
	 */
	function on_load_textdomain() {
		load_plugin_textdomain( 'wpglobus', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Return URL mode
	 * @int
	 */
	function get_url_mode() {
		return $this->url_mode;
	}

	/**
	 * Set flag URL
	 * @return void
	 */
	function _set_flags_url() {

		//		$this->flags_url = plugins_url(self::GLOBUS_PLUGIN_NAME . '/flags/');
		$this->flags_url = plugins_url( basename( dirname( dirname( __FILE__ ) ) ) ) . '/flags/';
	}

	/**
	 *    Set languages by default
	 */
	function _set_languages() {
		// Names for languages in the corresponding language, add more if needed
		$this->language_name['en'] = "English";
		$this->language_name['ru'] = "Русский";
		$this->language_name['de'] = "Deutsch";
		$this->language_name['zh'] = "中文";
		$this->language_name['fi'] = "Suomi";
		$this->language_name['fr'] = "Français";
		$this->language_name['nl'] = "Nederlands";
		$this->language_name['sv'] = "Svenska";
		$this->language_name['it'] = "Italiano";
		$this->language_name['ro'] = "Română";
		$this->language_name['hu'] = "Magyar";
		$this->language_name['ja'] = "日本語";
		$this->language_name['es'] = "Español";
		$this->language_name['vi'] = "Tiếng Việt";
		$this->language_name['ar'] = "العربية";
		$this->language_name['pt'] = "Português";
		$this->language_name['pl'] = "Polski";
		$this->language_name['gl'] = "Galego";

		$this->en_language_name['en'] = "English";
		$this->en_language_name['ru'] = "Russian";
		$this->en_language_name['de'] = "German";
		$this->en_language_name['zh'] = "Chinese";
		$this->en_language_name['fi'] = "Finnish";
		$this->en_language_name['fr'] = "French";
		$this->en_language_name['nl'] = "Dutch";
		$this->en_language_name['sv'] = "Swedish";
		$this->en_language_name['it'] = "Italian";
		$this->en_language_name['ro'] = "Romanian";
		$this->en_language_name['hu'] = "Hungarian";
		$this->en_language_name['ja'] = "Japanese";
		$this->en_language_name['es'] = "Spanish";
		$this->en_language_name['vi'] = "Vietnamese";
		$this->en_language_name['ar'] = "Arabic";
		$this->en_language_name['pt'] = "Portuguese";
		$this->en_language_name['pl'] = "Polish";
		$this->en_language_name['gl'] = "Galician";

		#Locales
		$this->locale['en'] = "en_US";
		$this->locale['ru'] = "ru_RU";
		$this->locale['de'] = "de_DE";
		$this->locale['zh'] = "zh_CN";
		$this->locale['fi'] = "fi";
		$this->locale['fr'] = "fr_FR";
		$this->locale['nl'] = "nl_NL";
		$this->locale['sv'] = "sv_SE";
		$this->locale['it'] = "it_IT";
		$this->locale['ro'] = "ro_RO";
		$this->locale['hu'] = "hu_HU";
		$this->locale['ja'] = "ja";
		$this->locale['es'] = "es_ES";
		$this->locale['vi'] = "vi";
		$this->locale['ar'] = "ar";
		$this->locale['pt'] = "pt_BR";
		$this->locale['pl'] = "pl_PL";
		$this->locale['gl'] = "gl_ES";

		#flags
		$this->flag['en'] = 'gb.png';
		$this->flag['ru'] = 'ru.png';
		$this->flag['de'] = 'de.png';
		$this->flag['zh'] = 'cn.png';
		$this->flag['fi'] = 'fi.png';
		$this->flag['fr'] = 'fr.png';
		$this->flag['nl'] = 'nl.png';
		$this->flag['sv'] = 'se.png';
		$this->flag['it'] = 'it.png';
		$this->flag['ro'] = 'ro.png';
		$this->flag['hu'] = 'hu.png';
		$this->flag['ja'] = 'jp.png';
		$this->flag['es'] = 'es.png';
		$this->flag['vi'] = 'vn.png';
		$this->flag['ar'] = 'arle.png';
		#$this->flag['ar'] = 'argm.jpg';
		$this->flag['pt'] = 'br.png';
		$this->flag['pl'] = 'pl.png';
		$this->flag['gl'] = 'galego.png';

	}

	/**
	 * Set default options
	 * @return void
	 */
	function _set_default_options() {

		update_option( $this->option_language_names, $this->language_name );
		update_option( $this->option_en_language_names, $this->en_language_name );
		update_option( $this->option_locale, $this->locale );
		update_option( $this->option_flags, $this->flag );

	}

	/**
	 * Get options from DB and wp-config.php
	 * @return void
	 */
	function _get_options() {

		$wpglobus_option = get_option( $this->option );

		/*
		 * FIX: after "Reset All" Redux options we must reset all WPGlobus options
		 * first of all look at $wpglobus_option['more_languages']
		 */
		if ( isset( $wpglobus_option['more_languages'] ) && is_array( $wpglobus_option['more_languages'] ) ) {

			$wpglobus_option = array();
			delete_option( $this->option );
			delete_option( $this->option_language_names );
			delete_option( $this->option_en_language_names );
			delete_option( $this->option_locale );
			delete_option( $this->option_flags );

		}

		/*
		 * Get enabled languages and default language ( just one main language )
		 */
		if ( isset( $wpglobus_option['enabled_languages'] ) && ! empty( $wpglobus_option['enabled_languages'] ) ) {
			$this->enabled_languages = array();
			foreach ( $wpglobus_option['enabled_languages'] as $lang => $value ) {
				if ( ! empty( $value ) ) {
					$this->enabled_languages[] = $lang;
				}
			}

			/** get first language in $this->enabled_languages for default language */
			reset( $wpglobus_option['enabled_languages'] );
			$this->default_language = key( $wpglobus_option['enabled_languages'] );
		}
		/** check WPGLOBUS_DEFAULT_LANGUAGE defined in wp-config.php */
		if ( defined( 'WPGLOBUS_DEFAULT_LANGUAGE' ) ) {
			$this->default_language = WPGLOBUS_DEFAULT_LANGUAGE;
			if ( ! in_array( $this->default_language, $this->enabled_languages ) ) {
				array_unshift( $this->enabled_languages, $this->default_language );
			}
		}

		/*
		 *
		 *
		 */
		$this->_set_flags_url();

		/*
		 * Get URL mode
		 */
		if ( isset( $wpglobus_option['url_mode'] ) && ! empty( $wpglobus_option['url_mode'] ) ) {
			$this->url_mode = $wpglobus_option['url_mode'];
		}
		else {
			$this->url_mode = self::GLOBUS_URL_PATH;
		}
		/** @todo make loading url_mode from wp-config.php */

		/*
		 * Get languages name
		 * big array of used languages
		 */
		$this->language_name = get_option( $this->option_language_names );

		if ( empty( $this->language_name ) ) {

			$this->_set_languages();
			$this->_set_default_options();

		}

		/*
		 * Get locales
		 */
		$this->locale = get_option( $this->option_locale );

		/*
		 * Get en_language_name
		 */
		$this->en_language_name = get_option( $this->option_en_language_names );

		/*
		 * Get option 'show_flag_name'
		 */
		if ( isset( $wpglobus_option['show_flag_name'] ) ) {
			$this->show_flag_name = $wpglobus_option['show_flag_name'];
		}
		if ( defined( 'WPGLOBUS_SHOW_FLAG_NAME' ) ) {
			if ( 'name' === WPGLOBUS_SHOW_FLAG_NAME ) {
				$this->show_flag_name = 'name';
			}
			elseif ( false === WPGLOBUS_SHOW_FLAG_NAME || '' === WPGLOBUS_SHOW_FLAG_NAME ) {
				$this->show_flag_name = '';
			}
		}

		/*
		 * Get navigation menu slug for add flag in front-end 'use_nav_menu'
		 */
		$this->nav_menu = '';
		if ( isset( $wpglobus_option['use_nav_menu'] ) ) {
			$this->nav_menu = ( $wpglobus_option['use_nav_menu'] == 'all' ) ? '' : $wpglobus_option['use_nav_menu'];
		}
		if ( defined( 'WPGLOBUS_USE_NAV_MENU' ) ) {
			$this->nav_menu = WPGLOBUS_USE_NAV_MENU;
		}

		/*
		 * Get custom CSS
		 */
		if ( isset( $wpglobus_option['css_editor'] ) ) {
			$this->css_editor = $wpglobus_option['css_editor'];
		}


		/*
		 *
		 */
		$option = get_option( $this->option_flags );
		if ( ! empty( $option ) ) {
			$this->flag = $option;
		}

	}

	/**
	 * Hard-coded enabled url modes
	 * @return array
	 */
	function _getEnabledUrlMode() {
		$enabled_url_mode = array(
			self::GLOBUS_URL_QUERY => 'URL query',
			self::GLOBUS_URL_PATH  => 'URL path'
		);
		return $enabled_url_mode;
	}

} // end of class WPGlobus_Config

# --- EOF