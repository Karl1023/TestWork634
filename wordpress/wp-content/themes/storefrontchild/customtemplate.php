<?php 
/* Template Name: Custom Template */ 

// Include the header
get_header(); 

// This is the custom page template currently being used by the Table Page

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
    wp_terms.name <> 'Uncategorized' AND wp_posts.post_status = 'publish'"); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <section class="custom-page-content">
            <header class="page-header">
                <!-- Display the page title -->
                <h1 class="page-title"><?php the_title(); ?></h1>
            </header>

            <div class="page-content">
                <!-- Takes the name of city being searched, then filters data upon keyup -->
                <form id="search-form">
                <label for="search-term">Search City:</label><br>
                <input type="text" id="search-term" name="search-term">
                </form>


                <?php 
                // Custom action hook before the table
                do_action('custom_action_hook_before_table'); 
                ?>

                <table id="table-container">
                    <thead>
                        <tr>
                            <th> City </th>
                            <th> Country </th>
                            <th> Temperature </th>
                        </tr>
                    </thead>
                        <tbody id="table-body">

                            <?php 

                            // Create table row and table data for each item in the database
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
                            ?>
                        </tbody>
                </table>

                <?php 
                // Custom action hook after the table
                do_action('custom_action_hook_after_table'); 
                ?>

            </div>
        </section>
    </main>
</div>

<?php
// Include the footer
get_footer();
?>