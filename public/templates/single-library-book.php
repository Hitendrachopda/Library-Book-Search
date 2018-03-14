<?php
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * @link       http://mysite.com
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/public
	 */
	
	/**
	 * The public-facing functionality of the plugin.
	 * Display the Book details
	 *
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/public
	 * @author     Hitendra Chopda. <hitendrachopda25@gmail.com>
	 */

get_header(); ?>

<div class="wrap lib-book-detail">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php

			/* Start the Loop */
			while ( have_posts() ) : the_post();
				$author_name		= $publisher_name = '';
				$author_taxonomy	= 'lib_author';
				$publish_taxonomy	= 'lib_publisher';
				$book_id			= get_the_ID();
				$book_authors		= wp_get_post_terms($book_id, $author_taxonomy,  array("fields" => "names"));
				$book_publishers	= wp_get_post_terms($book_id, $publish_taxonomy,  array("fields" => "names"));
				
				if($book_authors) {
					foreach( $book_authors as $book_author) {
						$author_name.= '<a href="'.get_term_link($book_author,$author_taxonomy).'" class="lib-author-link">'.$book_author.'</a>,';
					}
					$author_name = rtrim($author_name,',');
				}
				if($book_publishers) {
					foreach( $book_publishers as $book_publisher) {
						$publisher_name.= '<a href="'.get_term_link($book_publisher,$publish_taxonomy).'" class="lib-publisher-link">'.$book_publisher.'</a>,';
					}
					$publisher_name = rtrim($publisher_name,',');
				}
				echo '<p> <strong>Book Name </strong> : '.get_the_title().'</p>';
				echo '<p> <strong>Book Author </strong>: '.$author_name.'</p>';
				echo '<p> <strong>Book Publisher </strong>: '.$publisher_name.'</p>';
				echo '<p> <strong>Book Price </strong>: $'.get_post_meta($book_id,'lib_book_price', true).'</p>';
				$tot_rating  = get_post_meta( $book_id, 'lib_book_rating', 1);	
				echo '<p> <strong>Book Rating</strong> :';
				for( $star = 1; $star <= 5; $star ++ ) {
					if( $star <= $tot_rating)
						echo '<span class="fill-star">&#9733;</span>';
					else
						echo '<span class="blank-star">&#9734;</span>';
				}
				echo '</p>';
				echo '<p> <strong>Book Description </strong> : '.get_the_content().'</p>';

			endwhile; // End of the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
<?php get_footer();