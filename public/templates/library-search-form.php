<?php
/*
 * Book Search Public View
 */
 
?>
<?php $book_max_price = $this->lib_book_max_price(); ?>
	<div class="lib-search-form">
		<h2><?php echo __('Book Search','lib-book-search');?></h2>
		<form id="lib_frm_search" name="lbs_frm_search" method="post">
			<div class="left-part">
				<div class="form-row">
					<div class="form-group">
						<label><?php echo __('Book Name','lib-book-search');?></label>
						<input type="text" class="form-control half-left" placeholder="<?php echo __('Book Name','lib-book-search');?>" name="book_name">
					</div>
					<div class="form-group">
						<label><?php echo __('Publisher','lib-book-search');?></label>
						
						<?php
							// retrieving all the terms of "publisher"
							$publisher_terms = get_terms( array('taxonomy' => 'lib_publisher') );
							if($publisher_terms) {
								echo '<select name="book_publisher">';
									echo '<option value="">'.__('Select Publisher','lib-book-search').'</option>';
									foreach( $publisher_terms as $publisher_term) {
										echo '<option value="'.$publisher_term->term_id.'">'.$publisher_term->name.'</option>';			
									}
								echo '</select>';
							}
						?>
							
						
					</div>
				</div>
				<div class="form-group">
					<label><?php echo __('Price','lib-book-search');?></label>
					<div id="slider-range"></div>
					<div id="amount">$1 - $<?php echo $book_max_price; ?></div>
					<input type="hidden" id="book_min_price" name="book_min_price" value="1">
					<input type="hidden" id="book_max_price" name="book_max_price" value="<?php echo $book_max_price; ?>">
					
				</div>
			</div>
			<div class="right-part">
				<div class="form-group">
					<label><?php echo __('Author','lib-book-search');?></label>
					<input type="text" class="form-control half-left" placeholder="<?php echo __('Author','lib-book-search');?>" name="book_author">
				</div>
				<div class="form-group">
					<label><?php echo __('Rating','lib-book-search');?></label>
					<select name="book_rating">
						<option value=""><?php echo __('Select Rating','lib-book-search');?></option>
						<?php
							for( $rating = 1; $rating <= 5 ; $rating++) {
								echo '<option value="'.$rating.'">'.$rating.'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="lib_btn_wrapper">
				<input type="submit" class="btn btn-default" id="lbs_btn_submit" value="<?php echo __('Search','lib-book-search');?>">
			</div>
		</form>
	</div>
	<div class="lib-list-books">
		<table>
			<tr class="heading">
				<th>No</th>
				<th>Book Name</th>
				<th>Price</th>
				<th>Author</th>
				<th>Publisher</th>
				<th>Rating</th>
			</tr>
		</table>
		<div class="pagination1"></div>
	</div>