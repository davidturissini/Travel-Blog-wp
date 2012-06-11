<?php
  add_action( 'init', 'create_post_type' );
  function create_post_type() {
	register_post_type( 'journal_entry',
		array(
			'labels' => array(
				'name' => __( 'Journal Entries' ),
				'singular_name' => __( 'Journal Entry' )
			),
		'public' => true,
		'has_archive' => true,
		)
	);

    register_post_type( 'location',
                array(
                        'labels' => array(
                                'name' => __( 'Locations' ),
                                'singular_name' => __( 'Location' )
                        ),
                'public' => true,
                'has_archive' => false,
                'hierarchical' => true,
	        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
                )
        );
  }

  class Content {
       public function __construct( $params = array() ) {
         foreach($params as $key => $value) {
           $this->$key = $value;
         }
         $custom = get_post_custom($this->ID);
         foreach($custom as $key => $value) {
           $this->$key = $value[0];
         }
       }

       public function to_json () {
         return json_encode( $this );
       }
 
  }

  class JournalEntry extends Content {
    public static function by_location($location, $options = array()) {
      $options = array_merge($options, array(
        'post_type' => 'journal_entry', 
        'meta_key' => 'location_id', 
        'meta_value' => $location->ID, 
        'orderby' => "day", 
        'order' => "ASC"
      ));
      $raw_entries = get_posts($options);
      $entries = array();
      foreach($raw_entries as $entry) {
        $entries[] = new self($entry); 
      }
      return $entries;
    }  
   
    public function __construct( $params = array() ) {
      parent::__construct($params);
      $this->formatted_day = date("l, F d, Y", strtotime($this->day));
    }
  }

  class Location extends Content {
    public static function all($options = array()) {
      $options = array_merge(array('numberposts' => -1, 'post_type' => "location"), $options);
      $raw_locations = get_posts( $options );
      $locations = array();
      foreach($raw_locations as $location) {
        $loc = new self($location);
        $locations[] = $loc;
      }
      return $locations;
    }

    public static function visited($options = array()) {
      $options = array_merge(array('meta_key' => 'has_visited', 'meta_value' => 1, 'numberposts' => -1, 'post_type' => "location"), $options);
      $raw_locations = get_posts( $options );
      $locations = array();
      foreach($raw_locations as $location) {
        $loc = new self($location);
        $locations[] = $loc;
      }
      return $locations;
    }

    public static function non_visited($options = array()) {
      $options = array_merge(array('meta_key' => 'has_visited', 'meta_value' => 0, 'numberposts' => -1, 'post_type' => "location"), $options);
      $raw_locations = get_posts( $options );
      $locations = array();
      foreach($raw_locations as $location) {
        $loc = new self($location);
        $locations[] = $loc;
      }
      return $locations;
    }

    public static function find($id) {
      $raw_location = get_post( $id );
      return new self($raw_location);
    }
  
    public function journal_entries( $options = array() ) {
      if( !$this->journal_entries ) {
        $this->journal_entries = JournalEntry::by_location($this, $options);
      }
      return $this->journal_entries;
      }
  
    public function to_json( $options = array() ) {
      return parent::to_json($options);
    }

  }

  function renderLocation($location) {
  ?>
   <article id="location-html" <?php if($location) { echo "data-id=\"". $location->ID . "\""; } ?>  class="location">
    <header>
     <h2 class="title"><?php echo $location->post_title; ?><h2>
     <h5 class="geo">
      <span class="city"><?php echo $location->city; ?></span>, <span class="country"><?php echo $location->country; ?></span>
     </h5>
    </header>
    <p class="description"><?php echo $location->post_content; ?></p>
    <div class="photos">

    </div>
    <div class="journal-entries">
      <?php foreach($location->journal_entries() as $entry) { ?>
       <article class="journal-entry">
        <h3 class="day"><?php echo $entry->day; ?></h3>
        <div class="body"><?php echo $entry->post_content; ?></div>
       </article>
      <?php } ?>
      <article class="journal-entry" id="journal-entry-html">
       <h3 class="day"></h3>
       <div class="body"></div>
      </article>
    </div>
  </article>

  <?php
  }
?>
