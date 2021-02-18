'use strict';

arikaim.component.onLoaded(function() {
    var redirectUrl = $('#redirect').attr('redirect');
    redirectUrl = arikaim.getBaseUrl() + '/' + redirectUrl;

    window.onunload = function() {
        if (isEmpty(redirectUrl) == false) {               
            window.opener.document.location.href = redirectUrl;
        }
    }   
    window.close();
});