<?php
/*
Plugin Name: KnitTV Ads
Description: Simple Widget for KnitTV Ads
Version:     0.1
Author:      Devon Sawatzky
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/
define("WP_DEBUG_LOG", true);

/*
 * Enqueue Styles and Scripts
 */
function knittv_ads_enqueue_scripts() {
	wp_enqueue_style('knittv-ads', plugin_dir_url(__FILE__) . '/style.css');
	wp_enqueue_script("knittv-ads-adblock-detect", plugin_dir_url(__FILE__) . "/script.js");
}

/*
 * Widget
 */
function knittv_ads_enqueue_widget_scripts() {
	wp_enqueue_script("knittv-ads-widget-edit-script", plugin_dir_url(__FILE__) . "/edit.js", array(), false, true);
	wp_register_style("knittv-ads-widget-edit-style", plugin_dir_url(__FILE__) . "/edit.css");
	wp_enqueue_style("knittv-ads-widget-edit-style");
}
class KnittvAds extends WP_Widget {
	function __construct() {
		parent::__construct(false, __("Ads"), array('description'=>'Simple Widget for KnitTV Ads'));
	}
	function widget($args, $instance) {
		echo "<div class='knittv-ads adsbox'>";
		echo "<h3 class='widget-title'>${instance["title"]}</h3>";
		foreach($instance["ads"] as $i=>$ad) {
			echo "<div class='knittv-ad' id='knittv-ad-$i'>$ad</div>";
		}
		echo "</div>";
		echo "<div class='knittv-ads knittv-adblock-message' style='display: none'>";
		echo "<h3 class='widget-title'>${instance["adblock-title"]}</h3>";
		echo "<p>${instance["adblock-message"]}</p>";
		echo "</div>";
	}
	function form($instance) {
		$nonce=wp_create_nonce('knittv_ads_widget_form');
		echo "<input type='hidden' name='knittv_ads_widget_nonce' value='$nonce'>";
		$this->knittv_input($instance, "title", "Title");
		echo "<div class='adschooser'>";
		echo "<h3>Ads</h3>";
		$this->knittv_ads($instance);
		echo "</div>";
		echo "<p>Write a simple, tasteful message to AdBlock users. Make sure to follow the <a href='https://easylist.to/2013/05/10/anti-adblock-guide-for-site-admins.html'>EasyList guidelines</a> otherwise the message itself may be blocked as well!</p>";
		$this->knittv_input($instance, "adblock-title", "Adblock Message Title", "You Are Using AdBlock");
		$this->knittv_input($instance, "adblock-message", "Adblock Message", "We don't love ads either, but we do have to pay the bills! Please consider donating to support our site.", "textarea");
	}
	function knittv_ads($instance) {
		$n=0;
		while(isset($instance["ads"]) && isset($instance["ads"][$n]) && $instance["ads"][$n]) {
			$this->knittv_ad_input($n, $instance["ads"][$n]);
			$n++;
		}
		$this->knittv_ad_input($n, "");
	}
	function knittv_ad_input($n, $val) {
		$name=esc_attr($this->get_field_name("ad" . sprintf("%02d", $n)));
		$class=($n==0)?" class='first'":"";
		$value=esc_attr($val);
		echo "<input$class name='$name' value='$value' autocomplete='off'></input>";
	}
	function knittv_input($instance, $attribute, $label, $default="", $tagName="input") {
		$name=esc_attr($this->get_field_name($attribute));
		$id=esc_attr($this->get_field_id($attribute));
		$value=esc_attr(isset($instance[$attribute])? $instance[$attribute]: $default);
		if($tagName=="textarea") {
			echo "<label for='$id'>$label</label><$tagName id='$id' name='$name'>$value</$tagName>";
		}
		else {
			echo "<label for='$id'>$label</label><$tagName id='$id' name='$name' value='$value'></$tagName>";
		}
	}
	function update($new, $old) {
		$instance=array();
		if(wp_verify_nonce($_POST["knittv_ads_widget_nonce"], "knittv_ads_widget_form")) {
			#extract ads array
			$adkeys=preg_grep('/^ad[0-9][0-9]$/', array_keys($new));
			asort($adkeys);
			$instance["ads"]=[];
			foreach($adkeys as $i) {
				if($new[$i]) {
					array_push($instance["ads"], $new[$i]);
				}
			}
			$instance["title"]=$new["title"];
			$instance["adblock-title"]=$new["adblock-title"];
			$instance["adblock-message"]=$new["adblock-message"];
			return $instance;
		}
		else {
			return $old;
		}
	}
}
function knittv_ads_register_widgets() {
	register_widget("KnittvAds");
}

/*
 * Actually calling the stuff
 */
if(defined('ABSPATH')) { #don't actually do anything if this file was directly requested
	add_action( 'wp_enqueue_scripts', 'knittv_ads_enqueue_scripts' );
	add_action('widgets_init', 'knittv_ads_register_widgets');
	if(is_admin()) {
		add_action( 'admin_enqueue_scripts', 'knittv_ads_enqueue_widget_scripts' );
	}
}
