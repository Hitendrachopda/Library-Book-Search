<?php
	
	/**
	 * The file that defines the core plugin class
	 * A class definition that includes attributes and functions used across both the
	 * public-facing side of the site and the admin area.
	 *
	 * @link       http://mysite.com
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/includes
	 */
	
	/**
	 * The core plugin class.
	 * This is used to define internationalization, admin-specific hooks, and
	 * public-facing site hooks.
	 * Also maintains the unique identifier of this plugin as well as the current
	 * version of the plugin.
	 *
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/includes
	 * @author     Hitendra Chopda. <hitendrachopda25@gmail.com>
	 */
	class Library_Book_Search
	{
		
		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @access   protected
		 * @var      Library_Book_Search_Loader $loader Maintains and registers all hooks for the plugin.
		 */
		protected $loader;
		
		/**
		 * The unique identifier of this plugin.
		 *
		 * @access   protected
		 * @var      string $plugin_name The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;
		
		/**
		 * The current version of the plugin.
		 *
		 * @access   protected
		 * @var      string $version The current version of the plugin.
		 */
		protected $version;
				/**
		 * The unique identifier of this plugin.
		 *
		 * @access   protected
		 * @var      string $post_type_name The string used to uniquely identify this plugin.
		 */
		protected $post_type_name;
		
		/**
		 * The current version of the plugin.
		 *
		 * @access   protected
		 * @var      array $taxonomy_list The current version of the plugin.
		 */
		protected $taxonomy_list;
		
		/**
		 * Define the core functionality of the plugin.
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 */
		public function __construct()
		{	
			$this->plugin_name 		= 'library-book-search';
			$this->version     		= '1.0.0';
			$this->post_type_name  	= 'books';
			$this->taxonomy_list  	= array('author','publisher');
			
			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
			$this->define_public_hooks();	
		}
		/**
		 * Load the required dependencies for this plugin.
		 * Include the following files that make up the plugin:
		 * - Library_Book_Search_Loader. Orchestrates the hooks of the plugin.
		 * - Library_Book_Search_i18n. Defines internationalization functionality.
		 * - Library_Book_Search_Admin. Defines all hooks for the admin area.
		 * - Library_Book_Search_Public. Defines all hooks for the public side of the site.
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @access   private
		 */
		private function load_dependencies()
		{	
			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-library-book-search-loader.php';
			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-library-book-search-i18n.php';
			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-library-book-search-admin.php';
			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-library-book-search-public.php';
			$this->loader = new Library_Book_Search_Loader();
		}
		
		/**
		 * Define the locale for this plugin for internationalization.
		 * Uses the Library_Book_Search_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @access   private
		 */
		private function set_locale()
		{	
			$plugin_i18n = new Library_Book_Search_i18n();
			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
		}
		
		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @access   private
		 */
		private function define_admin_hooks()
		{	
			$plugin_admin = new Library_Book_Search_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_post_type_name(), $this->get_taxonomy_list() );
			$this->loader->add_action( 'init', $plugin_admin, 'setup_library_menu' );
			$this->loader->add_action( 'init', $plugin_admin, 'lib_register_post_type' );
			$this->loader->add_action( 'init', $plugin_admin, 'lib_add_taxonomy' );	
		}
		
		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @access   private
		 */
		private function define_public_hooks()
		{	
			$plugin_public = new Library_Book_Search_Public( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_action( 'init', $plugin_public, 'lib_add_shortcode' );
		}
		
		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 */
		public function run()
		{
			$this->loader->run();
		}
		
		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name()
		{
			return $this->plugin_name;
		}
		
		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @return    Library_Book_Search_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader()
		{	
			return $this->loader;
		}
		
		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @return    string    The version number of the plugin.
		 */
		public function get_version()
		{			
			return $this->version;
		}
		
		/**
		 * Retrieve the Post Type Name of the plugin.
		 *
		 * @return    string $post_type_name of the plugin.
		 */
		public function get_post_type_name()
		{	
			return $this->post_type_name;
		}
		
		/**
		 * Retrieve the The Taxonomy List of the plugin.
		 *
		 * @return    array $taxonomy_list of the plugin.
		 */
		public function get_taxonomy_list()
		{	
			return $this->taxonomy_list;
		}
	}