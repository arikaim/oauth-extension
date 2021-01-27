/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Oauth() {
    var self = this;
    var authWindow;
    var onSuccessCallback;
    var redirectUrl;

    this.getAuthUrl = function(provider, action) {
        var url = arikaim.getBaseUrl() + '/oauth/authentication/' + provider;

        return (isEmpty(action) == false) ? url + '/' + action : url;
    };

    this.openAuthWindow = function(provider, action, redirect, settings) {      
        var left = (screen.width / 2) - (800/2);
        var top = (screen.height / 2) - (600/2);
        var settings = 'location=no,toolbar=no,resizable=no,width=800,height=600,scrollbars=no,status=no,left=' + left + ',top=' + top;
        redirectUrl = redirect;

        if (isObject(authWindow) == true) {
            authWindow.close();
        }
        authWindow = window.open(this.getAuthUrl(provider,action),'auth',settings);
    };

    this.redirect = function() {  
        if (isEmpty(redirectUrl) == false) {
            arikaim.loadUrl(redirectUrl,true);
        }
    };

    this.getRedirectUrl = function() {
        return redirectUrl;
    };

    this.closeAuthWindow = function() {
        if (isObject(authWindow) == true) {
            authWindow.close();
        }
    };

    this.onSuccess = function(onSuccess) {
        onSuccessCallback = onSuccess;
    };

    this.success = function() {
        callFunciton(onSuccessCallback);
    };
}

var oauth = new Oauth();