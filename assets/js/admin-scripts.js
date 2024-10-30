(function($) {
	$(document).on( 'click', '.uac-tabs a', function() {

		jQuery('#uac-message').hide(500);

		// Hide All sections
		$('.uac-section').hide();

		// Show the selcted one
		$('.uac-section').eq($(this).index()).show();

		// Highlight the Tab
		$('.uac-tabs a').attr( 'class', 'nav-tab' );
		$(this).attr( 'class', 'nav-tab nav-tab-active' );

		// Update current tab number, to highlight it after the form is submitted.
		$('#uac_current_tab').val( $(this).index() + 1 );

		return false;
	})

	// Click first Tab
	$('.nav-tab-active').click();

})( jQuery );

function uac_copy_shortcode( id ) {
	var copyText = document.getElementById( id );

	/* Select the text field */
	copyText.select();
	copyText.setSelectionRange(0, 99999); /* For mobile devices */

	/* Copy the text inside the text field */
	navigator.clipboard.writeText(copyText.value);

	jQuery('#uac-message').html( '<div class="notice notice-success is-dismissible"><p>Shortcode Copied!</p></div>' ).show(500);
}
