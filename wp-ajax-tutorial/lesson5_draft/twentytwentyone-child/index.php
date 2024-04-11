<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header(); ?>

<!--************************* FILTER *************************-->
<form style="text-align:center" action="" method="POST" id="filter">
	<div>
		<label for="post_type">Выберите тип:</label><br>
		<select name="post_type" id="post_type">
			<option value="">Выберите...</option>
			<option value="post">Записи</option>
			<option value="page">Страницы</option>
		</select>
	</div>

	<div>
		<label for="sort">Сортировать по:</label><br>
		<select name="sort" id="sort">
			<option value="date-desk">Дата: по убыванию</option>
			<option value="date-asc">Дата: по возрастанию</option>
			<option value="title-desk">По алфавиту: А-Я</option>
			<option value="title-ask">По алфавиту: Я-А</option>
		</select>
	</div>

	<div>
		<?php
		$categories = get_terms ( array( 'taxonomy' => 'category',  'hide_empty' => false, ) );
		//echo "<pre>"; print_r($categories);
		if($categories) : ?>
			<label for="category">Рубрика:</label><br>
			<select name="category" id="category">
				<option value="">Выберете...</option>
				<?php foreach ($categories as $category) : ?>
				<option value="<?php echo $category->term_id ?>"><?php echo $category->name ?></option>
				<?php endforeach; ?>
			</select>
		<?php
		endif;
		?>
	</div>

	<input type="hidden" name="action" value="filterer" />
	<button class="button">Фильтровать</button>

</form>
<!--****************************************************************-->


<?php if ( is_home() && ! is_front_page() && ! empty( single_post_title( '', false ) ) ) : ?>
	<header class="page-header alignwide">
		<h1 class="page-title"><?php single_post_title(); ?></h1>
	</header><!-- .page-header -->
<?php endif; ?>

<div id="respnse">
<?php
if ( have_posts() ) {

	// Load posts loop.
	while ( have_posts() ) {
		the_post();

		get_template_part( 'template-parts/content/content', get_theme_mod( 'display_excerpt_or_full_post', 'excerpt' ) );
	}

	// Previous/next page navigation.
	twenty_twenty_one_the_posts_navigation();

} else {

	// If no content, include the "No posts found" template.
	get_template_part( 'template-parts/content/content-none' );

}
?></div><?php

get_footer();
