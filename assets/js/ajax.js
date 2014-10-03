/**
 * Implements the global hide/show of AJAX loader and error display.
 */

/**
 * ajaxStart()
 *
 * Simply makes the loader img visible when the first
 * AJAX method starts.
 */
$(document).ajaxStart(function(){
    $('#ajax-loader').css('display', 'block').css('top', posTop() + 15);
});

//--------------------------------------------------------------------

/**
 * ajaxStop()
 *
 * Simply hides the loader img when the last running
 * AJAX method is done.
 */
$(document).ajaxStop(function(){
    $('#ajax-loader').css('display', 'none');
});

//--------------------------------------------------------------------
/**
 * Browser Position
 * copyright Stephen Chapman, 3rd Jan 2005, 8th Dec 2005
 */
function posTop()
{
    return typeof window.pageYOffset != 'undefined' ?  window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;
}

//--------------------------------------------------------------------