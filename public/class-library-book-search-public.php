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
	 * Defines the plugin name, version, and hooks to
	 *
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/public
	 * @author     Hitendra Chopda. <hitendrachopda25@gmail.com>
	 */
	class Library_Book_Search_Public
	{		
		/**
		 * The ID of this plugin.
		 *
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;
		
		/**
		 * The version of this plugin.
		 *
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param      string $plugin_name The name of the plugin.
		 * @param      string $version     The version of this plugin.
		 */
		public function __construct( $plugin_name, $version )
		{		
			$this->plugin_name = $plugin_name;
			$this->version     = $version;
		}
		
		/**
		 * Register the stylesheets for the public-facing side of the site.
		 */
		public function enqueue_styles()
		{
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/library-book-search-public.css', array (), $this->version, 'all' );
			wp_enqueue_style( 'jquery-ui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array (), $this->version, 'all' );
		}
		
		/**
		 * Register the JavaScript for the public-facing side of the site.
		 */
		public function enqueue_scripts()
		{
			wp_enqueue_script( 'jquery-cookie', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.js', array ( 'jquery' ), $this->version, FALSE );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lib-book-search-public.js', array ( 'jquery' ), $this->version, FALSE );
			$book_max_price						= $this->lib_book_max_price();
			$localized_data['ajax_url']			= admin_url('admin-ajax.php');
			$localized_data['book_max_price']	= $book_max_price;
			
			// Localize the script with new data
			wp_localize_script( $this->plugin_name, 'lbsVar', $localized_data);
		}
		
		// function to get maximum book price value
		public function lib_book_max_price()
		{
			global $wpdb;
			$price_query	= "SELECT max(cast(meta_value as unsigned))  FROM ".$wpdb->prefix."postmeta WHERE meta_key='lib_book_price'";
			$book_max_price	= $wpdb->get_var($price_query);
			
			if( !empty( $book_max_price ))
			{
				if( $book_max_price < 3000)
					$book_max_price = 3000;
			}
			else
			{
				$book_max_price = 3000;
			}
			return $book_max_price;
		}
		public function lib_add_shortcode()
		{
			//Library search tool shortcode addded here
			add_shortcode('library-book-search',array($this,'lib_book_search_view'));
			
			// executing ajax for book searching
			add_action('wp_ajax_lib_search_book', array( $this, 'fn_lib_search_book'));
			add_action('wp_ajax_nopriv_lib_search_book', array( $this, 'fn_lib_search_book'));
			
			/* Filter the single_template and call single template file for book detail page */
			add_filter('single_template', array( $this,'lib_book_single_template'));
		
		}
		// this member function display search form and all books
		public function lib_book_search_view()
		{
			//Display Library Search Form
			ob_start();
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/library-search-form.php';
			return ob_get_clean();
		}
		
		// member function to display book detail on single template
		public function lib_book_single_template($single)
		{
			global $post;
			
			/* Call custom template for the book details page */
			if ( $post->post_type == 'lib_books' )
			{
				if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/single-library-book.php' ) )
				{
					return plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/single-library-book.php';
				}
			}
			return $single;
		}

		public function fn_lib_search_book()
		{
			$book_name			= '';
			$meta_rating_query	= array();
			$publisher_tax		= array();
			$author_tax 		= array();
			$meta_price_query	= array( 'relation' => 'AND' );
			$tax_query			= array( 'relation' => 'AND' );
			
			$paged				= $_POST['page_number'];
			$_data				= explode("&",  $_POST['search_data']);
			$post_per_page		= get_option( 'posts_per_page' );
			
			// calculating dynamic index number
			$index_paged	= $paged - 1;
			$index_number	= ( $post_per_page * $index_paged ) + 1;
			
			 // through this logic, we will store all the search key in array, which will be helpful in retrieving value
			$lib_key_array = array();
			foreach ($_data as $key => $value)
			{
				$_sub_data = explode("=",  $value);
				if(isset($_sub_data[0]) )
				{
					$lib_key_array[$_sub_data[0]] = $_sub_data[1];
				} 
			}
			
			// checking book name variable is set and it is not empty
			if(isset($lib_key_array['book_name']) && (trim( $lib_key_array['book_name'] ) != false))
			{
				$book_name = str_replace("+"," ",$lib_key_array['book_name']);
			}
			// retrieve value only when Book Publisher value exist and is not empty
			if(isset($lib_key_array['book_publisher']) && (trim( $lib_key_array['book_publisher'] )  != false))
			{
				$publisher_id	= $lib_key_array['book_publisher'];
				$tax_query[]	= array(
										'taxonomy'	=> 'lib_publisher',
										'field'		=> 'term_id',
										'terms'		=> $publisher_id,
									);
			}
			// retrieving value of book minimum price and it should be numeric
			if(isset($lib_key_array['book_min_price']) && (trim( $lib_key_array['book_min_price'] )  != false) && is_numeric( $lib_key_array['book_min_price'] ))
			{
				$book_min_price	= $lib_key_array['book_min_price'];
				$meta_price_query[]	= array(
										'key'		=> 'lib_book_price',
										'value'		=> $book_min_price ,
										'compare'	=> '>=',
										'type'		=> 'NUMERIC',
									);
			}
			// retrieving value of book maximum price and it should be numeric
			if(isset($lib_key_array['book_max_price']) && (trim( $lib_key_array['book_max_price'] )  != false) && is_numeric( $lib_key_array['book_max_price'] ))
			{
				$book_max_price	= $lib_key_array['book_max_price'];
				$meta_price_query[]	= array(
										'key'		=> 'lib_book_price',
										'value'		=> $book_max_price ,
										'compare'	=> '<=',
										'type'		=> 'NUMERIC',
									);
			}
			// retrieving book author name
			if(isset($lib_key_array['book_author']) && (trim( $lib_key_array['book_author'] )  != false))
			{
				$author_name	= str_replace("+"," ",$lib_key_array['book_author']);
				$tax_query[]	= array(
										'taxonomy'	=> 'lib_author',
										'field'		=> 'name',
										'terms'		=> $author_name,
										'operator'	=> 'IN'
									);
			}
			// retrieving book rating value from dropdown
			if(isset($lib_key_array['book_rating']) && (trim( $lib_key_array['book_rating'] )  != false) && is_numeric($lib_key_array['book_rating']))
			{
				$book_rating			= $lib_key_array['book_rating'];
				$meta_rating_query[]	= array(
												'key'     => 'lib_book_rating',
												'value'   => $book_rating,
												'compare' => '=',
											);
			}
			
			// combining array of rating as well as price into single array
			$final_meta_query['relation']	= 'AND';
			$final_meta_query[]				= $meta_rating_query;
			$final_meta_query[]				= $meta_price_query;
			
			// Arguments array of book post type
			$book_args	= array(
								'post_type'			=> 'lib_books',
								'posts_per_page'	=> $post_per_page,
								'paged'				=> $paged,
								's'					=> $book_name,
								'tax_query'			=> $tax_query,
								'meta_query'		=> $final_meta_query,

							);

			$book_loop	= new WP_Query( $book_args );

			// if it contains record, then go ahead
			if($book_loop->found_posts > 0 ) {				
				$result_html = $pagination = '';
				while( $book_loop->have_posts()) {
					$book_loop->the_post();
					$book_id		= get_the_ID();
					$book_author	= wp_get_post_terms($book_id, 'lib_author',  array("fields" => "names"));
					$book_publisher	= wp_get_post_terms($book_id, 'lib_publisher',  array("fields" => "names"));
					$result_html .= '<tr class="book-data">';
							$result_html .= '<td>'.$index_number.'</td>';
							$result_html .= '<td><a href="'.get_permalink($book_id).'">'.get_the_title().'</a></td>';
							$result_html .= '<td>'.get_post_meta( $book_id, 'lib_book_price', 1).'</td>';
							$result_html .= '<td>'.implode(',', $book_author).'</td>';
							$result_html .= '<td>'.implode(',', $book_publisher).'</td>';
							$tot_rating  = get_post_meta( $book_id, 'lib_book_rating', 1);	
							$result_html .= '<td><p>';					
							for( $star = 1; $star <= 5; $star ++ ) {
								if( $star <= $tot_rating)
									$result_html .= '<span class="fill-star">&#9733;</span>';
								else
									$result_html .= '<span class="blank-star">&#9734;</span>';
							}
							$result_html .= '</p></td>';
					$result_html .= '</tr>';
					$index_number++;
				}
				wp_reset_postdata(); //restores original post data.
				
				$big = 999999999; // need an unlikely integer
				$pagination = paginate_links( array(
									'base'		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
									'format'	=> '?paged=%#%',
									'current'	=> max( 1, $paged ),
									'type'		=> 'list',
									'total'		=> $book_loop->max_num_pages
								) );
				$response['status'] = true;
				$response['search_result'] = $result_html;
				$response['pagination_result'] = $pagination;
			} else {
				$response['status']		= false;
				$response['no_records']	= '<tr class="book-data"><td colspan="6" align="center"> No Books Found</td></tr>';
			}
			echo json_encode($response);
			exit;
		}
	}