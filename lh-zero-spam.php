<?php
/**
 * Plugin Name: LH Zero Spam
 * Plugin URI: http://lhero.org/plugins/lh-zero-spam/
 * Description: This is a very lightweight anti spam plugin utilising JavaScript nonce security to prevent comment and registration spam.
 * Version: 1.01
 * Author: Peter Shaw
 * Author URI: http://shawfactor.com
 == Changelog ==

= 1.0 =
* Initial release

= 1.01 =
* Code improvement

License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2011  Peter Shaw  (email : pete@localhero.biz)


This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published bythe Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class LH_zero_spam_plugin {


function write_nonce() {

echo "<span id=\"lh_zero_spam-nonce_holder\" data-lh_zero_spam_nonce=\"".wp_create_nonce("lh_zero_spam_nonce")."\"></span>\n";


}



function add_custom_comment_fields($fields) {

if (!is_user_logged_in() ) {

  

$fields[ 'lh_zero_spam' ] =   "<noscript><strong>Please switch on Javascript to enable commenting</strong></noscript>\n<input id=\"lh_zero_spam-nonce_value\" name=\"lh_zero_spam-nonce_value\" type=\"hidden\" size=\"15\" value=\"\" />\n\n";

add_action('wp_footer', array($this,"write_nonce"));

wp_enqueue_script('lh_zero_spam-script', plugins_url( '/assets/lh-zero-spam.js' , __FILE__ ), array(), '0.03', true  );

}

return $fields;

}



function add_custom_registration_fields($fields) {
?>
<noscript><strong>Please switch on Javascript to enable registration</strong></noscript>
<input id="lh_zero_spam-nonce_value" name="lh_zero_spam-nonce_value" type="hidden" size="15" value="" />
<?php

add_action('login_footer', array($this,"write_nonce"));

wp_enqueue_script('lh_zero_spam-script', plugins_url( '/assets/lh-zero-spam.js' , __FILE__ ), array(), '0.03', true  );

}


public function preprocess_comment( $commentdata ) {
		$valid = false;

		if ( is_user_logged_in() ) {

return $commentdata;

		} else {

if ( wp_verify_nonce( $_POST['lh_zero_spam-nonce_value'], "lh_zero_spam_nonce") ) {


return $commentdata;


} else {

print_r($_POST);

die;


}


}

		
}



public function preprocess_registration( $errors, $sanitized_user_login, $user_email ) {
if ( !wp_verify_nonce( $_POST['lh_zero_spam-nonce_value'], "lh_zero_spam_nonce") ) {

$errors->add( 'spam_error', __( '<strong>ERROR</strong>: Your comment may be spam or you need to activate javascript.', 'lh_zero_spam' ) );

}

return $errors;


}


function __construct() {

add_filter('comment_form_default_fields', array($this,"add_custom_comment_fields"));

add_action( 'preprocess_comment', array( $this, 'preprocess_comment' ) );

add_action( 'register_form', array($this,"add_custom_registration_fields"));

add_filter( 'registration_errors', array( $this, 'preprocess_registration' ), 10, 3 );

}

}


$lh_zero_spam = new LH_zero_spam_plugin();

?>