<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       linkedin.com/in/mustafa-shaaban22
 * @since      1.0.0
 *
 * @package    Ms_api
 * @subpackage Ms_api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ms_api
 * @subpackage Ms_api/admin
 * @author     Mustafa Shaaban <mustafashaaban22@gmail.com>
 */
class Ms_api_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->cryptor = new Ms_api_cryptor();

		// Add the plugin menus
//		add_action( 'admin_menu', array($this,'ms_api_menus' ));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ms_api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ms_api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ms_api-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ms_api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ms_api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ms_api-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * The function responsible for adding the plugin menu page
	 */
	public function ms_api_menus() {
		add_menu_page( 'MS API', 'MS API', 'manage_options', 'ms-api-dashboard', array($this,'ms_api_menu_page'));
		add_submenu_page('ms-api-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'ms-api-dashboard',  array($this,'ms_api_menu_page'));
		add_submenu_page('ms-api-dashboard', 'Settings', 'Settings', 'manage_options', 'ms-api-settings',  array($this,'ms_api_sub_menu_page'));
	}

	public function ms_api_menu_page(){
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/public/dashboard-page.php';
	}

	public function ms_api_sub_menu_page(){
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/public/settings-page.php';
	}

	/**
	 * The function fires after the plugin activation
	 * @param $plugin
	 * @param $network_activation
	 */
    public function detect_plugin_activation( $plugin, $network_activation ) {
//        if ('ms_api/ms_api.php' === $plugin) {
//	        $plugin_slug = 'json-api/json-api.php';
//	        $plugin_status = Ms_api_install_plugins::is_plugin_installed_notice($plugin_slug);
//	        if (false === $plugin_status) {
//	        	$plug = $this->cryptor->encrypt('true');
//		        $dashboard = add_query_arg( array(
//			        'page' => 'ms-api-dashboard',
//			        's' => $plug,
//		        ), admin_url() );
//		        wp_redirect($dashboard);
//		        exit();
//	        }
//
//        }
    }


}
