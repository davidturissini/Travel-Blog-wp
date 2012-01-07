<?php get_header(); ?>
  <script>
     function Location ( json ) {
       var location = this
       if( json.has_visited[0] ) {
         location = {
           city:json.city[0],
           country:json.country[0],
           lat:json.lat[0],
           lng:json.lng[0],
           has_visited:json.has_visited[0] == "1"
         } 
       } else {
         location = json
       }
       return location
     }
   $(function () {
    var myOptions = {
      zoom: 5,
      center: new google.maps.LatLng(47.608478,-122.33036),
      mapTypeId: google.maps.MapTypeId.HYBRID
    };
    window.map = new google.maps.Map(document.getElementById('map_canvas'),
        myOptions);
    window.map.dropTravelLocation = function (location) {
      var markerHash = {
        position: new google.maps.LatLng(location.lat, location.lng),
        map: window.map,
        title: location.title
      }
      if( !location.has_visited ) {
        markerHash.icon = "http://eric.lubow.org/wp-content/uploads/2009/12/gmap_blue_icon.png" 
      }
      new google.maps.Marker(markerHash) 
    }
   <?php
     $locations = get_posts(array('numberposts' => -1, 'post_type' => "location"));
     foreach($locations as $loc) { ?>
       window.map.dropTravelLocation(new Location(<?php echo json_encode(get_post_custom($loc->ID)); ?>));
       <?php } 
     ?>
   })
  </script>
  <div id="map_canvas">
  
  </div> 
<?php get_footer(); ?>
