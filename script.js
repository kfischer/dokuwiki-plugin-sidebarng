/**      
 * Adds the toggle switch to the TOC
 */      
function addSbTocToggle() {   
    if(!document.getElementById) return;
    var header = jQuery('#sb__right__toc__header');
    if(!header.length) return; 
    var obj          = document.createElement('span');
    obj.id           = 'sb__right__toc__toggle';
    obj.innerHTML    = '<span>&minus;</span>';
    obj.className    = 'toc_close';
    obj.style.cursor = 'pointer';  
         
    //prependChild(header,obj);
    jQuery( header ).prepend( obj );
         
    //obj.parentNode.onclick = toggleSbRightToc;
    jQuery( obj.parentNode ).bind( 'click', toggleSbToc );
    try {
       obj.parentNode.style.cursor = 'pointer';
       obj.parentNode.style.cursor = 'hand';
    }catch(e){}                
}        
         
/**      
 * This toggles the visibility of the Table of Contents
 */      
function toggleSbToc() {  
  var toc = jQuery('#sb__right__toc__inside');
  var obj = jQuery('#sb__right__toc__toggle');
         
  if( toc.css( 'display' ) == 'none' ) {
    toc.css( 'display', 'block' ); 
    obj.innerHTML       = '<span>&minus;</span>';
    obj.className       = 'toc_close';
  } else {                     
    toc.css( 'display', 'none' );  
    obj.innerHTML       = '<span>+</span>';
    obj.className       = 'toc_open';
  }      
}        


var right_dw_index = jQuery('#sb__index__tree').dw_tree({deferInit: true,                                                                     
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
 
// add TOC events
    jQuery(addSbTocToggle);
 
});

