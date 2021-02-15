/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function OauthControlPanel() {
  
    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/oauth/admin/delete/' + uuid,onSuccess,onError);          
    };

    this.setStatus = function(uuid, status, onSuccess, onError) { 
        var data = { 
            status: status,
            uuid: uuid 
        };
        
        return arikaim.put('/api/oauth/admin/status',data,onSuccess,onError);           
    };

    this.init = function() {    
        arikaim.ui.tab();
    };
}

var oauthControlPanel = new OauthControlPanel();

$(document).ready(function() {
    oauthControlPanel.init();
});