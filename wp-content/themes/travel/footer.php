  <div id="blog-content">
    <a class="close">Return to map</a>
    <div class="content">
 
    </div>
  </div>
  <article id="location-html" class="location">
    <h2 class="title"><h2>
    <h5 class="geo">
      <span class="city"></span>, <span class="country"></span>
    </h5>
    <div class="description"></div>
    <div class="photos">
 
    </div>
    <div class="journal-entries">
      <article class="journal-entry" id="journal-entry-html">
       <h3 class="day"></h3>
       <div class="body"></div>
      </article>
    </div>
  </article>


<?php if ( current_user_can('manage_options') ) { ?>
 <div id="edit-mode">
   <label for="edit-mode-checkbox">Edit mode?</label>
   <input type="checkbox" id="edit-mode-checkbox" />
 </div>
 <div id="location-form">
   <form method="post" action="/save_location.php" class="location-form">
     <input type="text" name="title" /> 
     <input type="text" name="city" />
     <input type="text" name="country" />
     <label>Visited?</label>
     <input type="checkbox" name="has_visited" />
     <input type="hidden" name="lat" value="" />
     <input type="hidden" name="lng" value="" />
     <input type="submit" value="Save" />
   </form>
 </div>
 <script>
  $(function () {
  
   $( document.getElementById("edit-mode-checkbox") ).change(function (e) {
    if(e.target.checked) { editMode() } else { stopEditMode() }
   })
  
   function editMode() {
     var $formDiv = $( document.getElementById("location-form") )
     google.maps.event.addListener(window.map, 'click', function (mapEvent) {
     var $form = $formDiv.clone(),
     formId = "location-" + new Date().getTime()
     $("form", $form).attr({id:formId})
     geocoder = new google.maps.Geocoder();
     geocoder.geocode({latLng:mapEvent.latLng}, function (e) {
     var address = (function () {
         var hash
         for(var i in e) {
           if( e[i].types[0] == "locality" ) {
             hash = e[i]
           }
         }
         if( hash ) { 
          return hash.formatted_address 
         }
         })(),
         country = (function () {
           for(var i in e) {
             if( e[i].types[0] == "country" ) { return e[i].formatted_address }
           }
         })(), city
         if( address ) {
          city = address.split(",")[0]
         }
     $("input[name='country']", $form).attr({value:country});
     $("input[name='city']", $form).attr({value:city});
     $("input[name='lat']", $form).attr({value:mapEvent.latLng.lat()});
     $("input[name='lng']", $form).attr({value:mapEvent.latLng.lng()});

     var marker = new google.maps.Marker({
       position: mapEvent.latLng, 
       map: map,
       title:"Hello World!"
     }),
     infowindow = new google.maps.InfoWindow(
       { content: $form.html(),
         size: new google.maps.Size(50,50)
       });
     infowindow.open(window.map,marker);
     google.maps.event.addListener( infowindow, "domready", function () {
     var $form = $( document.getElementById(formId) )
     $form.submit(function (e) { 
         e.preventDefault()
         var dataHash = {
            action:"save_location",
            title:$("input[name='title']", $form).val(),
            city:$("input[name='city']", $form).val(),
            country:$("input[name='country']", $form).val(),
            lat:$("input[name='lat']", $form).val(),
            lng:$("input[name='lng']", $form).val(),
            has_visited:$("input[name='has_visited']").get(0).checked
            }
         $.ajax({
           url:"/wp-admin/admin-ajax.php",
           type:"post",
           data:dataHash,
           success:function(e) { 
            infowindow.close() 
            marker.setMap(null);
            window.map.dropTravelLocation(new Location(dataHash));
           }
         })
       })
       google.maps.event.addListener(infowindow, "closeclick", function (e) {
         marker.setMap(null)
       })
     })
    })
   })
   }

   function stopEditMode() {
    google.maps.event.clearListeners(window.map, 'click') 
   }
  })
 </script>
 <script src="/wp-content/themes/travel/jquery.lightbox-0.5.min.js"></script>
<?php } ?>

</body>
</html>
