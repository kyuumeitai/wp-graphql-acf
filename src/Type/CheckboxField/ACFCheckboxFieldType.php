<?php

namespace WPGraphQL\Extensions\ACF\Type\ACFCheckboxFieldType;

class ACFCheckboxFieldType {
	private static $fields;

	public static function fields( $fields ) {

		if ( null === self::$fields ) {
			self::$fields = [];
		}

		self::$fields[ $type['graphql_label'] ] = function () use ( $type ) {

			$fields = [
				'id'       => [
					'type'        => Types::non_null( Types::id() ),
					'description' => __( 'The global ID for the field', 'wp-graphql-acf' ),
					'resolve'     => function ( array $field, array $args, AppContext $context, ResolveInfo $info ) {
						return ( ! empty( $field['ID'] ) && absint( $field['ID'] ) ) ? Relay::toGlobalId( self::$type_name, $field['ID'] ) : null;
					},
				],
				'label'    => [
					'type'        => Types::non_null( Types::string() ),
					'description' => __( 'This is the name which will appear on the EDIT page', 'wp-graphql-acf' ),
				],
				'value'    => [
					'type'    => Types::string(),
					'resolve' => function ( array $field ) {
						return get_field( $field['key'], $field['object_id'], true );
					},
				],
//					'order' => [],
				'required' => [
					'type' => Types::boolean(),
				],
				'key'      => [
					'type' => Types::string(),
				],
				'class'    => [
					'type' => Types::string(),
				],
				// @todo: Add conditional logic
				'group'    => [
					'type'        => ACFTypes::field_group_type(),
					'description' => __( 'The field group this field is part of', 'wp-graphql-acf' ),
					'resolve'     => function ( array $field ) {
						$field_group = acf_get_field_group( $field['parent'] );

						return ! empty( $field_group ) ? $field_group : null;
					},
				],
			];

			return $fields;

		};

		return ! empty( self::$fields[ $type['graphql_label'] ] ) ? self::$fields[ $type['graphql_label'] ] : null;

	}
}