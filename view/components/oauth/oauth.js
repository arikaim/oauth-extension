/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function Oauth() {   
    var authWindow;
    var onSuccessCallback;
    var redirectUrl;

    this.getAuthUrl = function(provider, action, configName) {
        var url = arikaim.getBaseUrl() + '/oauth/authentication/' + provider;
        var actionPath = (isEmpty(action) == false) ? '/' + action : '';
        var configPath = (isEmpty(configName) == false) ? '/' + configName : '';

        return  url + actionPath + configPath;
    };

    this.openAuthWindow = function(provider, action, redirect, settings, configName) {      
        var left = (screen.width / 2) - (800/2);
        var top = (screen.height / 2) - (600/2);
        if (isEmpty(settings) == true) {
            settings = 'location=no,toolbar=no,resizable=no,width=800,height=600,scrollbars=no,status=no,left=' + left + ',top=' + top;
        }
     
        redirectUrl = redirect;
        this.closeAuthWindow();
       
        authWindow = window.open(this.getAuthUrl(provider,action,configName),'auth',settings);
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