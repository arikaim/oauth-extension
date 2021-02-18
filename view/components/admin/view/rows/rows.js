'use strict';

arikaim.component.onLoaded(function() {   
    safeCall('oauthTokensView',function(obj) {
        obj.initRows();
    },true);   
}); 