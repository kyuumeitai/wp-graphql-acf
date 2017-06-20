<?php

namespace WPGraphQL\Extensions\Acf\Type\AcfBasic;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\Type\WPObjectType;
use WPGraphQL\Types;

class AcfBasicType extends WPObjectType {
    private static $type_name;

    private static $fields;

    public function __construct(array $config)
    {
        self::$type_name = 'acf';

        $config = [
            'name' => self::$type_name,
            'fields' => self::$fields,
            'description' => esc_html('Displaying basic advanced custom fields for our objects'),
        ];

        parent::__construct($config);
    }

    private static function fields() {
        if (null === self::$fields) :
            self::$fields = function () {
                $fields = [
                    'id' => [
                        'type' => Types::non_null(Types::id()),
                        'description' => __('Unique field id for the custom field', 'wp-graphql'),
                        'resolve' => function(\WP_Post $post, $args, $context, ResolveInfo $info) {
                            $fieldObject = get_field_object($post->ID);
                            return $fieldObject['ID'];
                        }
                    ],
                ];
                return self::prepare_fields($fields, self::$type_name);
            };
        endif;

        return self::fields;
    }

}