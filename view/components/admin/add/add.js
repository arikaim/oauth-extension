'use strict';

$(document).ready(function() {
    arikaim.ui.form.onSubmit("#ads_form",function() {  
        return adsControlPanel.add('#ads_form');
    },function(result) {
        arikaim.ui.form.clear('#ads_form');
        arikaim.ui.form.showMessage(result.message);        
    });
});
