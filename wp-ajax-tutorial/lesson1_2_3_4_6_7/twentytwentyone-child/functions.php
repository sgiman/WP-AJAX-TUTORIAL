<?php
/************************
 * function (sgiman)
 * version 1.0
 ************************/
//-----------------------
// Локализация скриптов
//-----------------------
// на сайте
add_action( 'wp_enqueue_scripts', function() {

    // Объявить jquery для wp-template
    wp_enqueue_script( 'jquery' );

    // Зарегистрировать ajax.js (без кеширования)
    wp_enqueue_script( 'jquery-ui-autocomplete' );

    // Локализовать admin-ajax.php
	wp_register_script( 'trueajax', get_stylesheet_directory_uri() . '/ajax.js', array( 'jquery', 'jquery-ui-autocomplete' ), time(), true );
	wp_localize_script(
		'trueajax',
		'sgiman',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ) // локализация admin-ajax.php
		)
	);
	wp_enqueue_script( 'trueajax' ); // объявить ajax-cкрипт

	// jquery UI
    wp_enqueue_style( 'jqueryuicss', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

} );

// в админке
add_action( 'admin_enqueue_scripts', function() {

	wp_enqueue_script( 'trueajax', get_stylesheet_directory_uri() . '/adminajax.js', array( 'jquery' ), time(), true );

} );


//==========================
// Узнать title post по ID
//==========================
add_action( 'wp_ajax_truepostname', 'true_ajax' );
add_action( 'wp_ajax_nopriv_truepostname', 'true_ajax' );

function true_ajax() {

	$post_id = ! empty( $_POST[ 'post_id' ] ) && $_POST[ 'post_id' ] ? intval( $_POST[ 'post_id' ] ) : 0;

	$post = get_post( $post_id );

	if( ! $post ) {
		echo 'Поста с этим ID не существует!';
	} else {
		echo get_the_title( $post );
	}

	die;
}


//----------------------------
// Валидация и безопасность
//----------------------------
add_action( 'wp_ajax_truechangeviews', function(){

	$nonce = check_ajax_referer( 'viws555', 'token', false );

	if( ! $nonce ) {
		echo 'Нарушение безопасности';
		die;
	}

	if( intval( $_POST[ 'views' ] ) < 0 ) {
		echo 'Не может быть отрицательным!!';
	} else {
		update_post_meta( $_POST[ 'post_id' ], 'post_views', intval( $_POST[ 'views' ] ) );
		echo 'ok';
	}
	die;

} );


//----------------------------------------
// добавление колонки в админку WordPress
//----------------------------------------
add_filter( 'manage_edit-post_columns', 'true_add_post_columns', 10, 1 );
function true_add_post_columns( $my_columns ){
	$my_columns['views'] = 'Просмотры';
	return $my_columns;
}

add_action( 'manage_posts_custom_column', 'true_fill_post_columns', 10, 1 );
function true_fill_post_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'views':
			$views = ( $views = get_post_meta($post->ID, 'post_views', true) ) ? $views : 0;
			echo '<input type="number" class="change-views" data-token="' . wp_create_nonce( 'viws555' ) . '" data-id="' . $post->ID . '" value="' . intval( $views ) . '" /><p></p>';
			break;
	}
}



//========================================
// AJAX - "LOAD MORE" POSTS
//========================================
add_action( 'wp_ajax_loadmore', 'true_loadmore' );
add_action( 'wp_ajax_nopriv_loadmore', 'true_loadmore' );

function true_loadmore() {

	$paged = ! empty( $_POST[ 'paged' ] ) ? $_POST[ 'paged' ] : 1;
	$paged++;

	$args = array(
		'paged' => $paged,
		'post_status' => 'publish'
	);

	$taxonomy = ! empty( $_POST[ 'taxonomy' ] ) ? $_POST[ 'taxonomy' ] : '';
	$term_id = ! empty( $_POST[ 'term_id' ] ) ? $_POST[ 'term_id' ] : 0;

	if( $taxonomy && $term_id ) {

		$args[ 'tax_query' ] = array(
			array(
				'taxonomy' => $taxonomy,
				'terms' => $term_id
			)
		);
	}

    // получить посты
    query_posts( $args );

	ob_start(); // буферизация

    // вывести контент постов
	while( have_posts() ) : the_post();
		get_template_part( 'template-parts/content/content', get_theme_mod( 'display_excerpt_or_full_post', 'excerpt' ) );
	endwhile;

    // получить контент постов
	$posts = ob_get_contents();
	ob_get_clean();

	ob_start();
	twenty_twenty_one_the_posts_navigation();
	$pagination = ob_get_contents();
	ob_get_clean();

	echo json_encode( array(

		'posts' => $posts,
		'pagination' => str_replace( admin_url( 'admin-ajax.php' ), $_POST[ 'pagenumlink' ], $pagination )

	) );
	die;

}


//========================================
// AJAX - COMMENTS
//========================================
add_action( 'wp_ajax_sendcomment', 'true_comment' );
add_action( 'wp_ajax_nopriv_sendcomment', 'true_comment' );

function true_comment() {

	$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
	if ( is_wp_error( $comment ) ) {
		$data = (int) $comment->get_error_data();
		if ( ! empty( $data ) ) {
			wp_die(
				'<p>' . $comment->get_error_message() . '</p>',
				__( 'Comment Submission Failure' ),
				array(
					'response'  => $data,
					'back_link' => true,
				)
			);
		} else {
			exit;
		}
	}

	$user            = wp_get_current_user();
	$cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );

	do_action( 'set_comment_cookies', $comment, $user, $cookies_consent );

	wp_list_comments(
		array(
			'avatar_size' => 60,
			'style'       => 'ol',
			'short_ping'  => true,
		),
		array( $comment )
	);

	die;

}


//========================================
// AJAX - SEARCH
//========================================
// поиск по сайту
add_action( 'wp_ajax_mywebsitesearch', 'true_search' );
add_action( 'wp_ajax_nopriv_mywebsitesearch', 'true_search' );

function true_search() {

	$search_term = isset( $_GET[ 'term' ] ) ? $_GET[ 'term' ] : '';

	$posts = get_posts( array(
		'posts_per_page' => 20,
		'post_type' => array( 'post', 'page' ),
		's' => $search_term
	) );

	$results = array();

	if( $posts ) {
		foreach( $posts as $post ) {
			$results[] = array(
				'id' => $post->ID,
				'value' => $post->post_title,
				'url' => get_permalink( $post->ID )
			);
		}
	}

	wp_send_json( $results );

}
