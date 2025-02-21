/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function OauthTokensView() {
    var self = this;
    
    this.init = function() {
        this.loadMessages('oauth::admin');

        order.init('oauth_rows','oauth::admin.view.rows','oauth');        
        paginator.init('oauth_rows');   

        search.init({
            id: 'oauth_rows',
            component: 'oauth::admin.view.rows',
            event: 'oauth.search.load'
        },'oauth')  
        
        arikaim.events.on('oauth.search.load',function(result) {      
            paginator.reload();
            self.initRows();    
        },'oauthSearch');

        $('#drivers_dropdown').dropdown({
            onChange: function(value) {                    
                arikaim.ui.loadComponent({
                    mountTo: 'auth_content',
                    component: 'oauth::oauth.button',
                    params: {
                        action: 'get-token',
                        provider: value,
                        class: 'primary mini button',
                        title: 'Authorize'
                    }
                });
            }
        });
    };

    this.initRows = function() {

        arikaim.ui.loadComponentButton('.token-details');

        $('.status-dropdown').dropdown({
            onChange: function(value) {               
                var uuid = $(this).attr('uuid');
                oauthControlPanel.setStatus(uuid,value);
            }
        });    

        arikaim.ui.button('.view-token',function(element) {
            var token = $(element).attr('token');
            var uuid = $(element).attr('uuid');            
            $('#token_' + uuid).html(token);

            return true;
        });  

        arikaim.ui.button('.delete-button',function(element) {
            var uuid = $(element).attr('uuid');
        
            modal.confirmDelete({ 
                title: self.getMessage('remove.title'),
                description: self.getMessage('remove.content')
            },function() {
                oauthControlPanel.delete(uuid,function(result) {
                    arikaim.ui.table.removeRow('#' + uuid);     
                });
            });
        });       
    };
};

var oauthTokensView = createObject(OauthTokensView,ControlPanelView);

arikaim.component.onLoaded(function() {
    oauthTokensView.init();
    oauthTokensView.initRows();  
}); 