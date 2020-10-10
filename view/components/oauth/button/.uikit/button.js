'use strict';

$(document).ready(function() {
    arikaim.ui.button('.oauth-authorize-button',function(element) {
        var provider = $(element).attr('provider');
        var action = $(element).attr('action');
        var redirectUrl = $(element).attr('redirect');

        oauth.openAuthWindow(provider,action,redirectUrl);        
    });       
});