<?php

namespace WPGraphQL\Extensions\ACF;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Extensions\ACF\Utils as ACFUtils;

class Filters {

	/**
	 * Filters the GraphQL root query fields, to add entry points for ACF
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public static function acf_root_query_field_groups( $fields ) {

		/**
		 * Setup the root query fields for ACF
		 */
		$fields['fieldGroups'] = [
			'type'        => \WPGraphQL\Types::list_of( Types::field_group_type() ),
			'description' => __( 'Field Groups defined by Advanced Custom Fields', 'wp-graphql-acf' ),
			'resolve'     => function ( $root, array $args, AppContext $context, ResolveInfo $info ) {
				return acf_get_field_groups();
			},
		];

		return $fields;

	}

	public static function acf_get_flexible_content_value( $value, $post_id, $field ) {
		return ACFUtils::_transform_flexible_layout_value($value, $field['layouts']);
	}

	/**
	 * Adds a "graphql_label" to each field when acf_get_fields() is called
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public static function acf_get_fields( $fields ) {

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $fields;
		}

		foreach ( $fields as $key => $field ) {

			$graphql_label                   = ACFUtils::_graphql_label( $field['name'] );
			$fields[ $key ]['graphql_label'] = $graphql_label . 'Field';

			if( $fields[$key]['type'] == 'flexible_content' ) {
				foreach($fields[$key]['layouts'] as $lkey => $layout ) {

					$graphql_label                   				 = ACFUtils::_graphql_label( $layout['name'] );
					$fields[$key]['layouts'][$lkey]['graphql_label'] = $graphql_label;

				}
			}

		}

		return $fields;
	}

	/**
	 * Adds a "graphql_label" to each field type that's returned when acf_get_field_types() is called
	 *
	 * @param $types
	 *
	 * @return array
	 */
	public static function acf_field_types( $types ) {

		if ( empty( $types ) || ! is_array( $types ) ) {
			return $types;
		}

		foreach ( $types as $type_key => $type ) {

			$graphql_label                       = ACFUtils::_graphql_label( $type['name'] );
			$types[ $type_key ]['graphql_label'] = $graphql_label . 'Field';

		}

		return $types;

	}
}
