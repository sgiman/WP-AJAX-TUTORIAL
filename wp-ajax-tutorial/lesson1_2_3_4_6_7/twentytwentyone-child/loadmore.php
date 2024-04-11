<?php

	global $wp_query;

	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$max_pages = $wp_query->max_num_pages;

	// echo '<pre>';
	// print_r( $wp_query );

	if( $paged < $max_pages ) :

		?><div id="loadmore" style="text-align:center;">
			<a href="#"
				data-max_pages="<?php echo $max_pages ?>"
				data-paged="<?php echo $paged ?>"
				data-taxonomy="<?php echo is_category() ? 'category' : get_query_var( 'taxonomy' ) ?>"
				data-term_id="<?php echo get_queried_object_id() ?>"
				data-pagenumlink="<?php echo get_pagenum_link( 1 ) ?>"
				class="button">Загрузить ещё</a>
		</div><?php

	endif;
?>
