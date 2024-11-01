<?php

/*
 * Plugin Name: WP details
 * Version: 1.1
 * Plugin URI: https://wordpress.org/plugins/wp-details
 * Description: Display system & wordpress information for support and troubleshooting. 
 * Tags: support, Maintanance, System Check, Details, WPorb, Information, System, Site Info, System Info, Info, Theme Info, Theme Information, Site Check 
 * Author: WPorb
 * Author URI: https://wporb.com
 * Requires at least: 4.0        
 * Tested up to: 5.1
 * License: GPL2
 * Text Domain: bsi
 * Domain Path: /lang/
 *
 */

class Bbtech_SI {

    public $version = '1.1';
    public $db_version = '1.1';
    protected static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct() {

        $this->init_actions();

        $this->define_constants();
        spl_autoload_register(array($this, 'autoload'));
        // Include required files
       
        
        register_activation_hook(__FILE__, array($this, 'install'));
        //Do some thing after load this plugin
        
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        
        do_action('bsi_loaded');
    }

   

    function install() {
        
    }

    function init_actions() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
       
    }

    
 
 

    function autoload($class) {
        $name = explode('_', $class);
        if (isset($name[1])) {
            $class_name = strtolower($name[1]);
            $filename = dirname(__FILE__) . '/class/' . $class_name . '.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }

    public function define_constants() {

        $this->define('BSI_VERSION', $this->version);
        $this->define('BSI_DB_VERSION', $this->db_version);
        $this->define( 'BSI_PATH', plugin_dir_path( __FILE__ ) );
        $this->define('BSI_URL', plugins_url('', __FILE__));
    }

    public function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
    
    
       function load_textdomain() {
        load_plugin_textdomain( 'bsi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }
    
    
    static function admin_scripts() { 
         
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'bsi_admin', plugins_url( 'assets/js/script.js', __FILE__ ), '', false, true );
        wp_localize_script( 'bsi_admin', 'BSI_Vars', array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'nonce'         => wp_create_nonce( 'bsi_nonce' ), 
            'pluginURL'     => BSI_URL,
             
        ) ); 
        
        wp_enqueue_style( 'bsi_admin', plugins_url( '/assets/css/style.css', __FILE__ ) );
        
        wp_enqueue_style( 'dashicons' );
        do_action( 'bsi_admin_scripts' );
    }

    
     function admin_menu() {
        $capability = 'read'; //minimum level: subscriber
        
        add_submenu_page('tools.php',
                        __( 'WP details', 'bsi' ), 
                        __( 'WP details', 'bsi' ),
                        $capability, 'wp_details', array( $this, 'wp_details_view' ) );
         
        do_action( 'bsi_admin_menu', $capability, $this );
    }
    
    function wp_details_view() {
        require ( BSI_PATH . '/view/status.php' );
    }
    

}


function bsi() {
    return Bbtech_SI::instance();
}
//bsi instance.
$bsi = bsi();

 /**
   * The code that runs during plugin activation.
   */
  function activate_bw_dev_info_bar() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bw-dev-info-bar-activator.php';
    Bw_Dev_Info_Bar_Activator::activate();
  }

  /**
   * The code that runs during plugin deactivation.
   *
   * @since    1.1
   */
  function deactivate_bw_dev_info_bar() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bw-dev-info-bar-deactivator.php';
    Bw_Dev_Info_Bar_Deactivator::deactivate();
  }

  register_activation_hook( __FILE__, 'activate_bw_dev_info_bar' );
  register_deactivation_hook( __FILE__, 'deactivate_bw_dev_info_bar' );


  /**
   * The core plugin class
   */
  require plugin_dir_path( __FILE__ ) . 'includes/class-bw-dev-info-bar.php';

  /**
   * Begins execution of the plugin.
   *
   * @since    1.0.0
   */
  function run_bw_dev_info_bar() {

    new Bw_Dev_Info_Bar();

  }

  run_bw_dev_info_bar();

