/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
'use strict';

function OauthSettings() {
    
    this.init = function() {  
        arikaim.ui.button('.auth-button',function(element) {
            var driverName = $(element).attr('driver-name');         
          
            oauth.openAuthWindow(driverName);    
        });
    };
}

var oautSettings = new OauthSettings();

arikaim.component.onLoaded(function() {
    oautSettings.init();
});