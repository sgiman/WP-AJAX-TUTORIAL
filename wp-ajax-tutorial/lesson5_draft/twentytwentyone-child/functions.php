<?php

add_action( 'wp_enqueue_scripts', function() {

	// активировать jquery.js
    wp_enqueue_script( 'jquery' );

    // зарегистрировать filter.js
    wp_register_script( 'filter', get_stylesheet_directory_uri() . '/filter.js', array( 'jquery' ), time(), true );

    // локализовать
    wp_localize_script(
        "filter",
        "true_obj",
        array('ajaxurl' => admin_url('admin-ajax.php'))
    );
    // активировать filter script
    wp_enqueue_script( 'filter' );

} );

add_action('wp_ajax_filterer', 'truesgi_ajax_filter');
add_action('wp_ajax_nopriv_filterer', 'truesgi_ajax_filter');


function truesgi_ajax_filter() {

    print_r ( $_POST );

    die();

}



/**
 * CREATE TABLES (MySQL)
 */
add_action('sgitab', 'sgi_tables');
function sgi_tables()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Создать таблицу 1 ("wp_sgiman_tbl_books")
    $full_name_table1 = $wpdb->prefix . "sgiman_tbl_books";
    $table_query1 = "CREATE TABLE " . $full_name_table1 . " (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(150) DEFAULT NULL,
                          `amount` int(11) DEFAULT NULL,
                          `description` text DEFAULT NULL,
                          `book_image` varchar(200) DEFAULT NULL,
                          `language` varchar(150) DEFAULT NULL,
                          `shelf_id` INT NULL,
                          `status` int(11) DEFAULT 1,
                          `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                          PRIMARY KEY (`id`)
                         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    dbDelta($table_query1);


// Создать таблицу 2 ("wp_sgiman_tbl_book_shelf")
    $full_name_table2 = $wpdb->prefix . "sgiman_tbl_book_shelf";
    $table_query2 = "CREATE TABLE " . $full_name_table2 . " (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `shelf_name` varchar(150) NOT NULL,
                          `capacity` int(11) NOT NULL,
                          `shelf_location` varchar(200) NOT NULL,
                          `status` int(11) NOT NULL DEFAULT 1,
                          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    dbDelta($table_query2);

// Заполнить таблицу 2 ("wp_sgiman_tbl_book_shelf")
    $insert_query = "INSERT into " . $full_name_table2 . " (shelf_name, capacity, shelf_location, status) VALUES
                    ('Shelf 1 ', 230, 'Left Cornot', 1),
                    ('Shelf 2 ', 300, 'Right Cornot', 1),
                    ('Shelf 3 ', 100, 'Center Top', 1)";

    $wpdb->query($insert_query);

// create page on plugin activation
// wp_posts
    $get_data = $wpdb->get_row(
//$wpdb->prepare("SELECT * from " . $wpdb->prefix . "posts WHERE post_name = %s", 'book_tool')
        $wpdb->prepare("SELECT * FROM wp_posts WHERE post_name = %s", 'book_tool')
    );

    if (!empty($get_data)) {
        // already we have data with this post name
    } else {
        // create page
        $post_arr_data = array(
            "post_title" => "Book Tool",
            "post_name" => "book_tool",
            "post_status" => "publish",
            "post_author" => 1,
            "post_content" => "Simple page content of Book Tool",
            "post_type" => "page"
        );
        wp_insert_post($post_arr_data);
    }

}

//sgi_tables();
