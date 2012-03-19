sidebarng = {

  init: function( ) {

    jQuery( '.sidebarng .sbhead' ).css( 'cursor', 'pointer' )
	.each( function () {
	    var $header,$box,$toggle;

	    $header = jQuery( this );
	    $box = $header.next( );
	    $toggle = jQuery(document.createElement('span'))
		    .html('<span>-</span>' )
		    .addClass( 'sbtoggle close' );


	    $header
		.prepend( $toggle )
		.click( function( ) {
		    if( !$box.stop( true, true ).is( ':hidden' )) {
			$toggle.addClass( 'open' );
			$toggle.removeClass( 'close' );
			sidebarng.setcookie( $box.attr('class').replace(/ /, '' ), 0 );
		    } else {
			$toggle.addClass( 'close' );
			$toggle.removeClass( 'open' );
			sidebarng.setcookie( $box.attr('class').replace(/ /, '' ), 1 );
		    }
		    $box.toggle( );
		});

	    if( sidebarng.getcookie( $box.attr('class').replace(/ /, '' ))) {
		$header.click( );
	    }

        });

  },
  setcookie: function( k, v ) {
    DokuCookie.setValue( 'sidebarng_hide:'+k, v );
    return true;
  },
  getcookie: function( k ) {
    return DokuCookie.getValue( 'sidebarng_hide:'+k );
  }
}
jQuery( sidebarng.init );

