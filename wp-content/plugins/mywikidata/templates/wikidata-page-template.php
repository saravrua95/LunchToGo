<?php
/**
 * Template Name: Wikidata Page
 *
 * 
 * 
 * 
 * 
 * 
 * 
 */
?>

<?php get_header() ?>

<div class="wrap">

		<fieldset>
            <legend>Buscador de alimentos</legend>
			<p></p>
			<form method="post" name="front_end" action="" >
				<p>
				<label for="numresults">Alimento:</label><br>
				<select name="movement">
				  <option value="Q530936">Fruta</option>
				</select>
				</p>
				<input type="hidden" name="new_search" value="1"/>
				<button type="submit">Buscar</button>
			</form>
		</fieldset>
		
			<?php
			if(isset($_POST['new_search']) == '1') {
				$movement = $_POST['movement'];
				
				if(isset($numresults))
					$numresults = $_POST['numresults'];
				else
					$numresults = 10;
				
				movement_wikidata_call($movement, $numresults);
			}
			?>
            
</div><!-- .wrap -->


<?php get_footer() ?>