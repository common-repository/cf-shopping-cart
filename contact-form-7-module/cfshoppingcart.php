<?php
/**
** A base module for [cfshoppingcart] and [cfshoppingcart*]
**/

/* Shortcode handler */

wpcf7_add_shortcode( 'cfshoppingcart', 'wpcf7_cfshoppingcart_shortcode_handler', true );
wpcf7_add_shortcode( 'cfshoppingcart*', 'wpcf7_cfshoppingcart_shortcode_handler', true );

function wpcf7_cfshoppingcart_shortcode_handler_ver() {
    return '0.1.1';
}

function wpcf7_cfshoppingcart_shortcode_handler( $tag ) {
    global $wpcf7_contact_form;
    $html = '';

    /*
    if ( ! is_array( $tag ) ) {
        //echo '1';
        return '';
    }
    */
    // print_r($tag);

    $type = $tag['type'];
    $name = $tag['name'];
    $options = (array) $tag['options'];
    //$values = (array) $tag['values'];
    $content = $tag['content'];

    if ( empty( $name ) ) {
        //echo '2';
        return '';
    }

    $atts = '';
    $id_att = '';
    $class_att = '';
    $cols_att = '';
    $rows_att = '';

    if ( 'cfshoppingcart*' == $type )
      $class_att .= ' wpcf7-validates-as-required';

    foreach ( $options as $option ) {
        if ( preg_match( '%^id:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
            $id_att = $matches[1];

        } elseif ( preg_match( '%^class:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
            $class_att .= ' ' . $matches[1];

        } elseif ( preg_match( '%^([0-9]*)[x/]([0-9]*)$%', $option, $matches ) ) {
            $cols_att = (int) $matches[1];
            $rows_att = (int) $matches[2];
        } elseif ( preg_match( '%^(.*?)=(.*)$%', $option, $matches ) ) {
            if (strstr($matches[2], '|')) {
                $cf_opt[$matches[1]] = explode('|', $matches[2]);
            } else {
                $cf_opt[$matches[1]] = $matches[2];
            }
        }
    }

    if ( $id_att )
      $atts .= ' id="' . trim( $id_att ) . '"';

    if ( $class_att )
      $atts .= ' class="' . trim( $class_att ) . '"';

    if ( $cols_att )
      $atts .= ' cols="' . $cols_att . '"';
    else
      $atts .= ' cols="40"'; // default size

    if ( $rows_att )
      $atts .= ' rows="' . $rows_att . '"';
    else
      $atts .= ' rows="10"'; // default size

    // Value
    /*
    if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) && $wpcf7_contact_form->is_posted() ) {
		if ( isset( $_POST['_wpcf7_mail_sent'] ) && $_POST['_wpcf7_mail_sent']['ok'] )
			$value = '';
		else
			$value = $_POST[$name];
	} else {
		$value = $values[0];

		if ( ! empty( $content ) )
			$value = $content;
	}
     */

    //
    if (function_exists('cfshoppingcart_ContactForm7')) {
        // $b is true or false
        list($b, $value) = cfshoppingcart_ContactForm7($cf_opt);
    } else {
        list($b, $value) = 'Function cfshoppingcart_ContactForm7 is not found.';
    }
    $value = esc_html( $value );
    //print_r($cf_opt);
    //echo 'value = ' . $value;

    $html .= '<input type="hidden" name="cfshoppingcart_checkout_data" value="1" />';
    //if (array_key_exists('hidden', $cf_opt)) {
    if (isset($cf_opt['hidden'])) {
        if ($b) {
            $html .= '<input type="hidden" name="' . $name . '"' . $atts . ' value="' . $value . '">';
        } else {
            //echo $value;
            //$html .= '<div class="cfshoppingcart_cart_cf7_msg">' . $value . '</div>';
            $html .= '<input type="hidden" name="' . $name . '"' . $atts . ' value="">';
        }
    } else {
        /*
        if ($b) {
            $html .= '<textarea name="' . $name . '"' . $atts . ' readonly="readonly">' . $value . '</textarea>';
        } else {
            $html .= '<div class="cfshoppingcart_cart_cf7_msg">' . $value . '</div>';
            $html .= '<textarea name="' . $name . '"' . $atts . ' readonly="readonly"></textarea>';
        }
        */
        if ($b) {
            $html .= '<pre name="' . $name . '-textarea">' . $value . '</pre>';
            $html .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
        } else {
            $html .= '<div class="cfshoppingcart_cart_cf7_msg">' . $value . '</div>';
            // $html .= '<pre name="' . $name . '-textarea">' . $value . '</pre>';
            $html .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
        }

    }
    // echo WPCF7_VERSION;

    $validation_error = '';
    if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) )
      $validation_error = $wpcf7_contact_form->validation_error( $name );

    $html = '<span class="wpcf7-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';

    //echo '3: ' . $html;
    return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_cfshoppingcart', 'wpcf7_cfshoppingcart_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_cfshoppingcart*', 'wpcf7_cfshoppingcart_validation_filter', 10, 2 );

function wpcf7_cfshoppingcart_validation_filter( $result, $tag ) {
    global $wpcf7_contact_form;

    $type = $tag['type'];
    $name = $tag['name'];

    $_POST[$name] = (string) $_POST[$name];

    if ( 'cfshoppingcart*' == $type ) {
        if ( '' == $_POST[$name] ) {
            $ver = explode(".", WPCF7_VERSION);
            if ($ver[0] > 4 || ($ver[0] == 4 && $ver[1] >= 1)) {
                // later than version 4.1
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_required') );
            } else {
                // ver 4.0.3
                $result['valid'] = false;
                $result['reason'][$name] = $wpcf7_contact_form->message( 'invalid_required' );
            }
        }
    }

    return $result;
}

