<?php
namespace WPGraphQL\Extensions\ACF;

class Utils {

	/**
	 * Utility function for formatting a string to be compatible with GraphQL labels (camelCase with lowercase first letter)
	 *
	 * @param $input
	 *
	 * @return mixed|string
	 */
	public static function _graphql_label( $input ) {

		$graphql_label = str_ireplace( '_', ' ', $input );
		$graphql_label = ucwords( $graphql_label );
		$graphql_label = str_ireplace( ' ', '', $graphql_label );
		$graphql_label = lcfirst( $graphql_label );

		return $graphql_label;

	}

	public static function _transform_flexible_layout_value( $value, $layouts ) {

		if( !is_array($value) ) return [];

		foreach($value as $key => $value_layout) {
			foreach($layouts as $lkey => $layout) {
				if( $value_layout['acf_fc_layout'] == $layout['name'] ) {
					foreach( $layout['sub_fields'] as $skey => $sub_field ) {
						if ( isset($value[$key][$sub_field['name']]) ) {
							$val = $value[$key][$sub_field['name']];
							$value[$key][$sub_field['name']] = $sub_field;
							$value[$key][$sub_field['name']]['value'] = $val;
						}
					}
				}
			}
			$value[$key]['graphql_label'] = self::_graphql_label($value[$key]['acf_fc_layout']);
		}
		return $value;
	}

}
