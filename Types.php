<?php
namespace WPGraphQL\Extensions\Acf;


use WPGraphQL\Extensions\Acf\Type\AcfBasic\AcfBasicType;

class Types
{
    private static $acf;

    public static function acf() {
        return self::$acf ?: (self::$acf = new AcfBasicType());
    }
}