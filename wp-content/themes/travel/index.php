<?php get_header(); ?>
  <script>
     function Location ( json ) {
       var loc = this
       $.extend(loc, json)
       loc.has_visited = loc.has_visited == "1"
       loc.journal_entries = []
       if( json.journal_entries ) {
         $.each(json.journal_entries, function (idx, e) {
          loc.journal_entries.push(new JournalEntry(e));
         })
       }

       loc.title = loc.post_title;

       loc.blogHTML = function () { 
         var $div = $( document.getElementById("location-html") ).clone().attr({id:null})
         $(".title", $div).text(loc.title);
         $(".city", $div).text(loc.city);
         $(".country", $div).text(loc.country);
         $.each(loc.journal_entries, function (idx, entry) {
           $(".journal-entries", $div).append(entry.blogHTML());
         }) 
         return $div.get(0);
       }

       loc.photos = function( callbacks ) {
         callbacks = callbacks || {}
         $.ajax({
           url:"http://api.flickr.com/services/rest",
           dataType:"jsonp",
           data: {
             api_key:"951c0814caade8b4fc2b381778269126",
             method: "flickr.photosets.getPhotos",
             format:"json",
             photoset_id:loc.flickr_set
           },
           jsonpCallback:"jsonFlickrApi",
           success:function (e) {
             var photos = []
             if( e.photoset ) {
             $.each(e.photoset.photo, function (idx, e) {
                 e.url = function () {
                 return "http://farm" + e.farm + ".static.flickr.com/" + e.server + "/" + e.id + "_" + e.secret + ".jpg"
                 } 
                 e.thumbnail = function (size) { 
                 var ary = e.url().split("."),
                     index = ary.length - 2
                   ary[index] = ary[index] + "_" + size
                   return ary.join(".");
                 }
                 photos.push(e)
               })
             }
             if( callbacks.success ) { callbacks.success(photos) }
           }
         })
       }

       return loc;
     }

     function JournalEntry(json) {
       var entry = this
         
       entry.blogHTML = function () {
         var $div = $( document.getElementById("journal-entry-html") ).clone().attr({id:null})
         $(".title", $div).html(json.post_title);
         $(".day", $div).html(json.formatted_day);
         $(".body", $div).html(json.post_content);
         return $div.get(0);
       }

       return entry;
     }

   $(function () {
    var myOptions = {
      zoom: 4,
      center: new google.maps.LatLng(47.608478,-122.33036),
      mapTypeId: google.maps.MapTypeId.HYBRID
    },
    infowindow = new google.maps.InfoWindow();
    window.locations = []
    window.findLocationByPostName = function (post_name) {
      for(var i = 0; i < window.locations.length; i++) {
       var l = window.locations[i]
       if( l.post_name == post_name ) { 
	return l 
	}
       }
    }
    window.map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
    window.map.dropTravelLocation = function (loc) {
      window.locations.push(loc)
      var markerHash = {
        position: new google.maps.LatLng(loc.lat, loc.lng),
        map: window.map,
        title: loc.title
      }
      if( !loc.has_visited ) {
        markerHash.icon = "http://eric.lubow.org/wp-content/uploads/2009/12/gmap_blue_icon.png" 
      }
      var marker = new google.maps.Marker(markerHash) 
      google.maps.event.addListener(marker, "click", function (e) {
       window.location.hash = loc.post_name
      })
    }

    function hideLocation () {
      window.location.hash = ""
    }

    function showLocation(loc) {
       var $blogContent = $( document.getElementById("blog-content") )
       if( !loc ) { 
        $blogContent.css({display:"none"})
        return 
       }
       $(".content", $blogContent).html( loc.blogHTML() )
       $blogContent.css({display:"block"})
       loc.photos({
        success:function (photos) { 
          $.each(photos, function(idx, photo) {
            var $img = $( document.createElement("img") ).attr({src:photo.thumbnail("s"),height:"75px",width:"75px"}),
            $imgLink = $( document.createElement("a") ).addClass("photo").attr({title:photo.title,href:photo.url()}).append( $img );
            $(".photos", $blogContent).append($imgLink);
          })
          $(".photos a", $blogContent).lightBox();
          $(".photos", $blogContent).slider()
        }})
       $(".close", $blogContent).unbind("click").click(function () { 
         hideLocation()
       })
       $(document).bind("keyup", function (e) { if(e.keyCode == 27) { 
         hideLocation()
         $(document).unbind("keyup") }  
       })
     }

   jQuery.fn.extend({
     slider: function () {
       this.each(function (idx, elem) {
         var width = elem.offsetWidth,
         children = Array.prototype.slice.call(elem.children),
         container = document.createElement("div"),
         innerWidth = 0
         elem.innerHTML = ""
         elem.appendChild(container);
         [].forEach.call(children, function (child) {
           container.appendChild(child)
           innerWidth += child.offsetWidth
         }) 
         if( innerWidth > elem.offsetWidth ) {
           elem.style.overflowX = "scroll"
         }
         container.style.width = innerWidth + "px"
       }) 
     }
   })

   $.address.change(function () {
     showLocation(window.findLocationByPostName(window.location.hash.replace("#", "")))
   })

   <?php
     foreach(Location::all() as $loc) { ?>
       window.map.dropTravelLocation(new Location(<?php echo $loc->to_json(); ?>));
       <?php } 
     ?>
   })
  </script>
  <div id="map_canvas">
  
  </div> 
<?php get_footer(); ?>
