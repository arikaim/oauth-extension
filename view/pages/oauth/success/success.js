'use strict';

$(document).ready(function() {
    var redirectUrl = $('#redirect').attr('redirect');
    redirectUrl = (isEmpty(redirectUrl) == false) ? arikaim.getBaseUrl() + '/' + redirectUrl : null;
  
    window.onunload = function() {
        if (isEmpty(redirectUrl) == false) {               
            window.opener.document.location.href = redirectUrl;
        }
    }   
    
    window.opener.arikaim.events.emit('oauth.success');

    window.close();
});