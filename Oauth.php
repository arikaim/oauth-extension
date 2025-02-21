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
        $this->addPageRoute('/oauth/callback/{provider}[/{config}]','OauthPages','callback','current>oauth.success',null,'oauth.callback',false);
        $this->addPageRoute('/oauth/authentication/{provider}[/{action}[/{config}]]','OauthPages','authentication','current>oauth.authentication',null,'oauth.authentication',false);
       
        // Events
        $this->registerEvent('oauth.auth','OAuth authorize success');
        // Ssevice
        $this->registerService('OauthService');   
    }
    
    public function dbInstall(): void
    {         
        // Db tables
        $this->createDbTable('OauthTokens');  
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
