<?php
	
	/**
	 * Define the internationalization functionality
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @link       http://mysite.com
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/includes
	 */
	
	/**
	 * Define the internationalization functionality.
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @package    Library_Book_Search
	 * @subpackage Library_Book_Search/includes
	 * @author     Hitendra Chopda. <hitendrachopda25@gmail.com>
	 */
	class Library_Book_Search_i18n
	{		
		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain()
		{		
			load_plugin_textdomain( 'lib-booking-search', FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
		}
	}