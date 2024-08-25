<h1>TestWork634 Repository</h1> <p>This project extends the Storefront WordPress theme by creating a custom post type called "Cities" with associated metadata and taxonomies. Additionally, a widget displays the city name and current temperature using an external API, and a custom page template displays a searchable table of countries, cities, and temperatures. All modifications are made within the child theme.</p>

<h2>Child Theme Modifications</h2>
<ul>
  <li>Theme Location: wp-content/themes/storefrontchild</li>
  <li>All customizations are housed within the child theme to ensure update compatibility with the parent Storefront theme.</li>
</ul>

<h2>Create Custom Post Type: "Cities"</h2>
<ul>
  <li>Location: functions.php</li>
  <li>Added custom fields for latitude and longitude within a meta box on the post editing screen.</li>
  <li>Registered a custom taxonomy titled "Countries" and attached it to the "Cities" post type.</li>
</ul>

<h2>Meta Box for Latitude and Longitude</h2>
<ul>
  <li>Location: functions.php</li>
  <li>Created a meta box with fields "latitude" and "longitude" for entering city coordinates.</li>
</ul>

<h2>Create Custom Taxonomy: "Countries"</h2>
<ul>
  <li>Location: functions.php</li>
  <li>Created a custom taxonomy named "Countries."</li>
  <li>Attached the taxonomy to the "Cities" post type.</li>
</ul>

<h2>Weather Widget</h2>
<ul>
  <li>Location: widgets/weather-widget.php</li>
  <li>Developed a custom widget that allows user to enter city name which the details are take from the cities custom post type. This displays the city name and current temperature.</li>
  <li>Integrated an external API (OpenWeatherMap) to retrieve real-time temperature data.</li>
</ul>

<h2>Custom Page Template with Table</h2>
<ul>
  <li>Location: customtemplate.php</li>
  <li>Created a custom page template to display a table listing countries, cities, and temperatures. This template is used by the "Table Page" page</li>
  <li>Used $wpdb for querying the database and retrieving data.</li>
  <li>Added a search field for cities using WP Ajax for dynamic filtering. This allows users to simply type the name of the city and the table filters data</li>
  <li>Implemented custom action hooks before and after the table for flexibility.</li>
</ul>

<h1>Usage</h1>
<h2>Custom post type adding</h2>
<ol>
  <li>In the wordpress dashboard, click Cities then add new post</li>
  <li>Enter the name of the city and the coordinates (latitude and longitude) of the city</li>
  <li>Add new category for the countries taxonomy then enter country name</li>
  <li>Choose country</li>
  <li>Click Publish or Update</li>
</ol>

<h2>Widget Usage</h2>
<ol>
  <li>In the wordpress dashboard, click Appearance then Widgets</li>
  <li>Add widget "Weather Widget" to a widget area</li>
  <li>Enter the name of the city that you wish to see from the Cities post type</li>
  <li>Click Publish or Update</li>
</ol>

<h2>Table Page Usage</h2>
<ol>
  <li>Click Table Page from navigation bar. This page will show all the details from the database. Including the temperature</li>
  <li>For searching a specific city, type the name of the city in the search bar</li>
  <li>To make all cities visible again, clear the search bar </li>
</ol>
