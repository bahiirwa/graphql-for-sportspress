<?php
/**
 * Initializes a singleton instance of WP_GraphQL_SportsPress
 *
 * @package WPGraphQL\SportsPress
 * @since 0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_GraphQL_SportsPress' ) ) {
	
	class WP_GraphQL_SportsPress {
		
		/**
		 * Initialize the functions when instance of class is called.
		 *
		 * @return void
		 */
		public static function instance() {
			add_action( 'register_post_type_args', [ __CLASS__, 'wp_graphql_sportspress_post_types' ], 10, 2  );
			add_filter( 'register_taxonomy_args', [ __CLASS__, 'wp_graphql_sportspress_taxonomies' ], 10, 2 );
			add_action( 'graphql_register_types', [ __CLASS__, 'wp_graphql_sportspress_post_meta' ] );
		}
	
		// sp_player, sp_event, sp_team, sp_staff
		public static function wp_graphql_sportspress_post_meta() {

			register_graphql_field( 
				'Event', 
				'matchday', 
				[
					'type' => 'String',
					'description' => __( 'The color of the post', 'wp-graphql' ),
					'resolve' => function( $post ) {
					$color = get_post_meta( $post->ID, 'sp_day', true );
					return ! empty( $color ) ? $color : 'blue';
					}
				]
			);

			//Register connection
			register_graphql_connection(
				[
					'fromType' 		=> 'Event',
					'toType'		=> 'Club',
					'fromFieldName' => 'Clubs', //Name of the field - can be whatever
					'resolve'		=> function( $event, $args, $context, $info ){
											$connection = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver( $event, $args, $context, $info, 'sp_team' );
											$clubs = get_post_meta( $event->ID, 'sp_team', false );
											// wp_send_json( $clubs );
											$connection->setQueryArg( 'post_parent', 0 );
											$connection->setQueryArg( 'post__in', $clubs );
											return $connection->get_connection();
					}
				]
			);

		}

		//Register connection
		public static function wp_graphql_sportspress_post_types( $args, $post_type ) {

			if ( 'sp_player' === $post_type ) {
				$args['show_in_graphql'] = true;
				$args['description'] = 'SportsPress Exposed Player Custom Post Type';
				$args['graphql_single_name'] = 'Player';
				$args['graphql_plural_name'] = 'Players';
			}
			
			if ( 'sp_event' === $post_type ) {
				$args['show_in_graphql'] = true;
				$args['graphql_single_name'] = 'Event';
				$args['graphql_plural_name'] = 'Events';
			}
			
			if ( 'sp_team' === $post_type ) {
				$args['show_in_graphql'] = true;
				$args['graphql_single_name'] = 'Club';
				$args['graphql_plural_name'] = 'Clubs';
			}
			
			if ( 'sp_staff' === $post_type ) {
				$args['show_in_graphql'] = true;
				$args['graphql_single_name'] = 'Staff';
				$args['graphql_plural_name'] = 'StaffPersons';
			}

			return $args;

		}

		/**
		 * Add Taxonomies from SportsPress Soccer
		 *
		 * @param sp_position $taxonomy
		 * @param sp_list $taxonomy
		 * @param sp_list $taxonomy
		 * @return void
		 */
		public static function wp_graphql_sportspress_taxonomies( $args, $taxonomy ) {
	
			if ( 'sp_position' === $taxonomy ) {
				$args['show_in_graphql'] = true;
				$args['graphql_single_name'] = 'PlayerPosition';
				$args['graphql_plural_name'] = 'PlayerPositions';
			}
	
			if ( 'sp_list' === $taxonomy ) {
				$args['show_in_graphql'] = true;
				$args['graphql_single_name'] = 'PlayerList';
				$args['graphql_plural_name'] = 'PlayerLists';
			}
	
			return $args;
		}
	
	}
}