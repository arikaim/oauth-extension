/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function OauthTokensView() {
    var self = this;

    this.init = function() {
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
    };

    this.initRows = function() {
        var component = arikaim.component.get('oauth::admin');
        var removeMessage = component.getProperty('messages.remove.content');

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
                title: component.getProperty('messages.remove.title'),
                description: removeMessage
            },function() {
                oauthControlPanel.delete(uuid,function(result) {
                    arikaim.ui.table.removeRow('#' + uuid);     
                });
            });
        });       
    };
};

var oauthTokensView = new OauthTokensView();

$(document).ready(function() {  
    oauthTokensView.init();
    oauthTokensView.initRows();  
}); 