=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Tags: shopping cart, widget, plugin, Exec-PHP, Contact Form 7, Custom Field Template, Multi site supported
Requires at least: 2.6
Tested up to: 4.9.5
Stable tag: 0.8.17

Cf Shopping Cart is simple shopping cart plugin for WordPress.
This plugin be working with Custom Field and more plugins.
Thereby website can have flexible design.
Can manage stock. Can use select to option different types of products, these can have extra charge or discount.
Can set shipping. Support PayPal payment.


== Description ==

Cf Shopping Cart is simple shopping cart plugin for WordPress.
This plugin be working with Custom Field and more plugins.
Thereby website can have flexible design.
Can manage stock. Can use select to option different types of products, these can have extra charge or discount.
Can set shipping. Support PayPal payment.


= Translators =

* Russian (ru_RU) - [kg69design](http://kg69design.com)
* Russian (ru_RU) - [Evgeny Vakhteev](http://www.sdelanomnoy.ru/)
* Dutch (nl_NL) - [G. J. van den Os](http://www.arrowhosting.nl)
* Dutch (nl_NL) - [Rene](http://www.cesmehotels.com)
* Spanish (es_ES) - [Jorge Guerrero, Miguel Olivares, Estefan僘 Mu?z](http://www.tehacesver.com/)
* Chinese (zh_TW) - [Tsao Peter](mailto:tsaopeter@yahoo.com.tw)
* German (de_DE) - Carola Fichtner
* Japanese (ja) - [AI.Takeuchi](http://takeai.silverpigeon.jp/)

If you have created your own language pack, or have an update of an existing one, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to [me](http://takeai.silverpigeon.jp/) so that I can bundle it into Cf Shopping Cart. You can [download the latest POT file from here](http://cfshoppingcart.silverpigeon.jp/?page_id=112).


== Installation ==

1. Install plugins and activate. Cf Shopping Cart, Contact Form 7, Custom Field Template, Exec-PHP(optional) and QF-GetThumb(optional).
2. Be place Cf Shopping Cart widget to sidebar.
3. Visit settings Contact Form 7, Add new contact form.
   Add code into the Form: '[cfshoppingcart* cartdata class:cfshoppingcart7]'.
   Add code into the Message body: '[cartdata]'.
   If need, add code to Additional Settings: 'on_sent_ok: "cfshoppingcart_empty_cart();"', The "on_sent_ok" and its sibling setting "on_submit" are deprecated and scheduled to be abolished by the end of 2017.
4. Add new page (example 'Check out'), add code into the article: '[contact-form ? "???"]'. remember this page url.
5. Add new page (example 'Shopping Cart') and put in '[cfshoppingcart_cart 1]' to article. remember this page url.
6. Setting Custom Field Template, add new template. Field name example: 'Product ID', 'Name' and 'Price'... remember field names.
7. Settings Cf Shopping Cart.
(If choice manually at 7th step then edit theme file (archive.php, single.php and more). Insert php code '<?php cfshoppingcart(); ?>' to the loop of output-article.)
8. Make product pages. Add new page or post, input Custom Field, write article and set category.
9. Repeat 8th steps.

[For basic Installation, you can also have a look at the plugin homepage.](http://cfshoppingcart.silverpigeon.jp/?page_id=13)

If you be running WordPress on Windows, must be rewrite php.ini file, if necessary.
Thereby, Php don't say error message very well.
Reference: http://php.net/manual/en/function.error-reporting.php
php.ini:
-- before --
error_reporting = E_ALL | E_STRICT
-- after --
;error_reporting = E_ALL | E_STRICT
error_reporting  =  E_ALL & ~E_NOTICE & ~E_DEPRECATED
------------

== Changelog ==

= 0.8.17 =

* Revert version 2.0 to 0.8
* Support Contact Form 7 plugin, later than version 4.1

= 0.8.16 =
* Add Admin Notices and button:
  New version 2.0, Tests now.
  Be careful: 2.0 is NOT COMPATIBLE previous!
  http://cfshoppingcart.silverpigeon.jp/

= 0.8.15 =
* Add Admin Notices:
  New version 2.0, Tests now.
  Be careful: 2.0 is NOT COMPATIBLE previous!
  http://cfshoppingcart.silverpigeon.jp/

= 0.8.14 =
* Support radio button.

= 0.8.13 =
* Can customize display cart widget.

= 0.8.12 =
* Multi site supported.

= 0.8.11 =
* Bug fix: check sold out.

= 0.8.10 =
* Bug fix.

= 0.8.9 =
* Separate to shipping setting screen.
* Add keyword "_CFSHOPPINGCART_PRODUCT_IS_HERE_", this keyword replace with detail of product in contents.
* Bug fix.


= 0.8.8 =
* Attention, this version has many changes. previous version is 0.7.9.
* Added options.
* One part of function is using WPtouch plugin.
* Remove and split shortcode "cfshoppingcart_put_cf_image" to another plugin.

= 0.8.7 =
* Added options and bug fix.

= 0.8.6 =
* Added options and bug fix.

= 0.8.5 =
* Added shortcode "cfshoppingcart_put_cf_image" and options.

= 0.8.4 =
* This is test version, added options.

= 0.8.3 =
* This is test version.

= 0.8.2 =
* This is test version.

= 0.8.1 =
* Added option: Display to content instead of excerpt if choice or input.

= 0.8.0 =
* Can edit popup messages (pnotify.js).
* Hide field of product information when sold out.

= 0.7.9 =
* Add return URL parameter that is change stock and empty cart after paypal payment successful.

= 0.7.8 =
* Fix: module for Contact Form 7.

= 0.7.7 =
* Translation for Russian has been created by kg69design.

= 0.7.6 =
* Fix message.

= 0.7.5 =
* Bug fix: 'wpcf7_mail_sent' hook.

= 0.7.4 =
* Bug fix: hook.

= 0.7.3 =
* Such as change message.

= 0.7.2 =
* Bug fix: about Visual Editor for WordPress 3.2.

= 0.7.1 =
* Bug fix: about Visual Editor for WordPress 3.2.
* PHP Error Handler option is change default on.

= 0.7.0 =
* Supported Visual Editor for WordPress 3.2.

= 0.6.19 =
* Added option, can change 'Add to Cart' button text.

= 0.6.18 =
* Tentatively, Visual Editor is disabled when use version of WordPress newer than 3.2. Can't work Visual Editor in setting screen on WordPress 3.2-RC2.

= 0.6.17 =
* Bug fix: output and check number of stock in check out screen.

= 0.6.16 =
* Resurrection of function: create symbolic link Extend module for Contact Form 7 plugin.

= 0.6.15 =
* Added option to php error handler. this option default is off. to use the option then have to edit script cfshoppingcart.php, off commnet to line:  require_once('module/error_handler.php');
* Bug fix: missing parameter preg_replace function.

= 0.6.14 =
* for debug version.

= 0.6.13 =
* Bug fix: process of check stock, case of stock item is single.

= 0.6.12 =
* Bug fix: destroy global value "post" in Cart screen.

= 0.6.11 =
* Bug fix: Check empty array for about specify products category number.

= 0.6.10 =
* Update Dutch language pack by G. J. van den Os. supported version 0.6.10 (Paypal).

= 0.6.9 =
* Fix PayPal default value.

= 0.6.8 =
* Bug fix: about product name for Paypal.
* Fix textarea size for PayPal configuration screen.

= 0.6.7 =
* Fix textarea size.

= 0.6.6 =
* Bug fix: about specify products category number.

= 0.6.5 =
* Can be specify products category number.

= 0.6.4 =
* Be using json_encode function when PHP have it.
* Added option: Display waiting animation.
* Added shorcode: cfshoppingcart_put_shipping.
* Added PayPal payment support.
* Visual Editor support.
* Added text area to widget.

= 0.6.3 =
* Update Dutch language pack by G. J. van den Os. supported version 0.6.3.
* Added to Custom Field keyword '#post_title'. This keyword be replaced to Post title.
* Rewrite Javascript.

= 0.6.2 =
* Bug fix: Process function argument error in shortcode for Contact Form 7.
* Bug fix: About display number of stock products.

= 0.6.1 =
* Bug fix: duplicate module error: wpcf7_cfshoppingcart_shortcode_handler.

= 0.6.0 =
* Attention, this version has many changes. previous version is 0.3.6.
* Bug fix: css load path failed in admin screen.

= 0.5.9 =
* Call wpcf7_add_shortcode function in myself.

= 0.5.8 =
* Bug fix: number of products in cart not clear after check out.

= 0.5.7 =
* Bug fix: shop closed message for widget.

= 0.5.6 =
* Added setting: Don't load css.

= 0.5.5 =
* Be put such 'empty cart' and other messages on check out screen.
* Move output script to footer.
* Added do_action_ref_array 'before_clear_cart'.
* Bug fix: process of after about check stock.

= 0.5.4 =
* Hook to 'wpcf7_mail_sent' of Contact Form 7.
* Remove unnecessary php class object.
* Support to be working when no ajax.
* Added shortcode.
* Remove no use css color setting.
* Bug fix to update Stock Custom Field value function.

= 0.4.5 =
* Bug fix: check out of stock.

= 0.4.3 =
* Added to Custom Field keyword '#postid'. This keyword be replaced to formatted Post ID Number.
* Added to Custom Field keyword '#hidden'. This keyword use to hidden to the Custom Field.
* Added option chooser of product and supported extra charges.
* Fix internationalization message.
* Removed old shipping function.
* Others

= 0.3.7 =
* Changed way to ajax communication.

= 0.3.6 =
* Change coding:
   $value = & new Class(); /* before */
   $value =   new Class(); /* after */
  Will be able to run on more computers, without modification.

= 0.3.5 =
* Support to path separator for Windows.
* Stop the use of split function, use to explode function.

= 0.3.4 =
* Bug fix: Not display 'empty cart' in Cart Widget after check out.

= 0.3.3 =
* Translation for Russian has been newly created by Evgeny Vakhteev.

= 0.3.2 =
* Attention, this version has many changes. previous version is 0.2.13.

= 0.3.0 beta5 =
* Bug fix.

= 0.3.0 beta4 =
* Bug fix.
* Fix table tag and image tag.
* Can select table tag from table or dl.
* others

= 0.3.0 beta3 =
* Added some messages in setting screen.
* Using jQuery Form Plugin and jQuery Pines Notify (pnotify) Plugin.
* Fix: way to loading jQuery.
* others

= 0.3.0 beta2 =
* Bug fix: about 'Shop now closed option'.
* Put message if server don't have symlink function.

= 0.3.0 beta1 =
* Changed way to setting module for Contact Form 7.
* Changed way to setting of shipping.
* Added shop open/closed status option.
* Added number of stock manage.

= 0.2.13 =
* Added setting option: can setting Thanks page url, move this url after send order.

= 0.2.12 =
* Added setting options, can setting the text of 'Go To Cart' and 'Orderer Input screen'.

= 0.2.11 =
* Changed way to session start.
* Automatic create symbolic link cfshoppingcart.php for Contact Form 7 module, and add setting option for them. Accordingly removed installation step 3rd.

= 0.2.10 =
* Translation for Dutch has been newly created by Rene.

= 0.2.9 =
* Translation for Spanish has been newly created by Jorge Guerrero, Miguel Olivares, Estefan僘 Mu?z.

= 0.2.8 =
* Update Chinese language pack by Tsao Peter.

= 0.2.7 =
* Update Chinese language pack by Tsao Peter.

= 0.2.6 =
* Bug fixed.

= 0.2.5 =
* Translation for Chinese has been newly created by Tsao Peter.

= 0.2.4 =
* Added error message.

= 0.2.3 =
* Bug fixed. Don't work QF-GetThumb plugin on Cf Shopping Cart

= 0.2.2 =
* Translation for German has been newly created by Carola Fichtner.

= 0.2.1 =
* Additions: submit order to empty the cart. (Installation 6th step changed.)

= 0.2.0 =
* Include language file (pot file).
* Bug fixed.

= 0.1.7 =
* Bug fixed.
* Shipping configuration file location change. Measure against automatic upgrade.
* Add any tag and css class.

= 0.1.6 =
* bug fixed.


== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png


== More plugins. Thank you! ==

Name: Custom Field Template
URL: http://wordpress.org/extend/plugins/custom-field-template/

Name: Contact Form 7
URL: http://wordpress.org/extend/plugins/contact-form-7/

Name: QF-GetThumb-wb (branch QF-GetThumb)
URL: http://takeai.silverpigeon.jp/?cat=28

== Others ==

#I can not speak english very well.
#I would like you to tell me mistake my English, code and others.
#thanks.
Cf Shopping Cart Website: http://cfshoppingcart.silverpigeon.jp/
Blog: http://takeai.silverpigeon.jp/
AI.Takeuchi <takeai@silverpigeon.jp>


