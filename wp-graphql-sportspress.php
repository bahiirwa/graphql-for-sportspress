<?php
/**
 * Plugin Name: WPGraphQL for SportPress
 * Plugin URI: http://omukiguy.com
 * Author Name: Laurence bahiirwa
 * Author URI: https://omukiguy.com
 * Description: Expose the SportsPress sports Data to GraphQL Endpoint - Access sports data via domain.com/graphql
 * Version: 0.1.0
 * text-domain: wp-graphql-sportspress
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * WPGraphQL requires at least: 0.8.0+
 *
 * @package     WPGraphQL\SportsPress
 * @author      bahiirwa
 * @license     GPL-3
 * 
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setups WPGraphQL SportsPress constants
 */
function wp_graphql_sportspress_constants() {
	// Plugin version.
	if ( ! defined( 'WPGRAPHQL_SPORTPRESS_VERSION' ) ) {
		define( 'WPGRAPHQL_SPORTPRESS_VERSION', '0.4.4' );
	}
	// Plugin Folder Path.
	if ( ! defined( 'WPGRAPHQL_SPORTPRESS_PLUGIN_DIR' ) ) {
		define( 'WPGRAPHQL_SPORTPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
	// Plugin Folder URL.
	if ( ! defined( 'WPGRAPHQL_SPORTPRESS_PLUGIN_URL' ) ) {
		define( 'WPGRAPHQL_SPORTPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}
	// Plugin Root File.
	if ( ! defined( 'WPGRAPHQL_SPORTPRESS_PLUGIN_FILE' ) ) {
		define( 'WPGRAPHQL_SPORTPRESS_PLUGIN_FILE', __FILE__ );
	}
	// Whether to autoload the files or not.
	if ( ! defined( 'WPGRAPHQL_SPORTPRESS_AUTOLOAD' ) ) {
		define( 'WPGRAPHQL_SPORTPRESS_AUTOLOAD', true );
	}
}

/**
 * Checks if WPGraphQL WooCommerce required plugins are installed and activated
 */
function wp_graphql_sportspress_dependencies_not_ready() {
	$deps = array();
	if ( ! class_exists( '\WPGraphQL' ) ) {
		$deps[] = 'WPGraphQL';
	}
	if ( ! class_exists( '\SportsPress' ) ) {
		$deps[] = 'SportsPress';
	}

	return $deps;
}

/**
 * Initializes WPGraphQL WooCommerce
 */
function wp_graphql_sportspress_init() {
	wp_graphql_sportspress_constants();

	$not_ready = wp_graphql_sportspress_dependencies_not_ready();
	if ( empty( $not_ready ) ) {
		require_once WPGRAPHQL_SPORTPRESS_PLUGIN_DIR . 'includes/class-wp-graphql-sportspress.php';
		return WP_GraphQL_SportsPress::instance();
	}

	foreach ( $not_ready as $dep ) {
		add_action(
			'admin_notices',
			function() use ( $dep ) {
				?>
				<div class="error notice is-dismissible">
					<p>
						<?php
							printf(
								/* translators: dependency not ready error message */
								esc_html__( '%1$s must be active for "WPGraphQL SportsPress" to work', 'wp-graphql-sportspress' ),
								esc_html( $dep )
							);
						?>
					</p>
				</div>
				<?php
			}
		);
	}

	return false;
}
add_action( 'graphql_init', 'wp_graphql_sportspress_init' );
