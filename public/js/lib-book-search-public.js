/** Main js Library Search plugin **/
jQuery(document).ready(function(){
	
	// make sure that our jquery executes only when particular class of our plugin exist
	if( jQuery('.lib-search-form #lib_btn_submit').length > 0 )
	{
		// ajax function to load all the books during page load
		// here argument defines that we will show records only for first page
		search_books(1);
		
		// when user clicks on "search" button, call ajax function again
		jQuery('.lib-search-form #lib_btn_submit').click(function(e){
			search_books(1);
			e.preventDefault();
		});
		
		// jQuery price range slider
		jQuery( "#slider-range" ).slider({
			  range: true,
			  min: 1,
			  max: libVar.book_max_price,
			  values: [ 1, libVar.book_max_price ],
			  slide: function( event, ui ) {
				jQuery( "#amount" ).html( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
				jQuery( "#book_min_price" ).val(ui.values[ 0 ]);
				jQuery( "#book_max_price" ).val(ui.values[ 1 ]);
			  }
			});
		
		// when clicking on pagination number
		jQuery('.lib-list-books .pagination1 ul li a').live('click',function(e){
			e.preventDefault();
			var navigation_Page	= jQuery(this).attr('href').split('=')[1];
			
			if( navigation_Page == '' || navigation_Page == null)
			{
				navigation_Page = 1;
			}
			search_books(navigation_Page);
		});
	}

});

function search_books(navigation_Page)
{
	var book_search_data = jQuery('#lib_frm_search').serialize();
	jQuery.ajax({
				type 	: 'POST',
				url      : libVar.ajax_url,
				dataType :"json",
				data: {
					action: 'lib_search_book',
					search_data : book_search_data,
					page_number : navigation_Page,
				},
				success: function(response)
				{
					jQuery('.lib-list-books table tr.book-data ').remove();
					jQuery('.lib-list-books .pagination1').html('');
					if(response.status)
					{
						jQuery(response.search_result).insertAfter(jQuery('.lib-list-books table tr.heading'));
						if(response.pagination_result != null && response.pagination_result != '')
						{
							jQuery('.lib-list-books .pagination1').html(response.pagination_result);
							jQuery('.lib-list-books .pagination1 ul li a.prev').removeClass('prev');
							jQuery('.lib-list-books .pagination1 ul li a.next').removeClass('next');
						}
					}
					else
					{
						jQuery(response.no_records).insertAfter(jQuery('.lib-list-books table tr.heading'));
					}
				},
				error:function(xhr,rrr,error)
				{

				}
			});
}
