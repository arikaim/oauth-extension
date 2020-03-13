<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Ads;

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
        $this->addApiRoute('POST','/api/ads/admin/add','AdsControlPanel','add','session');   
        $this->addApiRoute('PUT','/api/ads/admin/update','AdsControlPanel','update','session');   
        $this->addApiRoute('DELETE','/api/ads/admin/delete/{uuid}','AdsControlPanel','delete','session');      
        $this->addApiRoute('PUT','/api/ads/admin/status','AdsControlPanel','setStatus','session');    
                         
        // Db tables
        $this->createDbTable('OauthTokensSchema');  
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
