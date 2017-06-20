<?php
/**
 * Plugin Name: WP GraphQL ACF
 * Description: Adds registered meta data to objects by leveraging the existing register_meta() information.
 * Author: Toni Main
 * Author URI: https://tonimain.com
 */

namespace WPGraphQL\Extensions\Acf;

/**
 * Get a collection of registered post types and taxonomies
 * then run them through the GraphQL fields filter.
 */
use GraphQL\Language\AST\Type;
use GraphQL\Type\Definition\ObjectType;
use WPGraphQL\Type\WPObjectType;
use WPGraphQL\Types;

add_action( 'graphql_init', function() {

	$post_types = get_post_types();
	$taxonomies = get_taxonomies();
	$all_types  = array_merge(
		$post_types,
		$taxonomies,
		array( 'user' )
	);

	foreach ( $all_types as $type ) {
		add_filter( "graphql_{$type}_fields", function ( $fields ) use ( $type ) {
			return add_meta_fields( $fields, $type );
		} );
	}

} );

/**
 * Adds the meta fields for this $object_type registered using
 * register_meta().
 *
 * @param array $fields
 * @param string $object_type
 * @return array
 * @throws Exception If a meta key is the same as a default key warn the dev.
 */

function add_meta_fields( $fields, $object_type ) {
	$meta_keys = get_field_objects(false);

	if ( ! empty( $meta_keys ) ) {
		foreach ( $meta_keys as $key => $field_args ) {

			if ( isset( $fields[ $key ] ) ) {
				throw new \Exception( sprintf( 'Post meta key "%s" is a reserved word.', $key ) );
			}

			$fields[ $key ] = array(
				'type'        => resolve_meta_type( $field_args['type'] ),
				'description' => $field_args['name'],
				'resolve'     => function( $object ) use ( $object_type, $key, $field_args ) {
					if ( 'post' === $object_type || in_array( $object_type, get_post_types(), true ) ) {
						$acf_field = get_field_object( $field_args['key']);
						return $acf_field['value'];
					}
//					if ( 'term' === $object_type || in_array( $object_type, get_taxonomies(), true ) ) {
//						return get_term_meta( $object->term_id, $key, $field_args['single'] );
//					}
//					if ( 'user' === $object_type ) {
//						return get_user_meta( $object->ID, $key, $field_args['single'] );
//					}
					return '';
				},
			);
		}
	}

	return $fields;
}

/**
 * Resolves REST API types to meta data types.
 *
 * @param \GraphQL\Type\Definition\AbstractType $type
 * @param bool $single
 * @return mixed
 */
function resolve_meta_type( $type, $single = true ) {
	if ( $type instanceof \GraphQL\Type\Definition\AbstractType ) {
		return $type;
	}

//	$imagetype = new ObjectType([
//        'name' => 'Image',
//        'description' => 'Image from acf',
//        'fields' => [
//            'id' => [
//                'type' => Types::non_null(Types::id()),
//                'description' => __( 'The globally unique identifier for the user', 'wp-graphql' ),
//                'resolve' => function () {
//	                return '12';
//                }
//            ],
//            'image_name' => [
//                'type' => Types::string(),
//                'description' => __( 'Image field name from image', 'wp-graphql' ),
//                'resolve' => function() {
//	                return 'Im the awesome image name';
//                }
//            ]
//        ],
//    ]);

	switch ( $type ) {
		case 'integer':
			$type = \WPGraphQL\Types::int();
			break;
        case 'image':
            $type = \WPGraphQL\Types::list_of($imagetype);

		case 'number':
			$type = \WPGraphQL\Types::float();
			break;
		case 'boolean':
			$type = \WPGraphQL\Types::boolean();
			break;
		default:
			$type = apply_filters( "graphql_{$type}_type", \WPGraphQL\Extensions\Acf\Types::acf(), $type );
	}

	return $single ? $type : \WPGraphQL\Types::list_of( $type );
}
