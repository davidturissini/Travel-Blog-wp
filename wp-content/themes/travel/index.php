<?php get_header(); ?>
  <script>

    var LocationRouter = Backbone.Router.extend({
     routes:{
      "location/:location/":"locationShow",
      "":"root"
     },
     root:function () {
       window.locationView.hide()
     },
     locationShow:function(e) { 
      var location = window.map.locations.where({post_name:e})[0]
      window.locationView.show(location)
     }
    })
    window.router = new LocationRouter()

    var LocationShowView = Backbone.View.extend({
     model: Location,
     show:function (location) {
      this.model = location
      this.render()
    },
    hide:function () {
      this.$el.css({display:"none"})
    },
    render: function () {
     var view = this,
     loc = this.model,
     $div = $( document.getElementById("location-html") ).clone().attr({id:null})
     $(".title", $div).text(loc.get("post_title"));
     $(".city", $div).text(loc.get("city"));
     $(".country", $div).text(loc.get("country"));

     $(".content", view.$el).html( "test" )
     this.$el.css({display:"block"})
     loc.photos({
      success:function (photos) { 
       $.each(photos, function(idx, photo) {
       var $img = $( document.createElement("img") ).attr({src:photo.thumbnail("s"),height:"75px",width:"75px"}),
       $imgLink = $( document.createElement("a") ).addClass("photo").attr({title:photo.title,href:photo.url()}).append( $img );
       $(".photos", view.$el).append($imgLink);
      })
      $(".photos a", view.$el).lightBox();
      $(".photos", view.$el).slider()
     }})
     $(".close", view.$el).unbind("click").click(function () { 
      hideLocation()
     })
     $(document).bind("keyup", function (e) { if(e.keyCode == 27) { 
      hideLocation()
      $(document).unbind("keyup") }  
     })
     if( !window.isStage() ) {
      _gaq.push(["_trackEvent", "Location", "Viewed", loc.title])
     }
    }
   })

     var Location = Backbone.Model.extend({
       initialize: function (json) {
        this.has_visited = json.has_visited == "1"
       },
       journal_entries: function ( callbacks ) {
         callbacks = callbacks || {}
         return "asd"
       },
       photos: function( callbacks ) {
         var loc = this
         callbacks = callbacks || {}
         $.ajax({
           url:"http://api.flickr.com/services/rest",
           dataType:"jsonp",
           data: {
             api_key:"951c0814caade8b4fc2b381778269126",
             method: "flickr.photosets.getPhotos",
             format:"json",
             photoset_id:loc.get("flickr_set")
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
     })

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

    function hideLocation () {
      window.router.navigate("/", {trigger:true})
    }

   var LocationsCollection = Backbone.Collection.extend({
    model:Location
   })

   var Map = Backbone.View.extend({
    model:Location,
    initialize: function (e) {
      var map = this
      map.options = e.options
      map.locations = new LocationsCollection()
      map.locations.on("add", function (location) {
        map.drawMarker(location)
      })
    },
    render: function () {
     this.map = new google.maps.Map(this.el, this.options);
     return this
    },
    addLocation: function(location) {
     this.locations.push(location)
     this.drawMarker(location)
    },
    drawMarker: function(location) {
     var markerHash = {
        position: new google.maps.LatLng(location.get("lat"), location.get("lng")),
        map: this.map,
        title: location.get("post_title")
      }
      if( !location.has_visited ) {
        markerHash.icon = "http://eric.lubow.org/wp-content/uploads/2009/12/gmap_blue_icon.png" 
      }
      var marker = new google.maps.Marker(markerHash) 
      google.maps.event.addListener(marker, "click", function (e) {
       window.router.navigate("/" + location.get("post_type") + "/" + location.get("post_name") + "/", {trigger:true})
      })
    }
   })

   $(function () {
    var locations = new LocationsCollection()
    window.map = new Map({
     el:document.getElementById('map_canvas'), 
     options: {
      zoom: 5,
      center: new google.maps.LatLng(47.608478,-122.33036),
      mapTypeId: google.maps.MapTypeId.HYBRID
    }}).render()

    $( document.getElementsByClassName("location") ).each(function (idx, elem) {
     var location = new Location( $(elem).data("json") )
     window.map.addLocation(location)
    })
    
    window.locationView = new LocationShowView({
     el:document.getElementById("blog-content")
    }) 

    Backbone.history.start({pushState:true})
     
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


   })
  </script>
  <div id="map_canvas">
  
  </div> 
  <nav id="locations-nav">
   <ul>
   <?php
     foreach(Location::all() as $loc) { ?>
       <li>
         <article data-json='<?php echo $loc->to_json(); ?>' class="location">
           <a href="/"><?php echo $loc->post_title; ?></a>
         </article>
       </li>
       <?php } 
     ?>
   </ul>
  </nav>
<?php get_footer(); ?>
