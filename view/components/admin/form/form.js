'use strict';

arikaim.page.onReady(function() {    
    arikaim.ui.form.addRules("#ads_form",{
        inline: false,
        fields: {
            title: {
                identifier: "title",      
                rules: [{
                    type: "minLength[2]"       
                }]
            },
            code: {
                identifier: "code",      
                rules: [{
                    type: "minLength[2]"       
                }]
            }
        }
    });   
});