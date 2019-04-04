<?php
 
/*
Plugin Name: University of Alicante Wikidata Plugin
Plugin URI: http://www.ua.es/wikidata
Description: Create a search page for wikidata.
Version: 1.0
Author: Departamento de Lenguajes y Sistemas Inform�ticos
Author URI: http://www.dlsi.ua.es/
*/
 
/**
 * Funci�n que instancia el Widget
 */
//function mywikidata_create_widget(){    
    //include_once(plugin_dir_path( __FILE__ ).'/includes/widget.php');
    //register_widget('mywikidata_widget');
//}
//add_action('widgets_init', 'mywikidata_create_widget');

require_once 'easyrdf/vendor/autoload.php';

if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}

register_activation_hook(__FILE__,'mywikidata_install');

function mywikidata_install() {
   global $wp_version;
   If (version_compare($wp_version, "2.9", "<")) 
    { 
      deactivate_plugins(basename(__FILE__)); // Deactivate plugin
      wp_die("This plugin requires WordPress version 2.9 or higher.");
    }
    
    // create page
    check_pages_live();
}

add_filter( 'template_include', 'wikidata_page_template');

function wikidata_page_template( $template ) {

    if ( is_page( 'wikidata-search' )  ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/wikidata-page-template.php';
		return $new_template;
    }

    return $template;
}


function check_pages_live(){
    if(get_page_by_title( 'wikidata-search') == NULL) {
        create_pages_fly('wikidata-search');
    }
}

function create_pages_fly($pageName) {
	$createPage = array(
	  'post_title'    => $pageName,
	  'post_content'  => 'Wikidata Search Example',
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $pageName
	);

	// Insert the post into the database
	wp_insert_post( $createPage );
}

function example_dbpedia_call(){
	
	EasyRdf_Namespace::set('category', 'http://dbpedia.org/resource/Category:');
    EasyRdf_Namespace::set('dbpedia', 'http://dbpedia.org/resource/');
    EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
    EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');
	
	$sparql = new EasyRdf_Sparql_Client('http://dbpedia.org/sparql');
	
	echo "<h2>List of countries</h2>";
    echo "<ul>";

		$result = $sparql->query(
			'SELECT * WHERE {'.
			'  ?country rdf:type dbo:Country .'.
			'  ?country rdfs:label ?label .'.
			'  ?country dc:subject category:Member_states_of_the_United_Nations .'.
			'  FILTER ( lang(?label) = "en" )'.
			'} ORDER BY ?label'
		);
		foreach ($result as $row) {
			echo "<li>".$row->label."</li>\n";
		}

	echo "</ul>";
	echo "<p>Total number of countries:". $result->numRows() ."</p>";
	
	/*$foaf = new EasyRdf_Graph("http://njh.me/foaf.rdf");
	$foaf->load();
	$me = $foaf->primaryTopic();
	echo "My name is: ".$me->get('foaf:name')."\n";*/
}

function movement_wikidata_call($movement, $numresults){
	
	$sparql = new EasyRdf_Sparql_Client('http://query.wikidata.org/sparql');
	
	echo "<h2>Listado de ...</h2>";
    echo "<table cellspacing='0' cellpadding='0'>";


/* */





		$result = $sparql->query(
			'SELECT * WHERE {'.
			'  ?fruit wdt:P279 wd:Q3314483.'.
			'  OPTIONAL {?fruit wdt:P18 ?imagen } .'.
			'  ?fruit rdfs:label ?label .'.
			'  FILTER ( lang(?label) = "en" )'.
			'} ORDER BY ?label '.
			'LIMIT '.$numresults
		);
		foreach ($result as $row) {
			echo "<tr>";
			if(isset($row->imagen))
			    echo "<td><img heigth='100px' width='100px' src='".$row->imagen."'></td>";
			else 
				echo "<td><img heigth='100px' width='100px' src='".plugin_dir_url( __FILE__ ) . "img/no-imagen.jpg'></td>";
			echo "<td><b class='fn'><a class='url' target='_blank' href='".$row->writer."'>" .$row->label. "</a></b><br>";
			
		}
	echo "</table>";	
}
?>



