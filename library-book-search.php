<?php
	
	/**
	 * @link              http://www.mysite.com
	 * @package           Library Book Search
	 * @wordpress-plugin
	 * Plugin Name:       Libraty Book Search
	 * Plugin URI:        https://github.com/Hitendrachopda/Library-Book-Search/
	 * Description:       Simple Library Book Search by Title / Author / Publisher / Price and Rating
	 * Version:           1.0.0
	 * Author:            Hitendra Chopda.
	 * Author URI:        http://mysite.com
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       lib-book-search
	 * Domain Path:       /languages
	 */
	
	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) )	{
		die;
	}
	
	/**
	 * The code that runs during plugin activation.
	 */
	function activate_library_book_search()
	{
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search-activator.php';
		Library_Book_Search_Activator::activate();
	}
	
	/**
	 * The code that runs during plugin deactivation.
	 */
	function deactivate_library_book_search()
	{
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search-deactivator.php';
		Library_Book_Search_Deactivator::deactivate();
	}
	
	register_activation_hook( __FILE__, 'activate_library_book_search' );
	register_deactivation_hook( __FILE__, 'deactivate_library_book_search' );
	
	/**
	 * The core plugin class that is used to define internationalization,
	 * public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search.php';
	
	/**
	 * Begins execution of the plugin.
	 */
	function run_library_book_search()
	{
		$plugin = new Library_Book_Search();
		$plugin->run();
	}
	
	run_library_book_search();