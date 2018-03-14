<?php
	
	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @link       http://mysite.com
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/admin
	 */
	
	/**
	 * The admin-specific functionality of the plugin.
	 * Defines the plugin name, version, and hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/admin
	 * @author     Hitendra Chopda. <hitendrachopda25@gmail.com>
	 */
	class Library_Book_Search_Admin
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
		 * The version of this plugin.
		 *
		 * @access   private
		 * @var      string $post_type_name new post type name of this plugin.
		 */
		private $post_type_name;
		
		/**
		 * The version of this plugin.
		 *
		 * @access   private
		 * @var      array $taxonomy_name new post type name of this plugin.
		 */
		private $taxonomy_name;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param      string $plugin_name The name of this plugin.
		 * @param      string $version     The version of this plugin.
		 */
		public function __construct( $plugin_name, $version, $post_type_name,  $taxonomy_list = array())
		{			
			$this->plugin_name 		= $plugin_name;
			$this->version     		= $version;
			// Set post type name, author taxonomy name and publisher taxonomy name
			$this->post_type_name	= $post_type_name;
			$this->taxonomy_name	= $taxonomy_list;
		}
		
		/**
		 * Create Custom post type Book and Category for the admin area.
		 */
		
		public function setup_library_menu()
		{
			global $pagenow;
			//Add settings page menu in our 'Books' post type
			add_action( 'admin_menu', array( $this, 'lib_add_setting_page' ));

			// show metabox only when we edit/add book
			if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
				add_action( 'add_meta_boxes', array( $this, 'lib_add_meta_box' ));
				add_action( 'save_post', array( $this, 'lib_save_books_meta'));
			}				
		}

		/* Method which registers the post type */
		public function lib_register_post_type()
		{ 
			// Add action to register the post type, if the post type does not already exist
			if( ! post_type_exists( $this->post_type_name ) ) { 
				$post_type			= 'lib_'.$this->post_type_name;
				$name				= ucfirst( $this->post_type_name);
				$plural				= $name . 's';
				
				$lib_post_labels	= array(
											'name'					=> _x( $name, 'post type general name' ),
											'singular_name'			=> _x( $name, 'post type singular name' ),
											'add_new'				=> _x( 'Add New', strtolower( $name ) ),
											'add_new_item'			=> __( 'Add New ' . $name ),
											'edit_item'				=> __( 'Edit ' . $name ),
											'new_item'				=> __( 'New ' . $name ),
											'all_items'				=> __( 'All ' . $name ),
											'view_item'				=> __( 'View ' . $name ),
											'search_items'			=> __( 'Search ' . $plural ),
											'not_found'				=> __( 'No ' . ucfirst( $name ) . ' found'),
											'not_found_in_trash'	=> __( 'No ' . ucfirst( $name ) . ' found in Trash'), 
											'parent_item_colon'		=> '',
											'menu_name'				=> $name
										);
				$lib_post_args		= array(
									'label'					=> $plural,
									'labels'				=> $lib_post_labels,
									'public'				=> true,
									'show_ui'				=> true,
									'supports'				=> array( 'title', 'editor' ),
									'show_in_nav_menus'		=> true,
									'_builtin'				=> false,
								);
								
				 // Register the post type
				register_post_type( $post_type, $lib_post_args );
			
			}
		}
		/* Adding custom taxonomy to Book post type */
		public function lib_add_taxonomy()
		{
			$taxonomy_list = $this->taxonomy_name;
			if( !empty($taxonomy_list)) {
				$post_type			= 'lib_'.$this->post_type_name;
				foreach( $taxonomy_list as $lib_taxonomy_name )
				{
					// Add new taxonomy,
					$taxonomy_slug		= 'lib_'.$lib_taxonomy_name;
					$taxonomy_name		= ucfirst( $lib_taxonomy_name);
					$taxonomy_plural	= $taxonomy_name . 's';
					
					$taxonomy_labels	= array(
											'name'				=> _x( $taxonomy_plural, 'taxonomy general name' ),
											'singular_name'		=> _x( $taxonomy_name, 'taxonomy singular name' ),
											'search_items'		=>  __( 'Search '.$taxonomy_plural ),
											'all_items'			=> __( 'All '.$taxonomy_plural ),
											'parent_item'		=> __( 'Parent '.$taxonomy_name ),
											'parent_item_colon'	=> __( 'Parent '.$taxonomy_name ),
											'edit_item'			=> __( 'Edit '.$taxonomy_name ), 
											'update_item'		=> __( 'Update '.$taxonomy_name ),
											'add_new_item'		=> __( 'Add New '.$taxonomy_name ),
											'new_item_name'		=> __( 'New Topic '.$taxonomy_name ),
											'menu_name'			=> __( $taxonomy_plural ),
											);    
					 
					// checking whether taxonomy with particular slug exist or not
					if( ! taxonomy_exists( $taxonomy_slug ) )
					{
						/* Create taxonomy and attach it to the object type (post type) */
						register_taxonomy(
							$taxonomy_slug,
							$post_type,
							array(
								'hierarchical'		=> true,
								'labels'			=> $taxonomy_labels,
								'show_ui'			=> true,
								'show_admin_column'	=> true,
								'query_var'			=> true,
								'rewrite'			=> array( 'slug' => $taxonomy_slug ),
							)
						);
					}
				}
			}
		}
		
		/* Add meta boxes for the Books */
		public function lib_add_meta_box()
		{

			$lib_post_type 	= 'lib_'.$this->post_type_name;
			add_meta_box( 
				'lib_book_price', 
				__( 'Book Price', 'lib-book-search' ), 
				array($this,'lib_meta_book_price'), 
				$lib_post_type, 
				'side' 
			);
			add_meta_box( 
				'lib_book_rating', 
				__( 'Book Rating', 'lib-book-search' ), 
				array($this,'lib_meta_book_rating'), 
				$lib_post_type, 
				'side' 
			);
		}
		
		// Callback function, to display html version of Book Price metabox
		public function lib_meta_book_price( $post )
		{
			$lib_book_price = get_post_meta($post->ID,  'lib_book_price', TRUE);

			if (!$lib_book_price)
				$lib_book_price = '';

			// Nonce field to validate form request came from current site
			wp_nonce_field( basename( __FILE__ ), 'lib_book_price_field' );

			echo '<input type="number" name="lib_book_price" min="1"  value="'.$lib_book_price.'">';
		}

		//Callback function, to display html version of rating metabox
		public function lib_meta_book_rating( $post )
		{
			$lib_book_rating = get_post_meta($post->ID,  'lib_book_rating', TRUE);
			
			if (!$lib_book_rating) $lib_book_rating = '';

			// Nonce field to validate form request came from current site
			wp_nonce_field( basename( __FILE__ ), 'lib_book_rating_field' );

			echo '<input type="number" name="lib_book_rating" min="1" max="5" value="'.$lib_book_rating.'">';
		}
	    
		/**
		 * Save the metabox data
		*/
		public function lib_save_books_meta()
		{
			global $post;
			if( !empty($post) ) {
				$post_id	= $post->ID;
				 // Return if the user doesn't have edit permissions.
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}

				// Verify with proper authorization,
				// because save_post can be triggered outside also.
				if ( ! isset( $_POST['lib_book_price'] ) || ! wp_verify_nonce( $_POST['lib_book_price_field'], basename(__FILE__) ) || ! isset( $_POST['lib_book_rating'] ) || ! wp_verify_nonce( $_POST['lib_book_rating_field'], basename(__FILE__) ) ) {
					return $post_id;
				}

				// Now that we're authenticated, save the book meta data
				$lib_metas	= array('lib_book_price','lib_book_rating');
				foreach ($lib_metas as $lib_meta_key) {
					$lib_meta_value = $_POST[$lib_meta_key];
					if( !empty($lib_meta_value)) {
						if ( get_post_meta( $post_id, $lib_meta_key, false ) ) {
							// If the custom field already has a value, update it.
							update_post_meta( $post_id, $lib_meta_key, $lib_meta_value );
						} else {
							// If the custom field doesn't have a value, add it.
							add_post_meta( $post_id, $lib_meta_key, $lib_meta_value);
						}
					}
				}
			}
		}
		public function lib_add_setting_page()
		{
			$post_type			= 'lib_'.$this->post_type_name;
			add_submenu_page('edit.php?post_type='.$post_type, 'Shortcode', 'Shortcode', 'edit_posts', basename(__FILE__), array($this,'lib_show_shortcode'));

		}
		public function lib_show_shortcode()
		{
			echo '<div class="wrap">';
				echo '<p>To show Book Search Form and Listing, use this shortcode <strong> [library-book-search] </strong> in editor of any page/post. </p>';
			echo '</div>';
		}
	}