'use strict';

$(document).ready(function() {     
    safeCall('oauthTokensView',function(obj) {
        obj.initRows();
    },true);   
}); 