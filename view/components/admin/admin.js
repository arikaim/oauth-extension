/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function OauthControlPanel() {
  
    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/admin/oauth/delete/' + uuid,onSuccess,onError);          
    };

    this.setStatus = function(uuid, status, onSuccess, onError) { 
        var data = { 
            status: status,
            uuid: uuid 
        };
        
        return arikaim.put('/api/admin/oauth/status',data,onSuccess,onError);           
    };    
}

var oauthControlPanel = new OauthControlPanel();

arikaim.component.onLoaded(function() {
    arikaim.ui.tab();
});