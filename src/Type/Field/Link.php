<?php
namespace WPGraphQL\Extensions\ACF\Type\Field;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Type\WPObjectType;
use WPGraphQL\Types;
use \WPGraphQL\Extensions\ACF\Types as ACFTypes;

class LinkType extends WPObjectType {

	private static $type_name;
	private static $fields;

	public function __construct() {

		self::$type_name = 'link';

		$config = [
			'name' => self::$type_name,
			'description' => __( 'Link data', 'wp-graphql-acf' ),
			'fields' => self::fields(),
			// 'interfaces' => [ self::node_interface() ],
		];

		parent::__construct( $config );

	}

	private static function fields() {

		if ( null === self::$fields ) {
			self::$fields = function() {

				$fields = [
					'url' => [
						'type' => Types::string(),
					],
					'target' => [
						'type' => Types::string(),
					],
					'title' => [
						'type' => Types::string(),
					],
				];
				return self::prepare_fields( $fields, self::$type_name );

			};

		} // End if().

		return ! empty( self::$fields ) ? self::$fields : null;

	}

}
