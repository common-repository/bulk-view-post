<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ajayghaghretiya.wordpress.com/
 * @since             1.0.0
 * @package           bulk-view-post
 *
 * @wordpress-plugin
 * Plugin Name:       Bulk view post
 * Plugin URI:        https://ajayghaghretiya.wordpress.com/bulk-view-post
 * Description:       This plugin provides the bulk view feature in the admin post, product or any custom post types.
 * Version:           1.0.0
 * Author:            Ajay Ghaghretiya
 * Author URI:        https://ajayghaghretiya.wordpress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bulk-view-post
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'bulk_View_POST' ) ) {

	class  bulk_View_POST {

		function __construct() {
			add_action( 'admin_init', array( $this, 'bulk_view_action' ) );
		}

		public function bulk_view_post( $bulk_actions ) {
			$bulk_actions['bulk_view_action'] = esc_html__( 'View', 'domain' );

			return $bulk_actions;
		}

		public function bulk_view_post_action_handler( $redirect_to, $action_name, $post_ids ) {

			if ( 'bulk_view_action' === $action_name ) {
				set_transient( 'bulk_view_posts_ids', $post_ids, 300 ); //5 min
			}

			return $redirect_to;
		}

		public function open_url_new_tab( $url ) {
			?>
			<script type="text/javascript">
                window.open('<?php echo esc_url( $url ); ?>', '_blank');
			</script>
			<?php
		}

		public function bulk_view_action() {
			$args       = array(
				'show_ui' => true,
			);
			$post_types = get_post_types( $args, 'names' );
			if ( ! empty( $post_types ) && is_array( $post_types ) ) {
				foreach ( $post_types as $post_name ) {
					add_filter( "bulk_actions-edit-{$post_name}", array( $this, 'bulk_view_post' ) );
					add_filter( "handle_bulk_actions-edit-{$post_name}", array(
						$this,
						'bulk_view_post_action_handler'
					), 10, 3 );
				}
			}

			$post_ids = get_transient( 'bulk_view_posts_ids' );
			delete_transient( 'bulk_view_posts_ids' );

			if ( false !== $post_ids && ! empty( $post_ids ) && is_array( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					$post_link = get_permalink( $post_id );
					if ( ! empty( $post_link ) ) {
						$this->open_url_new_tab( $post_link );
					}
				}
			}

		}
	}

	new bulk_View_POST();
}
