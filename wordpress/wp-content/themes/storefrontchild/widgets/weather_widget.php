<?php
class Weather_Widget extends WP_Widget {

	// Register widget with WordPress.
	 
	function __construct() {
		parent::__construct(
			'weather_widget', 
			'Weather Widget',
			array( 
                'description' => __( 'Display the temperature of a City' ), 
                ) 
		);
        
        add_action('widgets_init', function(){
            register_widget('Weather_Widget');
        });
	}

    public $args = array(
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget' => '</div>',

    );

	// Front-end display of widget.
	// Gets the Name of the City from the cities custom post type. Then Gets the latitude and longitude which are then used for API call.
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] . ' City' ) . $args['after_title'];
		}

        // Get City title
		$city_title = $instance['title'];

        // Query to search for the city post by title
        $cities_query = new WP_Query(array(
            'post_type' => 'cities',
            'title' => $city_title, // Search for a post with the exact title
            'posts_per_page' => 1, // We only need one post with the matching title
        ));

        if ($cities_query->have_posts()) {
            while ($cities_query->have_posts()) {
                $cities_query->the_post();

                // Retrieve longitude and latitude meta values
                $longitude = get_post_meta(get_the_ID(), 'longitude', true);
                $latitude = get_post_meta(get_the_ID(), 'latitude', true);

                // Display the city data (e.g., title, content, longitude, and latitude)
                echo '<p>' . get_the_content() . '</p>';
                echo '<p><strong>Longitude:</strong> ' . esc_html($longitude) . '</p>';
                echo '<p><strong>Latitude:</strong> ' . esc_html($latitude) . '</p>';
            }
            wp_reset_postdata();
        } else {
            echo 'No city found with the given title.';
        }

        // Accessing the weather api based on city longitude and latitude

        $ch = curl_init();

        $url = 'api.openweathermap.org/data/2.5/forecast?lat='.$latitude.'&lon='.$longitude.'&appid=c631bba0d4b184109b01670449db0ed9&units=metric';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);

        if($e = curl_error($ch)){
            echo $e;
        }
        else{
            $decoded = json_decode($resp, true);
            $current_forecast = $decoded['list'][1];
            $current_temp = $current_forecast['main']['temp']."°C";
            echo '<p><strong>Temperature:</strong> ' . esc_html($current_temp) . '</p>';
        }

        curl_close($ch);

		echo $args['after_widget'];
	}

	// Back-end widget form.
	 
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );

		?>

		<p>
		    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title', 'text_domain' ); ?>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </label> 
		    
		</p>

		<?php 
	}

	// Updates Widget Values
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} 

$weather_widget = new Weather_Widget();

?>

