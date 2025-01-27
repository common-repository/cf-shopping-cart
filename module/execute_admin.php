<?php
/*
 * Setting Screen
 * call: execute_admin($this);
 * -*- Encoding: utf8n -*-
 */
function execute_admin(&$obj) {
    //print_r($_REQUEST);
    //print_r($obj->model);
    //echo phpinfo();

    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
    }

    if (isset($submit_symlink)) {
        $msg = symbolic_link_for_contactForm7Modules($obj);
    }

    ?>
    <div class="wrap cfshoppingcart_admin">
    <div id="icon-plugins" class="icon32"><br/></div>
    <h2><?php _e('Cf Shopping Cart', 'cfshoppingcart'); ?></h2>
    <form name="formCFShoppingcart" method="post">
    <div id="poststuff" class="meta-box-sortables" style="position: relative; margin-top:10px;">

    <?php
    if (isset($save)) {
        $msg = save($obj);
    }
    edit($obj, $msg);

    echo '</div>';
    echo '</form>';
    echo '</div>';
}

function symbolic_link_for_contactForm7Modules ($obj, $cmd = '') {
    //print $custom_fields;
    $model = &$obj->model;

    $ver = $model->get_version();

    global $cfshoppingcart_common;

    $f1 = $cfshoppingcart_common->get_plugin_fullpath();
    $f2dir = $f1;
    $f1 .= '/contact-form-7-module/cfshoppingcart.php';
    $f2dir = str_replace('/cf-shopping-cart', '/contact-form-7/modules', $f2dir);
    $f2 = $f2dir . '/cfshoppingcart.php';

    if (file_exists($f2dir)) {
        if ($cmd === 'check') return true;
    } else {
        if ($cmd === 'check') return false;
        return __('Set extend module for Contact Form 7','cfshoppingcart') . __('Direcotory not found','cfshoppingcart') . ': "' . $f2dir . '"';
    }

    if (file_exists($f2)) {
        return __('Already exists extend module for Contact Form 7.','cfshoppingcart');
    } else {
        //echo 'create';
        $old_umask = umask(0);
        $old_dir = fileperms($f2dir);
        if (!@chmod($f2dir, 0777)) {
            $msg .= '<p>1. ' . __('Change permission to folder to 0777 failed.','cfshoppingcart') . '(' . $f2dir . ')</p>';
        }
        if (!@symlink($f1, $f2)) {
            $msg .= '<p>2. ' . __('Create symbolic link is failed.','cfshoppingcart') . '(' . $f1 . ' to ' . $f2 . ')</p>';
        }
        /*
        if (!@chmod($f2, 0755)) {
            $msg .= '<p>3. ' . __('Change permission to symbolic link to 0755 failed.','cfshoppingcart') . '(' . $f2 . ')</p>';
        }
         */
        if (!@chmod($f2dir, $old_dir)) {
            $msg .= '<p>4. ' . __('Change permission to folder failed.','cfshoppingcart') . '(' . $f2dir . ')</p>';
        }
        umask($old_umask);
    }
    if (file_exists($f2)) {
        return '<p>' . __('Extend module for Contact Form 7 is OK.','cfshoppingcart') . '</p>';
    } else {
        return __('Create link to extend module for Contact Form 7 failed.','cfshoppingcart') . $msg;
    }
}

function save(&$obj) {
    //print_r($_REQUEST);
    if (is_array($_REQUEST)) {
        // Array extract to variable
        extract($_REQUEST);
    }

    //print $custom_fields;
    $model = &$obj->model;

    $model->set_version($model->get_current_version());

    $model->setCustomFields($custom_fields);
    $model->setPriceFieldName($price_field_name);
    //
    $model->setNumberOfStockFieldName($number_of_stock_field_name);
    $model->setProductNameFieldName($product_name_field_name);
    $model->setAddToCartButtonText($add_to_cart_button_text);
    $model->setSoldOutMessage($sold_out_message);
    $model->setTypeOfShowSoldOutMessage($type_of_show_sold_out_message);
    //
    $model->setLinkToProductFieldName($link_to_product_field_name);
    $model->setOpenProductLinkToAnotherWindow($open_product_link_to_another_window);
    //
    //$model->setCurrency_before($currency_before);
    //$model->setCurrency_after($currency_after);
    $model->setCurrencyFormat($currency_format);
    $model->setPostidFormat($postid_format);
    $model->setQuantity($quantity);
    $model->setCartUrl($cart_url);
    $model->setSendOrderUrl($send_order_url);
    $model->setShowThumbnailImageInCartScreen($show_thumbnail_image_in_cart_screen);
    $model->setQfgetthumbOption1($qfgetthumb_option_1);
    $model->setQfgetthumbDefaultImage($qfgetthumb_default_image);
    $model->setMaxQuantityOfOneCommodity($max_quantity_of_one_commodity);
    $model->setMaxQuantityOfTotalOrder($max_quantity_of_total_order);
    $model->setDisplayWaitingAnimation($display_waiting_animation);
    $model->setDebug($is_debug);
    $model->setVisualEditor($visual_editor);
    $model->setMultiSiteSupport($multi_site_support);

    $model->setShowCommodityOnHome($show_commodity_on_home);
    $model->setShowCommodityOnPage($show_commodity_on_page);
    $model->setShowCommodityOnArchive($show_commodity_on_archive);
    $model->setShowCommodityOnSingle($show_commodity_on_single);
    $model->setShowCommodityOnManually($show_commodity_on_manually);
    $model->setShowProductsCategoryNumbers($show_products_category_numbers);
    //
    $model->setGoToCartText($go_to_cart_text);
    $model->setOrdererInputScreenText($orderer_input_screen_text);
    $model->setThanksUrl($thanks_url);
    //
    $model->setShopNowClosed($shop_now_closed);
    $model->setClosedMessageForSidebarWidget($closed_message_for_sidebar_widget);
    //print_r($model);
    $model->setBeDontShowEmptyField($be_dont_show_empty_field);
    //
    $model->setTableTag($table_tag);
    //
    $model->setCustomFieldDefaultValue($custom_field_default_value);
    $model->setCustomFieldDefaultValueRaw($custom_field_default_value);
    //
    $model->setShowCustomFieldWhenPriceFieldIsEmpty($show_custom_field_when_price_field_is_empty);
    //
    $model->setDontDisplayTheseInformationOfBelowIfSoldOutProduct($dont_display_these_information_of_below_if_sold_out_product);

    $model->setContentInsteadOfExcerptOnHome($content_instead_of_excerpt_on_home);
    $model->setContentInsteadOfExcerptOnPage($content_instead_of_excerpt_on_page);
    $model->setContentInsteadOfExcerptOnArchive($content_instead_of_excerpt_on_archive);
    $model->setContentInsteadOfExcerptOnSingle($content_instead_of_excerpt_on_single);
    $model->setContentInsteadOfExcerptOnCategoryNumbers($content_instead_of_excerpt_on_category_numbers);
    $model->setContentInsteadOfExcerptOnPageNumbers($content_instead_of_excerpt_on_page_numbers);
    //
    $model->setDontDisplayOrderQuantityTextbox($dont_display_order_quantity_textbox);
    $model->setPlacedCartLinkToUnderTheProduct($placed_cart_link_to_under_the_product);
    $model->setCartLinkText($cart_link_text);
    $model->setPlacedCheckOutLinkToUnderTheProduct($placed_check_out_link_to_under_the_product);
    $model->setCheckOutLinkText($check_out_link_text);

    $model->setDisplaySoldOutMessageInPriceField($display_sold_out_message_in_price_field);
    $model->setSoldOutMessageInPriceField($sold_out_message_in_price_field);

    $obj->updateWpOption($model); // Save database-model
    $msg .= __('Saved', 'cfshoppingcart');
    return $msg;
}

function cfshoppingcart_get_query_string_array() {
    $f = array();
    $f = explode('&', getenv('QUERY_STRING'));
    //print_r($f);
    foreach ($f as $i => $v) {
        $g = explode('=', $v, 2);
        $qs[urldecode($g[0])] = urldecode($g[1]);
    }
    return $qs;
}
function cfshoppingcart_query_string($name, $value) {
    $qs = cfshoppingcart_get_query_string_array();

    if ($value) {
        $qs[$name] = $value;
    } else {
        unset($qs[$name]);
    }
    //
    $q = array();
    foreach ($qs as $n => $v) {
        $q[] = urlencode($n) . '=' . urlencode($v);
    }
    return join('&', $q);
}

function edit(&$obj, $msg = '') {
    //require_once('common.php');
    //$cfshoppingcart_common = new cfshoppingcart_common();
    global $cfshoppingcart_common;
    global $wp_version;

    $model = $obj->model;

    // update message.
    $current_version = $model->get_current_version();
    if ($model->get_version() !== $current_version) {
        $msg .= '<p><a href="http://takeai.silverpigeon.jp/?page_id=727" target="_blank">Cf Shopping Cart needs your support. Please donate today. Your contribution is needed for making this plugin better.</a></p>';
    }
    if ($msg) {
        echo '<div id="message" class="updated"><p>' . $msg . '</p></div>';
    }

    // Custom Field array and string
    $custom_fields_array = $model->getCustomFields();
    $custom_fields = $model->getCustomFieldsString();
    //echo '$model->getCustomFields() = ' . $model->getCustomFields() . ']';



    if (version_compare($wp_version, '3.2', '<')) {
        //wp_tiny_mce(true);
    }

    //echo 'QUERY_STRING' . $_SERVER['QUERY_STRING'];
    $qs = cfshoppingcart_get_query_string_array();
    $qs_option = $qs['option'];
    //echo $qs_option;

    $link_cfshoppingcart = '?' . cfshoppingcart_query_string('option', 'cfshoppingcart');
    $link_shipping = '?' . cfshoppingcart_query_string('option', 'shipping');
    $link_widget = '?' . cfshoppingcart_query_string('option', 'widget');
    $link_cf7 = '?' . cfshoppingcart_query_string('option', 'contactform7');
    $link_paypal = '?' . cfshoppingcart_query_string('option', 'paypal');
    $link_pnotify = '?' . cfshoppingcart_query_string('option', 'pnotify');
    ?>

    <div class="cfshoppingcart_admin_tab">
      <div class="cfshoppingcart_admin_tab_one <?php if ($qs_option === 'cfshoppingcart' || !$qs_option){echo 'cfshoppingcart_admin_current_tab';}?>"><a href="<?php echo $link_cfshoppingcart;?>"><?php _e('Shopping cart','cfshoppingcart');?></a></div>
      <div class="cfshoppingcart_admin_tab_one <?php if ($qs_option === 'shipping'){echo 'cfshoppingcart_admin_current_tab';}?>"><a href="<?php echo $link_shipping;?>"><?php _e('Shipping','cfshoppingcart');?></a></div>
      <div class="cfshoppingcart_admin_tab_one <?php if ($qs_option === 'widget'){echo 'cfshoppingcart_admin_current_tab';}?>"><a href="<?php echo $link_widget;?>"><?php _e('Widget','cfshoppingcart');?></a></div>
      <div class="cfshoppingcart_admin_tab_one <?php if ($qs_option === 'paypal'){echo 'cfshoppingcart_admin_current_tab';}?>"><a href="<?php echo $link_paypal;?>"><?php _e('PayPal','cfshoppingcart');?></a></div>
      <div class="cfshoppingcart_admin_tab_one <?php if ($qs_option === 'pnotify'){echo 'cfshoppingcart_admin_current_tab';}?>"><a href="<?php echo $link_pnotify;?>"><?php _e('pnotify','cfshoppingcart');?></a></div>
      <div class="cfshoppingcart_admin_tab_one <?php if ($qs_option === 'contactform7'){echo 'cfshoppingcart_admin_current_tab';}?>"><a href="<?php echo $link_cf7;?>"><?php _e('Module for Contact Form 7','cfshoppingcart');?></a></div>
    </div>

    <div class="cfshoppingcart_admin-links"><a href="http://takeai.silverpigeon.jp/">blog</a> | <a href="http://cfshoppingcart.silverpigeon.jp/">website</a> | <a href="http://takeai.silverpigeon.jp/?page_id=727">donate</a></div>

  <?php
  if ($qs_option === 'cfshoppingcart' || !$qs_option) {
  ?>

  <div class="postbox cfshoppingcart_postbox">
    <div class="handlediv" title="Click to toggle"><br /></div>
    <h3><?php _e('Options','cfshoppingcart');?></h3>
    <div class="inside">

        <table class="form-table">

        <tr><th><?php _e('Shop now closed', 'cfshoppingcart');?></th><td><input type="checkbox" name="shop_now_closed" value="checked" <?php echo $model->getShopNowClosed();?> /> <?php _e('Closed','cfshoppingcart');?> <?php _e("(Be 'Shop Closed' is user level less than 6.)",'cfshoppingcart');?></td></tr>
        <tr><th><?php _e('Closed message for Sidebar widget', 'cfshoppingcart');?> </th><td><input type="text" name="closed_message_for_sidebar_widget" id="closed_message_for_sidebar_widget" value="<?php echo $model->getClosedMessageForSidebarWidget();?>" style="width:95%" /></td></tr>

        <tr><th><?php _e('Custom field names', 'cfshoppingcart');?> </th><td><input type="text" name="custom_fields" id="custom_fields" value="<?php echo $custom_fields;?>" style="width:95%" /> </td></tr>
          <tr><th><?php _e('Price field name', 'cfshoppingcart');?> </th><td><input type="text" name="price_field_name" id="price_field_name" value="<?php $v = $model->getPriceFieldName(1); echo $v['value'];?>" style="width:95%" /><?php if(!$v['value']){echo '<br /><font color="red">'.__('*Required','cfshoppingcart').'</font>';}else if($v['msg']){echo'<br /><font color="red">'.$v['msg'].'</font>';}?></td></tr>

            <tr><th><?php _e('Be Custom Field name of linking to product page', 'cfshoppingcart');?> </th><td><input type="text" name="link_to_product_field_name" id="link_to_product_field_name" value="<?php $v = $model->getLinkToProductFieldName(1); echo $v['value'];?>" style="width:300px" /> <input type="checkbox" name="open_product_link_to_another_window" value="checked" <?php echo $model->getOpenProductLinkToAnotherWindow();?> /> <?php _e('Open another window','cfshoppingcart');?><?php if($v['value'] && $v['msg']){echo'<br /><font color="red">'.$v['msg'].'</font>';}?></td></tr>

        <tr><th><?php _e('Number of stock field name', 'cfshoppingcart');?> </th><td><input type="text" name="number_of_stock_field_name" id="number_of_stock_field_name" value="<?php $v = $model->getNumberOfStockFieldName(1); echo $v['value'];?>" style="width:95%" /> <?php _e("Empty if don't manage stock.",'cfshoppingcart');?><?php if($v['value'] && $v['msg']){echo'<br /><font color="red">'.$v['msg'].'</font>';}?></td></tr>

        <tr><th><?php _e('Product name field name', 'cfshoppingcart');?> </th><td><input type="text" name="product_name_field_name" id="product_name_field_name" value="<?php $v = $model->getProductNameFieldName(1); echo $v['value'];?>" style="width:95%" /><?php if($v['value'] && $v['msg']){echo'<br /><font color="red">'.$v['msg'].'</font>';}?></td></tr>

        <tr><th><?php _e("Show Custom Field when price field is empty.", 'cfshoppingcart');?></th><td><input type="checkbox" name="show_custom_field_when_price_field_is_empty" value="checked" <?php echo $model->getShowCustomFieldWhenPriceFieldIsEmpty();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
        <tr><th><?php _e("Be don't show empty field", 'cfshoppingcart');?></th><td><input type="checkbox" name="be_dont_show_empty_field" value="checked" <?php echo $model->getBeDontShowEmptyField();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>

        <tr><th><?php _e('Custom Field default value', 'cfshoppingcart');?> <p> <?php _e("example: <br />FieldName1=value1<br />FieldName2=value2<br />...", 'cfshoppingcart');?></p></th><td><textarea name="custom_field_default_value" id="custom_field_default_value" style="width:95%" rows="5"><?php echo $model->getCustomFieldDefaultValueRaw();?></textarea></td></tr>

        <tr><th><?php _e('Type of show sold out message', 'cfshoppingcart');?> </th><td><?php echo $model->getTypeOfShowSoldOutMessageListHtml();?> <?php _e('if select "Don\'t show the post" then to be private the post at sold out.','cfshoppingcart');?></td></tr>

        <tr><th><?php _e('Sold out message in Stock field', 'cfshoppingcart');?> </th><td><input type="text" name="sold_out_message" id="sold_out_message" value="<?php echo $model->getSoldOutMessage();?>" style="width:500px" /></td></tr>

       <tr><th><?php _e('Display sold out message in Price field', 'cfshoppingcart');?></th><td><input type="checkbox" name="display_sold_out_message_in_price_field" value="checked" <?php echo $model->getDisplaySoldOutMessageInPriceField();?> /> <?php _e("Enabled.",'cfshoppingcart');?> <?php _e('Text','cfshoppingcart');?> <input type="text" name="sold_out_message_in_price_field" id="sold_out_message_in_price_field" value="<?php echo $model->getSoldOutMessageInPriceField();?>" style="width:300px" /></td></tr>

        <tr><th><?php _e("Don't display these information of below if sold out product. (fieldname1, fieldname2, ...)", 'cfshoppingcart');?> </th><td><input type="text" name="dont_display_these_information_of_below_if_sold_out_product" id="dont_display_these_information_of_below_if_sold_out_product" value="<?php $v = $model->getDontDisplayTheseInformationOfBelowIfSoldOutProduct(1); echo join(',',$v['value']);?>" style="width:95%" /><?php if($v['value'] && $v['msg']){echo'<br /><font color="red">'.$v['msg'].'</font>';}?></td></tr>

        <tr><th><?php _e('Add to Cart button text', 'cfshoppingcart');?> </th><td><input type="text" name="add_to_cart_button_text" id="add_to_cart_button_text" value="<?php echo $model->getAddToCartButtonText();?>" style="width:95%" /></td></tr>

        <tr><th><?php _e('Quantity', 'cfshoppingcart');?></th><td><?php _e('Text','cfshoppingcart');?>: <input type="text" name="quantity" id="quantity" value="<?php echo $model->getQuantity();?>" style="width:300px" /><br /><?php _e("Don't display order quantity textbox if choice terminals",'cfshoppingcart');?>: <?php echo $model->getDontDisplayOrderQuantityTextboxHtml();?></td></tr>
        <tr><th><?php _e('Currency format', 'cfshoppingcart');?></th><td><input type="text" name="currency_format" id="currency_format" value="<?php echo $model->getCurrencyFormat();?>" size="10" /> <?php _e('example: $%.02f','cfshoppingcart');?></td></tr>
        <tr><th><?php _e('"#postid" keyword format', 'cfshoppingcart');?></th><td><input type="text" name="postid_format" id="postid_format" value="<?php echo $model->getPostidFormat();?>" size="10" /> <?php _e('example: %05d','cfshoppingcart');?></td></tr>
        <tr><th><?php _e('Max quantity of one commodity', 'cfshoppingcart');?></th><td><input type="text" name="max_quantity_of_one_commodity" id="max_quantity_of_one_commodity" value="<?php echo $model->getMaxQuantityOfOneCommodity();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><th><?php _e('Max quantity of total order', 'cfshoppingcart');?></th><td><input type="text" name="max_quantity_of_total_order" id="max_quantity_of_total_order" value="<?php echo $model->getMaxQuantityOfTotalOrder();?>" size="10" /> <?php _e('Zero is no limit.','cfshoppingcart');?></td></tr>
        <tr><th><?php _e('"Go to Cart" text', 'cfshoppingcart');?></th><td><input type="text" name="go_to_cart_text" id="go_to_cart_text" value="<?php echo $model->getGoToCartText();?>" size="40" /></td></tr>

        <tr><th><?php _e('Cart link', 'cfshoppingcart');?></th><td><?php _e('Text','cfshoppingcart');?>: <input type="text" name="cart_link_text" id="cart_link_text" value="<?php echo $model->getCartLinkText();?>" style="width:300px" /><br /><?php _e("Placed cart link to under the product if choice terminals",'cfshoppingcart');?>: <?php echo $model->getPlacedCartLinkToUnderTheProductHtml();?></td></tr>

        <tr><th><?php _e('Check out link', 'cfshoppingcart');?></th><td><?php _e('Text','cfshoppingcart');?>: <input type="text" name="check_out_link_text" id="check_out_link_text" value="<?php echo $model->getCheckOutLinkText();?>" style="width:300px" /><br /><?php _e("Placed check out link to under the product if choice termials",'cfshoppingcart');?>: <?php echo $model->getPlacedCheckOutLinkToUnderTheProductHtml();?></td></tr>

        <?php if ($model->getVisualEditor()) { ?>
          <tr><th><?php _e('"Orderer Input screen" text', 'cfshoppingcart');?></th><td><div class="postarea postdivrich"><?php the_editor(stripslashes($model->getOrdererInputScreenText()), 'orderer_input_screen_text','orderer_input_screen_text',true); ?></div></td></tr>
        <?php } else { ?>
          <tr><th><?php _e('"Orderer Input screen" text', 'cfshoppingcart');?></th><td><textarea name="orderer_input_screen_text" id="orderer_input_screen_text" cols="50" rows="5"><?php echo stripslashes($model->getOrdererInputScreenText());?></textarea></td></tr>
        <?php } ?>


        <tr><th><?php _e('Table tag type','cfshoppingcart');?></th><td><?php echo $model->getTableTagListHtml();?></td></tr>
        <tr><th><?php _e('Cart Url', 'cfshoppingcart');?></th><td><input type="text" name="cart_url" id="cart_url" value="<?php echo $model->getCartUrl();?>" style="width:95%" /></td></tr>
        <tr><th><?php _e('Send order Url', 'cfshoppingcart');?></th><td><input type="text" name="send_order_url" id="send_order_url" value="<?php echo $model->getSendOrderUrl();?>" style="width:95%" /></td></tr>
        <tr><th><?php _e('Thanks Url', 'cfshoppingcart');?></th><td><input type="text" name="thanks_url" id="thanks_url" value="<?php echo $model->getThanksUrl();?>" style="width:95%" /></td></tr>

        <tr><th><?php _e("Show thumbnail image in Cart Screen. Require qf-getthumb-wb plugin.", 'cfshoppingcart');?></th><td><input type="checkbox" name="show_thumbnail_image_in_cart_screen" value="checked" <?php echo $model->getShowThumbnailImageInCartScreen(); ?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>

        <tr><th><?php _e('Thumbnail image options', 'cfshoppingcart');?></th><td><input type="text" name="qfgetthumb_option_1" id="qfgetthumb_option_1" value="<?php echo $model->getQfgetthumbOption1();?>" style="width:95%" /></td></tr>
        <tr><th><?php _e('Default thumbnail image URL', 'cfshoppingcart');?></th><td><input type="text" name="qfgetthumb_default_image" id="qfgetthumb_default_image" value="<?php echo $model->getQfgetthumbDefaultImage();?>" style="width:95%" /></td></tr>

        <tr><th><?php _e('Choice show commodity on page', 'cfshoppingcart');?></th><td><input type="checkbox" name="show_commodity_on_home" value="checked" <?php echo $model->getShowCommodityOnHome();?> /> <?php _e('home','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_page" value="checked" <?php echo $model->getShowCommodityOnPage();?> /> <?php _e('page','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_archive" value="checked" <?php echo $model->getShowCommodityOnArchive();?> /> <?php _e('archive','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_single" value="checked" <?php echo $model->getShowCommodityOnSingle();?> /> <?php _e('single','cfshoppingcart');?> <input type="checkbox" name="show_commodity_on_manually" value="checked" <?php echo $model->getShowCommodityOnManually();?> /> <?php _e('manually (must edit theme)','cfshoppingcart');?> <br /><?php _e('Show products category numbers (Example: 1,2,..)', 'cfshoppingcart');?>: <input type="text" name="show_products_category_numbers" id="show_products_category_numbers" value="<?php echo $model->getShowProductsCategoryNumbers();?>" size="30" /></td></tr>
        <tr><td colspan="2"><?php _e('* Choice manually then insert PHP code: ', 'cfshoppingcart');?> '&lt;?php cfshoppingcart(); ?&gt;' to 'archive.php' and 'single.php' files(and more page.php, index.php...) in '<?php echo get_bloginfo( 'template_directory' );?>' directory.</td></tr>

        <tr><th><?php _e('Display to content instead of excerpt if choice or input.', 'cfshoppingcart');?></th><td><input type="checkbox" name="content_instead_of_excerpt_on_home" value="checked" <?php echo $model->getContentInsteadOfExcerptOnHome();?> /> <?php _e('home','cfshoppingcart');?> <input type="checkbox" name="content_instead_of_excerpt_on_page" value="checked" <?php echo $model->getContentInsteadOfExcerptOnPage();?> /> <?php _e('page','cfshoppingcart');?> <input type="checkbox" name="content_instead_of_excerpt_on_archive" value="checked" <?php echo $model->getContentInsteadOfExcerptOnArchive();?> /> <?php _e('archive','cfshoppingcart');?> <input type="checkbox" name="content_instead_of_excerpt_on_single" value="checked" <?php echo $model->getContentInsteadOfExcerptOnSingle();?> /> <?php _e('single','cfshoppingcart');?> <br /><input type="text" name="content_instead_of_excerpt_on_category_numbers" id="content_instead_of_excerpt_on_category_numbers" value="<?php echo join(',',$model->getContentInsteadOfExcerptOnCategoryNumbers());?>" size="20" /> <?php _e('Category numbers (Example: 1,2,..)', 'cfshoppingcart');?><br /><input type="text" name="content_instead_of_excerpt_on_page_numbers" id="content_instead_of_excerpt_on_page_numbers" value="<?php echo join(',',$model->getContentInsteadOfExcerptOnPageNumbers());?>" size="20" /> <?php _e('Page numbers (Example: 1,2,..)', 'cfshoppingcart');?></td></tr>

        <tr><th><?php _e("Display waiting animation", 'cfshoppingcart');?></th><td><input type="checkbox" name="display_waiting_animation" value="checked" <?php echo $model->getDisplayWaitingAnimation();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>

        <tr><th><?php _e('Debug mode', 'cfshoppingcart');?></th><td><input type="checkbox" name="is_debug" value="checked" <?php echo $model->getDebug();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>
        <tr><th><?php _e('Visual Editor', 'cfshoppingcart');?></th><td><input type="checkbox" name="visual_editor" value="checked" <?php echo $model->getVisualEditor();?> /> <?php _e('Enable the visual editor when setting. Reload this page after update options when change this checkbox.','cfshoppingcart');?></td></tr>

        <tr><th><?php _e('Mult site support', 'cfshoppingcart');?></th><td><input type="checkbox" name="multi_site_support" value="checked" <?php echo $model->getMultiSiteSupport();?> /> <?php _e('Enabled','cfshoppingcart');?></td></tr>

         <tr><th><input type="submit" name="save" value="<?php _e('Update Options', 'cfshoppingcart')?>&nbsp;&raquo;" class="button-primary" /></th><td></td></tr>

        </table>
    </div>
  </div>

    <?php } else if ($qs_option === 'contactform7') { ?>

<div class="postbox cfshoppingcart_postbox">
  <div class="handlediv" title="Click to toggle"><br /></div>
  <h3><?php _e('Module for Contact Form 7','cfshoppingcart');?></h3>
  <div class="inside">
    <?php
    if (!function_exists('symlink')) {
        function symlink($a,$b){ return false; }
        echo '<p>' . __("This server don't have symlink function, therefore can't use button 'Set extend module for Contact Form 7' in Cf Shopping Cart setting screen.",'cfshoppingcart') . '</p>';
    } else {
        ?>
        <table>
        <tr><td><input type="submit" name="submit_symlink" value="<?php _e('Set extend module for Contact Form 7', 'cfshoppingcart')?>" /></td></tr>
        <tr><td><p><?php _e('Click button If shown "Array" in Check out Screen shopping item.','cfshoppingcart');?></p><p><?php _e("or Copy 'cfshoppingcart.php' file from '/wp-content/plugins/cf-shopping-cart/contact-form-7-module/' to '/wp-content/plugins/contact-form-7/module/'.",'cfshoppingcart');?></p></td></tr>
        </table>
        <?php
    }
    ?>
  </div>
</div>



<?php
} else if ($qs_option === 'shipping') {
    apply_filters('cfshoppingcart_shipping_put_configuration', $obj);
} else if ($qs_option === 'widget') {
    apply_filters('cfshoppingcart_widget_put_configuration', $obj);
} else if ($qs_option === 'pnotify') {
    apply_filters('cfshoppingcart_pnotify_put_configuration', $obj);
} else if ($qs_option === 'paypal') {
    apply_filters('cfshoppingcart_paypal_put_configuration', $obj);
} else {
    echo '<p>Unknown qs_option</p>';
}
}

