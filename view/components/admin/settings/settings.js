/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
*/
"use strict";

function OauthSettings() {
    
    this.init = function() {
        arikaim.events.on('driver.config',function(element,name,category) {
            arikaim.ui.setActiveTab('#settings_button');
            return drivers.loadConfig(name,'tab_content');           
        },'driverConfig');       
        
        arikaim.ui.button('.auth-button',function(element) {
            var driverName = $(element).attr('driver-name');         
          
            oauth.openAuthWindow(driverName);    
        });
    };
}

var oautSettings = new OauthSettings();

arikaim.page.onReady(function() {
    oautSettings.init();
});