===WooCommerce Transmit SMS ===
Contributors: Transmit SMS Team
Donate link: 
Plugin URI:https://wordpress.org/plugins/woocommerce-transmit-sms/
Tags:  SMS, Notifications, Order Confirmations, Delivery Notifications, Text Message Notifications, Text Message Alerts, WooCommerce 
Requires at least: 3.5
Stable tag: 1.4
Tested :3.8
Tested up to: 3.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Send SMS updates to customers when their order status is updated and receive an SMS message when a customer places a new order

== Installation ==

1. Upload the 'woocommerce-transmit-sms' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Klik menu Woocomerce > settings > integration (tab)> Transmit SMS Notifications.
3. Enter your Burst SMS API key and secret in the Settings tab.
4. Set your options for 'woocommerce-transmit-sms'

= What is a Burst SMS API key? = 

To send SMS you will need to sign up for a BURST SMS account
and purchase some SMS credit. When you sign up you'll be given an API key.

= What format should the mobile number be in? =

All mobile numbers Format would accepted, you can entered with in international format or local format, but remember to choose right country.

== Frequently asked questions ==

= A question that someone might have =

An answer to that question.
== Screenshots ==
1. Woocommerce Transmit Sms backend 1.
2. Woocommerce Transmit Sms SMS backend 2.
3. Woocommerce Transmit Sms SMS frontend.


== Changelog ==

= 1.0 =
 * Basic code development
= 1.1 =
 * Changing code who calling woowcomerce meta (not avalible on new version woocomerce)
= 1.2 =
 * Removing calling jquery from external source
 * Changing and modify some code to fit with wordpress.org
= 1.3 =
 * Adding function to detect changing status order automatically by other plugin
= 1.4 =
 * Decoding message
  
== Upgrade notice ==
Latest stable version is 1.4, please upgrade to version 1.4