<?php
/*
Plugin Name: Dave and Melissa Travel Adventures
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Allows Melissa and I to tie together our travel plans / adventures
Version: The Plugin's Version Number, e.g.: 1.0
Author: David Turissini 
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

register_activation_hook(__FILE__,'install_travel_locations');

function install_travel_locations () {
  global $wpdb;
  $table_name = $wpdb->prefix . "travel_locations";

  $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  title varchar(255) NOT NULL,
          country varchar(255),
          city varchar(255),
	  lat decimal(30,15) NOT NULL,
	  lng decimal(30,15) NOT NULL,
          has_visited tinyint DEFAULT 0,
	  UNIQUE KEY id (id)
	);";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
  }

add_action('admin_menu', 'travel_menu');

function travel_menu () {
 add_menu_page( "Travel Location", "Travel", "activate_plugins", "travel-locations", "travel_locations_menu", "", 9);
 return "travel-menu";
 }

function travel_locations_menu () {
  global $wpdb;
  if( isset($_POST['travel-name']) ) {
    $name = $_POST['travel-name']; 
    $sql = sprintf("INSERT INTO %s (name, country, lat, lng, has_visited) VALUES ('%s', '', '', '', '0')",
      $wpdb->prefix . "travel_locations",
      $name 
      );
    echo $sql;
    $wpdb->query($sql);
    }
  $html = '
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <div class="wrap">
    <h2>Add New Place</h2>
    <form name="travel-location" method="post" action="">
      <div id="titlediv">
        <input id="title" type="text" name="travel-name" placeholder="Title" />
      </div>
      <div id="map_canvas" style="width:100%;height:500px">

      </div>
      
    </form>
    <script>
      $(function () {
      var map;
      function initialize() {
        var myOptions = {
          zoom: 8,
          center: new google.maps.LatLng(-34.397, 150.644),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);
      }
      initialize()
      })
    </script>
    </div>
  ';
  echo $html;
 }

?>
