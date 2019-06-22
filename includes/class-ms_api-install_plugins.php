<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_install_plugins {

	public function __construct() {

	}

	public function replace_plugin( $plugin_slug, $plugin_zip, $old_plugin_slug = '' ) {
		if ( $this->is_plugin_installed( $plugin_slug ) ) {
			$this->upgrade_plugin( $plugin_slug );
			$installed = true;
		} else {
			$installed = $this->install_plugin( $plugin_zip );
		}

		if ( ! is_wp_error( $installed ) && $installed ) {
			$activate = activate_plugin( $plugin_slug );

			if ( is_null( $activate ) && ! empty( $old_plugin_slug ) ) {
				deactivate_plugins( array( $old_plugin_slug ) );
			}

		}
	}

	public function is_plugin_installed( $slug ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();
		if ( ! empty( $all_plugins[ $slug ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_plugin_installed_notice($plugin_slug) {
		$all_plugins = get_plugins();
		if ( ! empty( $all_plugins[ $plugin_slug ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function install_plugin( $plugin_zip ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		wp_cache_flush();

		$upgrader  = new Plugin_Upgrader();
		$installed = $upgrader->install( $plugin_zip );

		return $installed;
	}

	public function upgrade_plugin( $plugin_slug ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		wp_cache_flush();

		$upgrader = new Plugin_Upgrader();
		$upgraded = $upgrader->upgrade( $plugin_slug );

		return $upgraded;
	}
}