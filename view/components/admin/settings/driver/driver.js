'use strict';

arikaim.component.onLoaded(function() {
    $('#drivers_dropdown').dropdown({
        onChange: function(value) {                    
            return drivers.loadConfigForm(value,'driver_settings');    
        }
    });
});