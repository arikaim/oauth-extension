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
        $this->addApiRoute('DELETE','/api/admin/oauth/delete/{uuid}','OauthControlPanel','delete','session');      
        $this->addApiRoute('PUT','/api/admin/oauth/status','OauthControlPanel','setStatus','session');    
        // Pages
        $this->addPageRoute('/oauth/callback/{provider}[/{config}]','OauthPages','callback','oauth>oauth.success',null,'oauth.callback',false);
        $this->addPageRoute('/oauth/authentication/{provider}[/{action}[/{config}]]','OauthPages','authentication','oauth>oauth.authentication',null,'oauth.authentication',false);
        // Db tables
        $this->createDbTable('OauthTokens');  
        // Events
        $this->registerEvent('oauth.auth','OAuth authorize success');
        // Ssevice
        $this->registerService('OauthService');   
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
