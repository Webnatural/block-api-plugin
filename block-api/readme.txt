=== API Block Widget ===
Contributors: Dariusz Zielonka
Tags: widget, API, block
Requires at least: 4.9
Tested up to: 5.8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

API Block Widget is a WordPress plugin that provides a widget and a block to display the response from an API as a block. It allows you to configure the widget's title and content and fetches the API response using the WP HTTP API.

If your WordPress version and active theme do not support Gutenberg, you can use the API Block Widget. However, if Gutenberg is supported, the plugin provides a block named "Block API" that renders the API response as a block in the Gutenberg editor.

== Installation ==

1. Upload the `api-block-widget` folder to the `/wp-content/plugins/` directory or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

To use the API Block Widget:
1. Go to the 'Appearance' -> 'Widgets' screen to add the API Block Widget to your desired widget area.
2. Configure the widget by providing a title and content.
3. Save the widget settings, and the API response will be displayed in the widget area.

To use the "Block API" block (can be used as a widget in 'Appearance' -> 'Widgets'):
1. Create or edit a post or page in the Gutenberg editor.
2. Add a new block and search for the "Block API" block.
3. Select the "Block API" block to insert it into your content.
4. Configure the block by providing a title.
5. Publish or update your post or page, and the API response will be displayed as a block.

== Frequently Asked Questions ==

= How do I customize the API URL and data? =

By default, the widget and the "Block API" block are configured to fetch the API response from `https://httpbin.org/post` and sends a sample data payload. To customize the API URL and data, you can modify the code inside the respective methods of the `API_Block_Widget` class and `Block_API_Plugin` class.

For the widget:
1. Open the `api-block-widget/includes/class-api-block-widget.php` file.
2. Look for the following lines:
   ```php
   $api_url  = 'https://httpbin.org/post';
   $api_data = array(
       'title'   => 'Title',
       'content' => 'Lorem ipsum dolor sit amet',
   );
