<?php

// Enqueue the style of child theme
add_action( 'wp_enqueue_scripts', 'storefrontchild_enqueue_styles' );

function storefrontchild_enqueue_styles() {
	wp_enqueue_style( 
		'storefrontchild-parent-style', 
		get_parent_theme_file_uri( 'style.css' )
	);
}

// Custom Widgets
include_once('widgets/weather_widget.php');

// Creation of Cities custom post type
function city_custom_post_type() {
	register_post_type('cities',
		array(
			'labels'      => array(
				'name'          => __('Cities'),
				'singular_name' => __('City')
			),
				'public'      => true,
				'has_archive' => true
                
		)
	);
}
add_action('init', 'city_custom_post_type');

// Creation of taxonomy for countries
function cities_taxonomy() {
    register_taxonomy(
        'countries',  
        'cities',             
        array(
            'hierarchical' => true,
            'label' => 'Countries', 
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'themes',    
                'with_front' => false  
            )
        )
    );
}
add_action( 'init', 'cities_taxonomy');



//Creation of metabox and fields for latitude and longitude

//Create a metabox for the fields
function cities_metaboxes( ) {
    global $wp_meta_boxes;
    add_meta_box('postfunctiondiv', __('Latitude and Longitude'), 'cities_metaboxes_html', 'cities', 'normal', 'high');
 }
 add_action( 'add_meta_boxes_cities', 'cities_metaboxes' );


//Create fields for Longitude and Latitudes
 function cities_metaboxes_html()
{
    global $post;
    $custom = get_post_custom($post->ID);
    $latitude = isset($custom["latitude"][0])?$custom["latitude"][0]:'';
    $longitude = isset($custom["longitude"][0])?$custom["longitude"][0]:'';
?>
    <label>Latitude:</label><input name="latitude" value="<?php echo $latitude; ?>">
    <br>
    <br>
    <label>Longitude:</label><input name="longitude" value="<?php echo $longitude; ?>">
<?php
}

//Save Latitude and Longitude data
function cities_save_post()
{
    if(empty($_POST)) return; 
    global $post;
    update_post_meta($post->ID, "latitude", $_POST["latitude"]);
    update_post_meta($post->ID, "longitude", $_POST["longitude"]);

}   

add_action( 'save_post_cities', 'cities_save_post' );  


//-------------------------------------------------------------------
//ajax
// Enqueue jQuery and custom script
function enqueue_custom_scripts() {
    wp_enqueue_script('jquery'); // Enqueue jQuery
    wp_enqueue_script('custom-ajax-script', get_stylesheet_directory_uri() . '/js/custom-ajax.js', array('jquery'), null, true);

    // Pass the AJAX URL to the script
    wp_localize_script('custom-ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
//--------------



//-------------

//The Javascript


//The PHP
function ajax_request() {

    

    if ( isset($_REQUEST) ) {
        $searchTerm = $_REQUEST['term']; 

        global $wpdb;

        $posts = $wpdb->get_results("
            SELECT 
            wp_posts.post_title, 
            wp_terms.name, 
            m1.meta_value AS latitude, 
            m2.meta_value AS longitude
            FROM 
                wp_posts
            INNER JOIN 
                wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id
            INNER JOIN 
                wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
            INNER JOIN 
                wp_terms ON wp_term_taxonomy.term_id = wp_terms.term_id
            LEFT JOIN 
                wp_postmeta AS m1 ON (wp_posts.ID = m1.post_id AND m1.meta_key = 'latitude')
            LEFT JOIN 
                wp_postmeta AS m2 ON (wp_posts.ID = m2.post_id AND m2.meta_key = 'longitude')
            WHERE 
                wp_terms.name <> 'Uncategorized' AND wp_posts.post_title LIKE '".$searchTerm."%' AND wp_posts.post_status = 'publish'"); 

        foreach ($posts as $post){
            echo "<tr>";
            echo '<td>' . htmlspecialchars($post->post_title) . '</td>';
            echo '<td>' . htmlspecialchars($post->name) . '</td>';

            // Accessing the weather api based on city longitude and latitude

            $ch = curl_init();

            $url = 'api.openweathermap.org/data/2.5/forecast?lat='.$post->latitude.'&lon='.$post->longitude.'&appid=c631bba0d4b184109b01670449db0ed9&units=metric';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);

            $resp = curl_exec($ch);

            if($e = curl_error($ch)){
                ;
            }
            else{
                $decoded = json_decode($resp, true);
                $current_forecast = $decoded['list'][0];
                $current_temp = $current_forecast['main']['temp']."°C";
                echo '<td>' . esc_html($current_temp) . '</td>';
            }

            curl_close($ch);
            

            echo "</tr>";
        }
    }

   wp_die();
}

add_action( 'wp_ajax_ajax_request', 'ajax_request' );
add_action( 'wp_ajax_nopriv_ajax_request', 'ajax_request' );