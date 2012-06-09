<?php get_header(); ?>
  <script>
    var LocationRouter = Backbone.Router.extend({
     routes:{
      "location/:location/":"locationShow",
      "":"root"
     },
     root:function () {
       window.map.hideLocation()
     },
     locationShow:function(e) { 
      var location = window.map.locations.where({post_name:e})[0]
      map.focusLocation(location)
     },
     navigateToLocation: function(loc) {
      if( loc ) {
       this.navigate("/" + loc.get("post_type") + "/" + loc.get("post_name") + "/", {trigger:true})
      } else {
       this.navigate("/", {trigger:true})
      }
     }
    })

    var LocationMenu = Backbone.View.extend({
      render:function () {
       var map = this.options.map,
       $locations = $( this.el.getElementsByClassName("location") )
       map.on("locationChanged", function (event) {
        var location = event.location
        $locations.removeClass("selected")
        if( location ) { 
         var $loc = $locations.filter(function (idx, e) { 
          return location.get("ID") == $(e).data().json.ID  
         }).first()
         $loc.addClass("selected")
        }
       }) 

       $locations.click(function (e) {
        e.preventDefault()
        var $target = $(e.currentTarget)
        if( !$target.hasClass("selected") ) {
         var location = map.locations.where({ID:$target.data().json.ID})[0] 
         map.router.navigateToLocation(location)
        } else {
         map.router.navigateToLocation(null) 
        }
       })
      }
    })

    var JournalEntryView = Backbone.View.extend({
      model:JournalEntry,
      render: function () {
       var journal = this,
       $div = $( document.getElementById("journal-entry-html") ).clone().attr({id:null})
       $(".title", $div).html(journal.model.get("post_title"));
       $(".day", $div).html(journal.model.get("formatted_day"));
       $(".body", $div).html(journal.model.get("post_content"));
       return $div.get(0);
      }
    })

    var LocationShowView = Backbone.View.extend({
     model: Location,
     initialize: function () {
       this.journalView = new JournalEntryView()
     },
     show:function (location) {
      this.model = location
      this.render()
     },
     hide:function () {
      this.$el.css({display:"none"})
     },
     renderJournalEntries:function (entries) {
       var locView = this
       entries.forEach(function(entry, idx) {
         var journalView = new JournalEntryView({model:entry})
         $(".journal-entries", locView.el).append(journalView.render())
       })
     },
     showHTML: function () {
      var view = this
      this.$el.css({display:"block"})
      $(".close", view.$el).unbind("click").click(function () { 
       window.map.router.navigateToLocation(null)
      })
      $(document).bind("keyup", function (e) { if(e.keyCode == 27) { 
       window.map.router.navigateToLocation(null)
       $(document).unbind("keyup") }  
      })
      if( !window.isStage() ) {
       _gaq.push(["_trackEvent", "Location", "Viewed", view.model.get("post_title")])
      }
     },
     showPhotos: function () {
      var view = this
      this.model.photos({
       success:function (photos) { 
        $.each(photos, function(idx, photo) {
        var $img = $( document.createElement("img") ).attr({src:photo.thumbnail("s"),height:"75px",width:"75px"}),
        $imgLink = $( document.createElement("a") ).addClass("photo").attr({title:photo.title,href:photo.url()}).append( $img );
        $(".photos", view.$el).append($imgLink);
       })
       $(".photos a", view.$el).lightBox({imageBtnClose:"/images/lightbox-btn-close.gif"});
       $(".photos", view.$el).slider()
      }})
 
     },
     render: function () {
      var view = this,
      loc = this.model,
      options = options || {},
      $div = $( document.getElementById("location-html") ).clone().attr({id:null}),
      $content = $(".content", view.el)
      $content.empty()
      $(".title", $div).text(loc.get("post_title"));
      $(".city", $div).text(loc.get("city"));
      $(".country", $div).text(loc.get("country"));
      $(".description", $div).text(loc.get("post_content"))
      $content.append($div)
      view.showPhotos() 

      loc.journal_entries({
       success:function (entries) {
        view.renderJournalEntries(entries)
       }
      })
      view.showHTML()
     }
    })

     var Location = Backbone.Model.extend({
       initialize: function (json) {
        this.has_visited = json.has_visited == "1"
       },
       journal_entries: function ( callbacks ) {
         var loc = this
         callbacks = callbacks || {}
         if(!loc._journal_entries) {
           loc._journal_entries = []
         $.ajax({
           url: "/wp-admin/admin-ajax.php",
           data: {
             action:"journal_from_location",
             location_id:loc.get("ID")
           },
           success: function (e) {
            $.each(JSON.parse(e), function (idx, json) {
              loc._journal_entries.push(new JournalEntry(json))
            })
            if( callbacks.success ) { callbacks.success(loc._journal_entries) }
           },
           error: function () {
 
           }
         })
         } else {
           if( callbacks.success ) { callbacks.success(loc._journal_entries) }
         }
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

     var JournalEntry = Backbone.Model.extend({
       blogHTML: function () {
       }
     })

   var LocationsCollection = Backbone.Collection.extend({
    model:Location,
    initialize:function () {
      this.selected = null
    },
    setSelected: function (location) {
      this.selected = location
      this.trigger("selectedChange", {location:location})
    }
   })

   var Map = Backbone.View.extend({
    model:Location,
    initialize: function (e) {
     var map = this
     map.options = e.options
     map.router = new LocationRouter()
     map.locations = new LocationsCollection()
     map.locations.on("add", function (location) {
      map.drawMarker(location)
     })
     map.locations.on("selectedChange", function (event) {
      map.trigger("locationChanged", {location:event.location}) 
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
    hideLocation: function () {
      window.locationView.hide()
      this.locations.setSelected(null)
    },
    focusLocation: function (location) {
      var map = this,
      eventHandler = google.maps.event.addListener(map.map, "idle", mapIdle)
      window.locationView.hide()
      function mapIdle() {
       eventHandler.remove()
       setTimeout(function () {
        window.locationView.show(location)
       }, 700)
       
      }
      var currentCenterLng = Math.round(map.map.center.lng, 4),
       currentCenterLat = Math.round(map.map.center.lat(), 4),
       currentCenterLng = Math.round(map.map.center.lng(), 4),
       locLat = Math.round(location.get("lat"), 4),
       locLng = Math.round(location.get("lng"), 4)

     if(locLat != currentCenterLat || locLng != currentCenterLng) {
      var zoomLevel = 10
      if( window.map.map.zoom < zoomLevel ) {
       window.map.map.setZoom(zoomLevel)
      }
       map.map.panTo( new google.maps.LatLng(location.get("lat"), location.get("lng")) )
      } else {
       window.locationView.show(location)
      }
      map.locations.setSelected(location) 
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
       map.router.navigateToLocation(location)
     })
    }
   })

   $(function () {
    var locations = new LocationsCollection()
    window.map = new Map({
     el:document.getElementById('map_canvas'), 
     options: {
      zoom: 4,
      center: new google.maps.LatLng(41.08193934195916,-93.67801624999997),
      mapTypeId: google.maps.MapTypeId.HYBRID
    }}).render()

    $( document.getElementsByClassName("location") ).each(function (idx, elem) {
     var location = new Location( $(elem).data("json") )
     window.map.addLocation(location)
    })

    new LocationMenu({
     el:document.getElementById("locations-nav"),
     map:window.map
    }).render()
    
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
   <header>Places visited</header>
   <ul>
   <?php
     foreach(Location::visited() as $loc) { ?>
       <li>
        <a class="location<?php if( $loc->has_visited == 0 ) { echo " not-visited"; } ?>" data-json='<?php echo $loc->to_json(); ?>' href="/"><?php echo $loc->city; ?>, <?php echo $loc->country; ?></a>
       </li>
       <?php } 
     ?>
   </ul>
   <header>Upcoming trips</header>
   <ul>
   <?php
     foreach(Location::non_visited() as $loc) { ?>
       <li>
        <a class="location<?php if( $loc->has_visited == 0 ) { echo " not-visited"; } ?>" data-json='<?php echo $loc->to_json(); ?>' href="/"><?php echo $loc->city; ?>, <?php echo $loc->country; ?></a>
       </li>
       <?php } 
     ?>
   </ul>
  </nav>
<?php get_footer(); ?>
