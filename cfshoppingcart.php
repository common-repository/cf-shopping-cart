<?php
/*
Plugin Name: Cf Shopping Cart
Plugin URI: http://takeai.silverpigeon.jp/
Description: Placement simply shopping cart to content.
Author: AI.Takeuchi
Version: 0.8.17
Author URI: http://takeai.silverpigeon.jp/
*/

// -*- Encoding: utf8n -*-

/*  Copyright 2009-2011 AI Takeuchi (email: takeai@silverpigeon.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// global
// require_once('module/error_handler.php');
$wpCFShoppingcart = new WpCFShoppingcart();
require_once('module/sum.php');
require_once('module/common.php');
require_once('module/paypal.php');
require_once('module/pnotify.php');
require_once('module/put_shipping.php');


// global
$cfshoppingcart_common = new cfshoppingcart_common();

$plugin_folder = $cfshoppingcart_common->get_plugin_folder();
$plugin_fullpath = $cfshoppingcart_common->get_plugin_fullpath();
$plugin_path = $cfshoppingcart_common->get_plugin_path();
$plugin_uri = $cfshoppingcart_common->get_plugin_uri();

load_plugin_textdomain('cfshoppingcart',
                       $plugin_path . '/lang', $plugin_folder . '/lang');

// Clear cart before action test
/*
function cfshoppingcart_before_clear_cart_test($args) {
    $_SESSION['cfshoppingcart_before_clear_cart_test'] = 'ok';
}
add_action('cfshoppingcart_before_clear_cart', 'cfshoppingcart_before_clear_cart_test');
  */
// Clear cart after sent email
function cfshoppingcart_clear_after_sent_email($cf7) {
    if (function_exists('wpcf7_cfshoppingcart_shortcode_handler_ver')) {
        if (!$_REQUEST['cfshoppingcart_checkout_data']) { return; }
    }

    // Clear cart before action
    //do_action_ref_array('cfshoppingcart_before_clear_cart', array(&$this));
    do_action_ref_array('cfshoppingcart_before_clear_cart', array());

    // do clear cart
    require_once('module/commu.php');
    $commu = new cfshoppingcart_commu();
    $commu->cfshoppingcart_empty_cart();
}
add_action('wpcf7_mail_sent', 'cfshoppingcart_clear_after_sent_email');

/* session start */
// $priority number (8) is less than Contact Form 7
add_action('init', 'cfshoppingcart_init_session_start', 8);
function cfshoppingcart_init_session_start(){
    global $Ktai_Style, $cfshoppingcart_common;
    $cfname = $cfshoppingcart_common->get_session_key();

    if (is_object($Ktai_Style)) {
        if ($Ktai_Style->is_ktai()) {
            ini_set('session.use_cookies','off');
            ini_set('session.use_trans_sid', '1');
        }
    }
    if (!session_id()) {
        session_start();
    }
    if (!function_exists('wpcf7_cfshoppingcart_shortcode_handler') && function_exists('wpcf7_add_shortcode')) {
        //echo 'wpcf7_add_shortcode found.';
        require_once('contact-form-7-module/cfshoppingcart.php');
    }
    unset($_SESSION[$cfname]['no_ajax_msg']);
    // After payment processing when successful
    if (isset($_GET['cfshoppingcart_after_payment_processing'])) {
        if ($_GET['cfshoppingcart_after_payment_processing'] === 'successful') {
            // do clear cart
            require_once('module/commu.php');
            $commu = new cfshoppingcart_commu();
            $commu->cfshoppingcart_empty_cart();
        }
    }
    if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['wp_cfshoppingcart'])) {
        // ajax onload
        //exit();
    } else if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['wp_cfshoppingcart'])) {
        cfshoppingcart_sum();
        //echo $_SERVER['HTTP_X_REQUESTED_WITH'];
        //echo print_r($_POST);
        //exit;
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            // ajax json echo
            @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
            //@header('Content-Type: text/html; charset=' . get_option('blog_charset'));
            //exit;
            //require_once('JSON/JSON.php');
        }
        require_once('module/commu.php');
        $commu = new cfshoppingcart_commu();
        $msg = $commu->cfshoppingcart_main();
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            // ajax json echo
            echo $msg;
            exit;
        } else {
            //print_r($msg);
            $_SESSION[$cfname]['no_ajax_msg'] = $msg;
        }
    }
}


// ritch text editor
// 2011-04-23
function cfshoppingcart_admin_tinymce() { //ok
    wp_enqueue_script('common');
    wp_enqueue_script('jquery-color');
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) add_thickbox();
    wp_print_scripts('media-upload');
    if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
    remove_all_filters('mce_external_plugins');
}

function cfshoppingcart_tiny_mce_before_init($init) {
    $init['plugins'] = str_replace(
        array('wpfullscreen',',,'),
        array('', ','),
        $init['plugins']
        );
    return $init;
}

if (is_admin()) {
    $model = $wpCFShoppingcart->model;

    // Registration of management screen header output function.
    add_action('admin_head', array(&$wpCFShoppingcart, 'addAdminHead'));
    // Registration of management screen function.
    add_action('admin_menu', array(&$wpCFShoppingcart, 'addAdminMenu'));
    add_action('admin_notices', 'cfshoppingcart_action_admin_notices', 5);
} else {
    /* $handle スクリプトの識別名
     * $src(optional) スクリプトファイルへのパス
     * http://で始まるURLまたはサイトルートから絶対パス
     * $deps(optional) 依存するスクリプトのリスト（配列）
     * $ver(optional) スクリプトのバージョン
     */
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery.form', $plugin_uri . '/js/jquery.form.js', array('jquery'), 2.52);
    wp_enqueue_script('jquery.pnotify', $plugin_uri . '/js/jquery.pnotify.min.js', array('jquery'), '1.0.1');
    //
    require_once('module/add_wp_head.php');
    add_action('wp_head', 'cfshoppingcart_add_wp_head');
    require_once('module/add_wp_footer.php');
    add_action('wp_footer', 'cfshoppingcart_add_wp_footer');
    //
    add_action('the_excerpt', 'cfshoppingcart_use_the_content_instead_of_the_excerpt_hook');
    require_once('module/contact-form-7.php');
    require_once('module/function_cfshoppingcart.php');
    require_once('module/show_product.php');
    add_action('the_content', 'show_product');
    // short-code
    require_once('module/cart.php');
    add_shortcode('cfshoppingcart_cart', 'cfshoppingcart_cart');
    //
    require_once('module/cart_link.php');
    add_shortcode('cfshoppingcart_cart_link', 'cfshoppingcart_cart_link');
    //
    //
    require_once('module/send_order_link.php');
    add_shortcode('cfshoppingcart_checkout_link', 'cfshoppingcart_checkout_link');
    // Can use the short-code in sidebar widget
    add_filter('widget_text', 'do_shortcode');
}

function cfshoppingcart_action_admin_notices() {
    if (!function_exists('wpcf7_add_shortcode')) {
        echo '<div id="message" class="updated"><p>' . __("Cf Shopping Cart say: 'wpcf7_add_shortcode' function not found. Let you see 'Module for Contact Form 7' menu of Cf Shopping Cart setting screen, or Could you install Contact Form 7 plugin.",'cfshoppingcart') . '</p></div>';
    }
}


class WpCFShoppingcartModel {
    // member variable
    var $version;// = '0.2.11';
    var $debug;// = '';
    var $custom_fields;
    var $price_field_name;// = 'Price';
    // stock
    var $number_of_stock_field_name;
    var $product_name_field_name;
    var $type_of_show_sold_out_message;
    var $sold_out_message;
    var $add_to_cart_button_text;
    //
    var $link_to_product_field_name;
    var $open_product_link_to_another_window;

    var $currency_format;// = '$%.02fYen';
    var $quantity;// = 'Quantity';
    var $cart_url;// = 'http://';
    var $send_order_url;// = 'http://';
    var $show_thumbnail_image_in_cart_screen;// checked
    var $qfgetthumb_option_1;// = 'tag=0&num=0&crop_w=150&width=160&crop_h=150&height=160';
    var $qfgetthumb_default_image;// = '';
    //var $cfshoppingcart_justamomentplease;// = 'text-align:center;background:#fff09e;border:2px solid orange;';
    var $max_quantity_of_one_commodity;// = 12;
    var $max_quantity_of_total_order;// = 36;
    //
    var $show_commodity_on_home;// = 'checked';
    var $show_commodity_on_page;// = 'checked';
    var $show_commodity_on_archive;// = 'checked';
    var $show_commodity_on_single;// = 'checked';
    var $show_commodity_on_manually;// = '';
    var $show_products_category_numbers;
    var $go_to_cart_text;
    var $orderer_input_screen_text;
    var $thanks_url;
    var $shop_now_closed;
    var $be_dont_show_empty_field;
    var $closed_message_for_sidebar_widget;
    var $table_tag;
    var $postid_format;
    //
    var $custom_field_default_value;
    var $custom_field_default_value_raw;
    //
    var $show_custom_field_when_price_field_is_empty;
    var $display_waiting_animation;
    var $visual_editor;
    var $multi_site_support;
    //
    var $dont_display_these_information_of_below_if_sold_out_product;
    //
    var $content_instead_of_excerpt_on_home;
    var $content_instead_of_excerpt_on_page;
    var $content_instead_of_excerpt_on_archive;
    var $content_instead_of_excerpt_on_single;
    var $content_instead_of_excerpt_on_category_numbers;
    var $content_instead_of_excerpt_on_page_numbers;
    //
    var $dont_display_order_quantity_textbox;
    var $placed_cart_link_to_under_the_product;
    var $cart_link_text;
    var $placed_check_out_link_to_under_the_product;
    var $check_out_link_text;
    //
    var $display_sold_out_message_in_price_field;
    var $sold_out_message_in_price_field;

    // constructor
    function WpCFShoppingcartModel() {
        // default value
        $this->version = '0.8.17';
        $this->debug = '';
        $this->visual_editor = '';
        $this->multi_site_support = '';
        $this->custom_fields = array('Product ID','Name','Price');
        $this->price_field_name = 'Price';
        $this->currency_format = '$%.02fYen';
        $this->quantity = 'Quantity';
        $this->cart_url = 'http://';
        $this->send_order_url = 'http://';
        $this->show_thumbnail_image_in_cart_screen = 'checked';
        $this->qfgetthumb_option_1 = 'tag=0&num=0&crop_w=150&width=160&crop_h=150&height=160';
        $this->qfgetthumb_default_image = '';
        //$this->cfshoppingcart_justamomentplease = 'text-align:center;background:#fff09e;border:2px solid orange;';
        $this->max_quantity_of_one_commodity = 12;
        $this->max_quantity_of_total_order = 36;
        //
        $this->show_commodity_on_home = 'checked';
        $this->show_commodity_on_page = 'checked';
        $this->show_commodity_on_archive = 'checked';
        $this->show_commodity_on_single = 'checked';
        $this->show_commodity_on_manually = '';
        $this->show_products_category_numbers = '';
        //
        $this->go_to_cart_text = '&raquo;&nbsp;Go To Cart';
        $this->orderer_input_screen_text = '&raquo;&nbsp;Orderer Input screen';
        $this->thanks_url = '';
        // stock
        $this->number_of_stock_field_name = '';
        $this->product_name_field_name = 'Name';
        $this->type_of_show_sold_out_message = '';
        $this->sold_out_message = 'Sold out';
        $this->add_to_cart_button_text = 'Add to Cart';
        $this->shop_now_closed = '';
        $this->be_dont_show_empty_field = '';
        $this->closed_message_for_sidebar_widget = 'Shop now closed';
        //
        $this->table_tag = 'table'; // table, dl
        //
        $this->postid_format = '%05d';
        //
        $this->link_to_product_field_name = 'Name';
        $this->open_product_link_to_another_window = 'checked';
        //
        $this->custom_field_default_value = '';
        //
        $this->display_waiting_animation = '';
        $this->show_custom_field_when_price_field_is_empty = '';
        //
        $this->dont_display_these_information_of_below_if_sold_out_product = '';
        //
        $this->content_instead_of_excerpt_on_home = '';
        $this->content_instead_of_excerpt_on_page = '';
        $this->content_instead_of_excerpt_on_archive = '';
        $this->content_instead_of_excerpt_on_single = '';
        $this->content_instead_of_excerpt_on_category_numbers = '';
        $this->content_instead_of_excerpt_on_page_numbers = '';
        //
        $this->dont_display_order_quantity_textbox = '';
        $this->placed_cart_link_to_under_the_product = '';
        $this->cart_link_text = __('View cart','cfshoppingcart');
        $this->placed_check_out_link_to_under_the_product = '';
        $this->check_out_link_text = __('Check out','cfshoppingcart');
        //
        $this->display_sold_out_message_in_price_field = '';
        $this->sold_out_message_in_price_field = __('Sold out','cfshoppingcart');
    }

    function toDouble($v) {
        if (preg_match('/-?[0-9]+\.[0-9]+$/', $v)) {
            return $v;
        } else if (preg_match('/-?[0-9]+$/', $v)) {
            return $v;
        } else {
            return '';
        }
    }

    function toAlpha($v) {
        return preg_replace('/[^-?[0-9]+\.[0-9]+$/', $v);
    }

    //
    function get_current_version() {
        return '0.8.17';
    }
    function get_version() {
        return $this->version;
    }
    function set_version($fields) {
        $this->version = $fields;
    }
    //
    function setVisualEditor($fields) {
        if (!user_can_richedit()) {
            $fields = '';
        }
        $this->visual_editor = $fields;
    }
    function getVisualEditor() {
        return $this->visual_editor;
    }
    //
    function setMultiSiteSupport($fields) {
        $this->multi_site_support = $fields;
    }
    function getMultiSiteSupport() {
        return $this->multi_site_support;
    }
    //
    function is_debug() {
        if ($this->debug) return true;
        else return false;
    }
    function setDebug($fields) {
        $this->debug = $fields;
    }
    function getDebug() {
        return $this->debug;
    }
    //
    function setShowThumbnailImageInCartScreen($fields) {
        $this->show_thumbnail_image_in_cart_screen = $fields;
    }
    function getShowThumbnailImageInCartScreen() {
        return $this->show_thumbnail_image_in_cart_screen;
    }
    //
    function setDisplayWaitingAnimation($fields) {
        $this->display_waiting_animation = $fields;
    }
    function getDisplayWaitingAnimation() {
        return $this->display_waiting_animation;
    }
    //
    function getWidgetEmpyCartHtml() {
        $current_user = wp_get_current_user();
        $html = '';
        if ($this->getShopNowClosed() && $current_user->user_level < $this->getShopNowClosedUserLevel()) {
            $html .= '<span class="shop_closed">' . $this->getClosedMessageForSidebarWidget() . '</span>';
        } else {
            $html .= '<span class="cart_empty">' . __('Shopping Cart is empty.', 'cfshoppingcart') . '</span>';
        }
        return $html;
    }
    //
    function setPostidFormat($fields) {
        $this->postid_format = $fields;
    }
    function getPostidFormat() {
        return $this->postid_format;
    }
    //
    function getShopNowClosedUserLevel() {
        return 6; // user level
    }
    function setShopNowClosed($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->shop_now_closed = $fields;
    }
    function getShopNowClosed() {
        return $this->shop_now_closed;
    }
    //
    function setCustomFieldDefaultValueRaw($fields) {
        // set raw data
        $this->custom_field_default_value_raw = strip_tags($fields);
    }
    function getCustomFieldDefaultValueRaw() {
        return $this->custom_field_default_value_raw;
    }
    function setCustomFieldDefaultValue($fields) {
        // set data
        global $wpCFShoppingcart, $cfshoppingcart_common;
        //$wpCFShoppingcart = new WpCFShoppingcart();
        $model = $wpCFShoppingcart->model;

        $default = $cfshoppingcart_common->clean_cf_textarea(strip_tags($fields));
        $default = explode("\n", $default);
        foreach ($default as $i => $v) {
            if (!preg_match('/^(.*?)=(.*)$/', $v, $match)) { continue; }
            $def[$match[1]] = $match[2];
        }
        $this->custom_field_default_value = $def;
    }
    function getCustomFieldDefaultValue() {
        return $this->custom_field_default_value;
    }
    //
    function setBeDontShowEmptyField($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->be_dont_show_empty_field = $fields;
    }
    function getBeDontShowEmptyField() {
        return $this->be_dont_show_empty_field;
    }
    //
    function setShowCustomFieldWhenPriceFieldIsEmpty($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_custom_field_when_price_field_is_empty = $fields;
    }
    function getShowCustomFieldWhenPriceFieldIsEmpty() {
        return $this->show_custom_field_when_price_field_is_empty;
    }
    //
    function setClosedMessageForSidebarWidget($fields) {
        $this->closed_message_for_sidebar_widget = strip_tags($fields);
    }
    function getClosedMessageForSidebarWidget() {
        return $this->closed_message_for_sidebar_widget;
    }
    //
    //
    function setTableTag($fields) {
        $this->table_tag = strip_tags($fields);
    }
    function getTableTag() {
        return $this->table_tag;
    }
    function getTableTagListHtml() {
        $gt = $this->table_tag;

        $h = '<select name="table_tag" id="table_tag">';
        if ($gt === 'table') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="table" value="table" ' . $selected . '>table</option>';
        if ($gt === 'dl') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="dl" value="dl" ' . $selected . '>dl</option>';
        $h .= '</select>';
        return $h;
    }

    // stock
    function setNumberOfStockFieldName($fields) {
        $this->number_of_stock_field_name = strip_tags(trim($fields));
    }
    function getNumberOfStockFieldName($ra = 0) {
        $v = $this->number_of_stock_field_name;
        if (!$ra) return $v;

        if (in_array($v, $cfs = $this->getCustomFields())) {
            return array('value' => $v, 'msg' => '');
        } else {
            return array('value' => $v, 'msg' => __('*This field name is not exists in Custom Field.','cfshoppingcart'));
        }
    }
    // product name
    function setProductNameFieldName($fields) {
        $this->product_name_field_name = strip_tags(trim($fields));
    }
    function getProductNameFieldName($ra = 0) {
        $v = $this->product_name_field_name;
        if (!$ra) return $v;

        if (in_array($v, $cfs = $this->getCustomFields())) {
            return array('value' => $v, 'msg' => '');
        } else {
            return array('value' => $v, 'msg' => __('*This field name is not exists in Custom Field.','cfshoppingcart'));
        }
    }
    //
    function setLinkToProductFieldName($fields) {
        $this->link_to_product_field_name = strip_tags(trim($fields));
    }
    function getLinkToProductFieldName($ra = 0) {
        $v = $this->link_to_product_field_name;
        if (!$ra) return $v;

        if (in_array($v, $cfs = $this->getCustomFields())) {
            return array('value' => $v, 'msg' => '');
        } else {
            return array('value' => $v, 'msg' => __('*This field name is not exists in Custom Field.','cfshoppingcart'));
        }
    }
    function setOpenProductLinkToAnotherWindow($fields) {
        $this->open_product_link_to_another_window = $fields;
    }
    function getOpenProductLinkToAnotherWindow() {
        return $this->open_product_link_to_another_window;
    }
    //
    function setTypeOfShowSoldOutMessage($fields) {
        $this->type_of_show_sold_out_message = strip_tags($fields);
    }
    function getTypeOfShowSoldOutMessage() {
        return $this->type_of_show_sold_out_message;
    }
    function getTypeOfShowSoldOutMessageListHtml() {
        $gt = $this->type_of_show_sold_out_message;

        $h = '<select name="type_of_show_sold_out_message" id="type_of_show_sold_out_message" >';
        if ($gt === 'show_sold_out_message') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="show_sold_out_message" value="show_sold_out_message" ' . $selected . '>' . __("Show sold out message",'cfshoppingcart') . '</option>';
        if ($gt === 'dont_show_the_product') { $selected = 'selected="selected"'; } else { $selected = ''; }
        $h .= '<option class="dont_show_the_product" value="dont_show_the_product" ' . $selected . '>' . __("Don't show the product",'cfshoppingcart') . "</option>";
        $h .= '</select>';
return $h;
    }
    //
    function setSoldOutMessage($fields) {
        $this->sold_out_message = strip_tags(trim($fields));
    }
    function getSoldOutMessage() {
        return $this->sold_out_message;
    }
    //
    function setAddToCartButtonText($fields) {
        $this->add_to_cart_button_text = strip_tags(trim($fields));
    }
    function getAddToCartButtonText() {
        $t = $this->add_to_cart_button_text;
        if (!$t) { $t = __('Add to Cart','cfshoppingcart'); }
        return $t;
    }

    //
    function setShowCommodityOnHome($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_home = $fields;
    }
    function getShowCommodityOnHome() {
        return $this->show_commodity_on_home;
    }
    //
    function setShowCommodityOnPage($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_page = $fields;
    }
    function getShowCommodityOnPage() {
        return $this->show_commodity_on_page;
    }
    //
    function setShowCommodityOnArchive($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_archive = $fields;
    }
    function getShowCommodityOnArchive() {
        return $this->show_commodity_on_archive;
    }
    //
    function setShowCommodityOnSingle($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_single = $fields;
    }
    function getShowCommodityOnSingle() {
        return $this->show_commodity_on_single;
    }
    //
    function setShowCommodityOnManually($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->show_commodity_on_manually = $fields;
    }
    function getShowCommodityOnManually() {
        return $this->show_commodity_on_manually;
    }
    //
    function setShowProductsCategoryNumbers($fields) {
        $f = trim(preg_replace('/[^0-9,]|^,+|,+$/', '', $fields));
        if ($f === "") {
            $this->show_products_category_numbers = NULL;
        } else {
            $f = explode(',', $f);
            $this->show_products_category_numbers = $f;
        }
    }
    function getShowProductsCategoryNumbers() {
        $a = $this->show_products_category_numbers;
        if (!is_array($a)) return '';
        return join(',', $a);
    }
    function isShowProductsCategoryNumber($postid) {
        //echo "postid = $postid ";
        $ca = $this->show_products_category_numbers;
        //print_r($ca);
        //if (count($ca) == 0) return true;
        if (!$ca) return true;

        $cates  = get_the_category($postid);
        foreach ($cates as $cate) {
            // 未分類を除外
            //if ($cate->cat_ID == 0) { continue; }
            //echo "catid = " . $cate->cat_ID;
            if (in_array($cate->cat_ID, $ca)) {
                //echo " (" . $ca . " == " . $cate->cat_ID . ")";
                return true;
            }
        }
        return false;
    }
    //
    function setCustomFields($fields) {
        $a = array();
        $f = explode(',', $fields);
        foreach ($f as $key => $value) {
            $s = strip_tags(trim($value));
            if ($s) array_push($a, $s);
        }
        $this->custom_fields = $a;
    }
    function getCustomFields() {
        $cf = $this->custom_fields;
        if (is_array($cf)) {
            return $cf;
        } else {
            $a = array();
            array_push($a, $cf);
            return $a;
        }
    }
    function getCustomFieldsString() {
        $a = $this->getCustomFields();
        return join(',', $a);
    }
    //
    function setPriceFieldName($field) {
        $this->price_field_name = strip_tags(trim($field));
    }
    function getPriceFieldName($ra = 0) {
        $v = $this->price_field_name;
        if (!$ra) return $v;

        if (in_array($v, $cfs = $this->getCustomFields())) {
            return array('value' => $v, 'msg' => '');
        } else {
            return array('value' => $v, 'msg' => __('*This field name is not exists in Custom Field.','cfshoppingcart'));
        }
    }
    //
    function setCurrencyFormat($field) {
        $this->currency_format = strip_tags($field);
    }
    function getCurrencyFormat() {
        return $this->currency_format;
    }
    //
    function setQuantity($field) {
        $this->quantity = strip_tags($field);
    }
    function getQuantity() {
        return $this->quantity;
    }
    //
    function setGoToCartText($field) {
        $this->go_to_cart_text = strip_tags($field);
    }
    function getGoToCartText() {
        return $this->go_to_cart_text;
    }
    //
    function setOrdererInputScreenText($field) {
        //$this->orderer_input_screen_text = strip_tags($field);
        $this->orderer_input_screen_text = $field;
    }
    function getOrdererInputScreenText() {
        return $this->orderer_input_screen_text;
        //return stripslashes($this->orderer_input_screen_text);
    }
    //
    function setThanksUrl($field) {
        $this->thanks_url = strip_tags($field);
    }
    function getThanksUrl() {
        return $this->thanks_url;
    }
    //
    function setCartUrl($field) {
        $this->cart_url = strip_tags($field);
    }
    function getCartUrl() {
        return $this->cart_url;
    }
    //
    function setSendOrderUrl($field) {
        $this->send_order_url = strip_tags($field);
    }
    function getSendOrderUrl() {
        return $this->send_order_url;
    }
    //
    function setQfgetthumbOption1($field) {
        $this->qfgetthumb_option_1 = strip_tags($field);
    }
    function getQfgetthumbOption1() {
        return $this->qfgetthumb_option_1;
    }
    //
    function setQfgetthumbDefaultImage($field) {
        $this->qfgetthumb_default_image = strip_tags($field);
    }
    function getQfgetthumbDefaultImage() {
        return $this->qfgetthumb_default_image;
    }
    //
    function setMaxQuantityOfOneCommodity($field) {
        $this->max_quantity_of_one_commodity = $this->toDouble($field);
    }
    function getMaxQuantityOfOneCommodity() {
        return $this->max_quantity_of_one_commodity;
    }
    //
    function setMaxQuantityOfTotalOrder($field) {
        $this->max_quantity_of_total_order = $this->toDouble($field);
    }
    function getMaxQuantityOfTotalOrder() {
        return $this->max_quantity_of_total_order;
    }
    //
    function setDontDisplayTheseInformationOfBelowIfSoldOutProduct($fields) {
        $a = array();
        $f = explode(',', $fields);
        foreach ($f as $key => $value) {
            $s = strip_tags(trim($value));
            if ($s) array_push($a, $s);
        }
        $this->dont_display_these_information_of_below_if_sold_out_product = $a;
    }
    function getDontDisplayTheseInformationOfBelowIfSoldOutProduct($ra = 0) {
        $cf = $this->dont_display_these_information_of_below_if_sold_out_product;
        if (is_array($cf)) {
            $ret = $cf;
        } else {
            $a = array();
            array_push($a, $cf);
            $ret = $a;
        }
        if (!$ra) return $ret;

        // check if exists field name
        $cfs = $this->getCustomFields();
        $faild_values = array();
        foreach ($ret as $k1 => $v1) {
            if (!in_array($v1, $cfs)) {
                $faild_values[] = $v1;
            }
        }
        if ($faild_values) {
            return array('value' => $ret, 'msg' => sprintf(__('*Field name "%s" is not exists in Custom Field.','cfshoppingcart'), join(',',$faild_values)));
        } else {
            return array('value' => $ret, '');
        }
    }


    //
    function setContentInsteadOfExcerptOnHome($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->content_instead_of_excerpt_on_home = $fields;
    }
    function getContentInsteadOfExcerptOnHome() {
        return $this->content_instead_of_excerpt_on_home;
    }
    function setContentInsteadOfExcerptOnPage($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->content_instead_of_excerpt_on_page = $fields;
    }
    function getContentInsteadOfExcerptOnPage() {
        return $this->content_instead_of_excerpt_on_page;
    }
    function setContentInsteadOfExcerptOnArchive($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->content_instead_of_excerpt_on_archive = $fields;
    }
    function getContentInsteadOfExcerptOnArchive() {
        return $this->content_instead_of_excerpt_on_archive;
    }
    function setContentInsteadOfExcerptOnSingle($fields) {
        $fields = preg_replace('/[^a-zA-Z]/', '', $fields);
        $this->content_instead_of_excerpt_on_single = $fields;
    }
    function getContentInsteadOfExcerptOnSingle() {
        return $this->content_instead_of_excerpt_on_single;
    }
    function setContentInsteadOfExcerptOnCategoryNumbers($fields) {
        $f = trim(preg_replace('/[^0-9,]|^,+|,+$/', '', $fields));
        if ($f === "") {
            $this->content_instead_of_excerpt_on_category_numbers = NULL;
        } else {
            $f = explode(',', $f);
            $this->content_instead_of_excerpt_on_category_numbers = $f;
        }
    }
    function getContentInsteadOfExcerptOnCategoryNumbers() {
        $a = $this->content_instead_of_excerpt_on_category_numbers;
        if (!is_array($a)) return array();
        return $a;
    }
    function setContentInsteadOfExcerptOnPageNumbers($fields) {
        $f = trim(preg_replace('/[^0-9,]|^,+|,+$/', '', $fields));
        if ($f === "") {
            $this->content_instead_of_excerpt_on_page_numbers = NULL;
        } else {
            $f = explode(',', $f);
            $this->content_instead_of_excerpt_on_page_numbers = $f;
        }
    }
    function getContentInsteadOfExcerptOnPageNumbers() {
        $a = $this->content_instead_of_excerpt_on_page_numbers;
        if (!is_array($a)) return array();
        return $a;
    }

    //
    function setDontDisplayOrderQuantityTextbox($fields) {
        $this->dont_display_order_quantity_textbox = $fields;
    }
    function getDontDisplayOrderQuantityTextboxHtml() {
        return $this->getSelectTerminalTag('dont_display_order_quantity_textbox', $this->dont_display_order_quantity_textbox);
    }
    function getDontDisplayOrderQuantityTextboxValue() {
        global $wpCFShoppingcart;
        $ttype = $wpCFShoppingcart->getTerminalType();
        return $this->dont_display_order_quantity_textbox[$ttype];
    }
    function setPlacedCartLinkToUnderTheProduct($fields) {
        $this->placed_cart_link_to_under_the_product = $fields;
    }
    function getPlacedCartLinkToUnderTheProductHtml() {
        return $this->getSelectTerminalTag('placed_cart_link_to_under_the_product', $this->placed_cart_link_to_under_the_product);
    }
    function getPlacedCartLinkToUnderTheProductValue() {
        global $wpCFShoppingcart;
        $ttype = $wpCFShoppingcart->getTerminalType();
        return $this->placed_cart_link_to_under_the_product[$ttype];
    }
    function setCartLinkText($fields) {
        $this->cart_link_text = $fields;
    }
    function getCartLinkText() {
        return $this->cart_link_text;
    }
    function setPlacedCheckOutLinkToUnderTheProduct($fields) {
        $this->placed_check_out_link_to_under_the_product = $fields;
    }
    function getPlacedCheckOutLinkToUnderTheProductHtml() {
        return $this->getSelectTerminalTag('placed_check_out_link_to_under_the_product', $this->placed_check_out_link_to_under_the_product);
    }
    function getPlacedCheckOutLinkToUnderTheProductValue() {
        global $wpCFShoppingcart;
        $ttype = $wpCFShoppingcart->getTerminalType();
        return $this->placed_check_out_link_to_under_the_product[$ttype];
    }
    function setCheckOutLinkText($fields) {
        $this->check_out_link_text = $fields;
    }
    function getCheckOutLinkText() {
        return $this->check_out_link_text;
    }
    //
    function setDisplaySoldOutMessageInPriceField($fields) {
        $this->display_sold_out_message_in_price_field = $fields;
    }
    function getDisplaySoldOutMessageInPriceField() {
        return $this->display_sold_out_message_in_price_field;
    }
    function setSoldOutMessageInPriceField($fields) {
        $this->sold_out_message_in_price_field = $fields;
    }
    function getSoldOutMessageInPriceField() {
        return $this->sold_out_message_in_price_field;
    }
    //
    function getSelectTerminalTag($name, $values) {
        $v = array('pc',
                   'smartphone',
                   //'tabletpc',
                   'cellphone',
                   );
        $m = array(__('PC','cfshoppingcart'),
                   __('Smartphone','cfshoppingcart'),
                   //__('Tablet PC','cfshoppingcart'),
                   __('Cellphone','cfshoppingcart'),
                   );
        $c = count($v);
        $h = array();
        for ($i = 0; $i < $c; $i++) {
            $n = $name . '[' . $v[$i] . ']';
            if ($values[$v[$i]]) { $checked = 'checked'; } else {$checked = '';}
            $h[] = '<input type="checkbox" name="'.$n.'" value="checked" '.$checked.' /> '.$m[$i];
        }
        return join(', ', $h);
    }
}

/* main class */
class WpCFShoppingcart {
    var $view;
    var $model;
    var $common;
    var $pnotify;
    var $shipping; // object
    var $widget; // object
    var $request;
    var $plugin_name;
    var $plugin_fullpath, $plugin_path, $plugin_folder, $plugin_uri;

    // constructor
    function WpCFShoppingcart() {
        $this->plugin_name = 'cfshoppingcart';
        $this->model = $this->getModelObject();

        require_once('module/shipping.php');
        $this->shipping = new WpCFShoppingcartShipping($wpCFShoppingcart);

        require_once('module/widget.php');
        $this->widget = new WpCFShoppingcartWidget($wpCFShoppingcart);

        require_once('module/pnotify.php');
        $this->pnotify = new WpCFShoppingcartPnotify($wpCFShoppingcart);

        require_once('module/common.php');
        $this->common = new cfshoppingcart_common();
        $this->plugin_uri = $this->common->get_plugin_uri();
    }

    // create model object
    function getModelObject() {
        $data_clear = 0; // Debug: 1: Be empty to data

        // get option from Wordpress
        $option = $this->getWpOption();

        //printf("<p>Debug[%s, %s]</p>", strtolower(get_class($option)), strtolower('WpCFShoppingcartModel'));

        // Restore the model object if it is registered
        if (strtolower(get_class($option)) === strtolower('WpCFShoppingcartModel') && $data_clear == 0) {
            $model = $option;
        } else {
            // create model instance if it is not registered,
            // register it to Wordpress
            $model = new WpCFShoppingcartModel();
            $this->addWpOption($model);
        }
        return $model;
    }

    function getWpOption() {
        $option = get_option($this->plugin_name);

        if(!$option == false) {
            $OptionValue = $option;
        } else {
            $OptionValue = false;
        }
        return $OptionValue;
    }

    /* be add plug-in data to Wordpresss */
    function addWpOption(&$model) {
        $option_description = $this->plugin_name . " Options";
        $OptionValue = $model;
        //print_r($OptionValue);
        add_option(
            $this->plugin_name,
            $OptionValue,
            $option_description);
    }

    /* update plug-in data */
    function updateWpOption(&$OptionValue) {
        $option_description = $this->plugin_name . " Options";
        $OptionValue = $OptionValue;
        //$OptionValue = $this->model;

        update_option(
            $this->plugin_name,
            $OptionValue,
            $option_description);
    }

    /*
     * management screen header output
     * reading javascript and css
     */
    function addAdminHead() {
        echo '<link type="text/css" rel="stylesheet" href="';
        echo $this->plugin_uri . '/cfshoppingcart.css" />' . "\n";

        echo '<script type="text/javascript" src="';
        echo $this->plugin_uri . '/js/jquery.cookie.js"></script>' . "\n";
        echo '<script type="text/javascript" src="';
        echo $this->plugin_uri . '/js/dismiss20.js"></script>' . "\n";
        echo '<script type="text/javascript" src="';
        echo $this->plugin_uri . '/js/postbox.js"></script>' . "\n";
    }

    function addAdminMenu() {
        $hook = add_options_page(
            __('Cf Shopping Cart Options','cfshoppingcart'),
            __('Cf Shopping Cart','cfshoppingcart'),
            8,
            'cfshoppingcart.php',
            array(&$this, 'executeAdmin')
            );
        add_action('admin_print_scripts-'.$hook, array(&$this, 'admin_scripts'));
    }

    function admin_scripts() {
        global $wp_version;

        if ($this->model->getVisualEditor()) {
            // tiny mce
            if (version_compare($wp_version, '3.2', '>=')) {
                add_filter('tiny_mce_before_init', 'cfshoppingcart_tiny_mce_before_init', 999);
            } else {
                //echo $wp_version;
                add_filter('admin_head','cfshoppingcart_admin_tinymce');
                add_action('admin_print_footer_scripts', 'wp_tiny_mce_preload_dialogs', 30);
                add_action('tiny_mce_preload_dialogs', 'wp_link_dialog', 30);
            }
        }
    }

    function executeAdmin() {
        require_once('module/execute_admin.php');
        execute_admin($this);
    }

    function getTerminalType() {
        global $Ktai_Style;
        $ret = '';
        if (is_object($Ktai_Style)) {
            if ($Ktai_Style->is_ktai()) {
                $ret = 'ktai';
            }
        }
        if ($ret == '' && class_exists('WPtouchPlugin')) {
            $wptouch = new WPtouchPlugin();
            if ($wptouch->applemobile) { // boolean
                $ret = 'smartphone';
            }
        }
        if ($ret == '') {
            $ret = 'pc';
        }
        //
        if (isset($this->args['debug'])) {
            echo '<p>getTerminalType: applemobile = '.$ret . '</p>';
            if (is_object($Ktai_Style)) {
                echo '<p>getTerminalType: KtaiStylePlugin not found.</p>';
            }
            if (class_exists('WPtouchPlugin')) {
                echo '<p>getTerminalType: WPtouchPlugin not found.</p>';
            }
        }
        return $ret;
    }
}


