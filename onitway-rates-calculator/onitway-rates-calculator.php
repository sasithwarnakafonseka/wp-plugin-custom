<?php
/**
 * Plugin Name: Onitway Rates Calculator
 * Plugin URI:  https://onitway.com
 * Description: This plugin lets you add several custom post types in your WordPress.
 * Version:     1.1.1
 * Author:      Onitway
 * Author URI:  https://onitway.com
 * Text Domain: onitway-rates-calculator
 */

if ( !class_exists('Onitway_Rates_Calculator_Type_Plus') ) :

    class Onitway_Rates_Calculator_Type_Plus
    {

        /**
         * The single instance of the class.
         */
        private static $_instance = null;

        /**
         * Main onitway_rates_calculator_type_plus Instance.
         *
         * @see onitway_rates_calculator_type_plus_instance()
         * @return onitway_rates_calculator_type_plus - Main instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function __construct()
        {
			/*
			 *  Path to classes folder in Plugin
			 */
			
			define('ONITWAY_RATES_CALCULATOR_PATH', plugin_dir_path(__FILE__) );
			define('ONITWAY_RATES_CALCULATOR_INCLUDES_PATH', plugin_dir_path(__FILE__) . 'includes/');
			define('ONITWAY_RATES_CALCULATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
			
			$this->include_files();

            add_action('plugins_loaded', array($this, 'plugins_loaded'));
			add_action('init', array($this, 'maybe_flush_rewrite_rules'));
        }

        /**
         * Load plugin textdomain.
         *
         * @access public
         * @return void
         */
        public function plugins_loaded()
        {
            load_plugin_textdomain('custom-post-type-plus', false, basename(dirname(__FILE__)) . '/languages/');
        }

        public function include_files()
        {
			
			include ONITWAY_RATES_CALCULATOR_PATH . 'functions.php';
            /*
            * Include Custom Post Types
            */
			include ONITWAY_RATES_CALCULATOR_INCLUDES_PATH . 'base.php';

            include ONITWAY_RATES_CALCULATOR_INCLUDES_PATH . 'team.php';
            include ONITWAY_RATES_CALCULATOR_INCLUDES_PATH . 'portfolio.php';
            include ONITWAY_RATES_CALCULATOR_INCLUDES_PATH . 'testimonial.php';
            include ONITWAY_RATES_CALCULATOR_INCLUDES_PATH . 'activity.php';
            include ONITWAY_RATES_CALCULATOR_INCLUDES_PATH . 'amenity.php';

        }

		public static function activation() {
			update_option( 'onitway_rates_calculator_type_plus_flush_rewrite_rules', '1' );
		}

		public function maybe_flush_rewrite_rules() {
			if ( get_option( 'onitway_rates_calculator_type_plus_flush_rewrite_rules', '0' ) === '1' ) {
				flush_rewrite_rules();
				delete_option('onitway_rates_calculator_type_plus_flush_rewrite_rules');
			}
		}
		
		public static function get_default_template() {
			return 'templates/default.php';
		}
    }

	/**
	 * Main instance of Onitway_Rates_Calculator_Type_Plus_Instance.
	 *
	 * @since
	 * @return Onitway_Rates_Calculator_Type_Plus
	 */
	function onitway_rates_calculator_type_plus_instance()
	{
		return Onitway_Rates_Calculator_Type_Plus::instance();
	}

	/*
	 * Global for backwards compatibility.
	 */
	$GLOBALS['onitway_rates_calculator_type_plus_instance'] = onitway_rates_calculator_type_plus_instance();

	register_activation_hook( __FILE__, array( 'Onitway_Rates_Calculator_Type_Plus', 'activation' ) );

endif;