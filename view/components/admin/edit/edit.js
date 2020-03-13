'use strict';

$(document).ready(function() {
   
    $('#ads_dropdown').dropdown({
        onChange: function(value, text, choice) { 
            arikaim.page.loadContent({
                id: 'ad_form_content',
                component: 'ads::admin.form',
                params: { uuid: value }
            },function(result) {
                initAdsForm();
            });
        }
    });
    
    function initAdsForm() {
        arikaim.ui.form.onSubmit("#ads_form",function() {  
            return adsControlPanel.update('#ads_form');
        },function(result) {          
            arikaim.ui.form.showMessage(result.message);        
        });
    }
    
    initAdsForm();    
});