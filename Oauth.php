<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Oauth;

use Arikaim\Core\Extension\Extension;

/**
 * Oauth extension
*/
class Oauth extends Extension
{
    /**
     * Install extension routes, events, jobs ..
     *
     * @return void
    */
    public function install()
    {
        // Control Panel
        $this->addApiRoute('DELETE','/api/oauth/admin/delete/{uuid}','OauthControlPanel','delete','session');      
        $this->addApiRoute('PUT','/api/oauth/admin/status','OauthControlPanel','setStatus','session');    
        // Pages
        $this->addPageRoute('/oauth/callback/{provider}[/{action}]','OauthPages','callback','oauth>oauth.success',null,'oauth.callback',false);
        $this->addPageRoute('/oauth/authentication/{provider}[/{action}]','OauthPages','authentication','oauth>oauth.authentication',null,'oauth.authentication',false);
        // Db tables
        $this->createDbTable('OauthTokensSchema');  
        // Events
        $this->registerEvent('oauth.auth','OAuth authorize');
    }
    
    /**
     * UnInstall
     *
     * @return void
     */
    public function unInstall()
    {         
    }
}
