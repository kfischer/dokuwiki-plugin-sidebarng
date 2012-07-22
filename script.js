// from lib/scripts/index.js 
var right_dw_index = jQuery('#right__index__tree').dw_tree({deferInit: true,
    load_data: function  (show_sublist, $clicky) {
        jQuery.post(          
            DOKU_BASE + 'lib/exe/ajax.php',
            $clicky[0].search.substr(1) + '&call=index',
            show_sublist, 'html'
        );
    }
});  

jQuery(function(){

// from lib/scripts/index.js 
  var $tree = jQuery('#right__index__tree');
  right_dw_index.$obj = $tree;
  right_dw_index.init();
 
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
            $header.delegate( '.close', 'mouseover', function( ) {
                $header.click( );
            });
            $header.delegate( '.open', 'mouseover', function( ) {
                $header.click( );
            });
console.log( sidebarng.getcookie( $box.attr('class').replace(/ /, '' )));
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

})

