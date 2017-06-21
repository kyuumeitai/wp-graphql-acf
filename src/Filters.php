<?php
namespace WPGraphQL\Extensions\ACF;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;

class Filters {

	public static function _graphql_label( $input ) {

		$graphql_label = str_ireplace( '_', ' ', $input );
		$graphql_label = ucwords( $graphql_label );
		$graphql_label = str_ireplace( ' ', '', $graphql_label );
		$graphql_label = lcfirst( $graphql_label );
		return $graphql_label;

	}

	public static function acf_root_query_field_groups( $fields ) {

		$fields['fieldGroups'] = [
			'type' => \WPGraphQL\Types::list_of( Types::field_group_type() ),
			'description' => __( 'Field Groups defined by Advanced Custom Fields', 'wp-graphql-acf' ),
			'resolve' => function( $root, array $args, AppContext $context, ResolveInfo $info ) {
				return acf_get_field_groups();
			},
		];

		return $fields;

	}



	public static function acf_get_fields( $fields ) {

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $fields;
		}

		foreach ( $fields as $key => $field ) {

			$graphql_label = self::_graphql_label( $field['type'] );
			$fields[ $key ]['graphql_label'] = $graphql_label . 'Field';

		}

		return $fields;
	}

	public function acf_field_types( $types ) {

		if ( empty( $types ) || ! is_array( $types ) ) {
			return $types;
		}

		foreach ( $types as $type_key => $type ) {

			$graphql_label = self::_graphql_label( $type['name'] );
			$types[ $type_key ]['graphql_label'] = $graphql_label . 'Field';

		}

		return $types;

	}

}
