<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Oauth\Service;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Service\Service;
use Arikaim\Core\Service\ServiceInterface;

/**
 * Oauth service class
*/
class OauthService extends Service implements ServiceInterface
{
    /**
     * Init service
     */
    public function boot()
    {
        $this->setServiceName('oauth');
    }

    /**
     * Get access token
     *
     * @param integer $userId
     * @param string  $provider
     * @param integer $type
     * @return string|null
     */
    public function getAccessToken(int $userId, string $provider, int $type = 2): ?string
    {
        $model = Model::OauthTokens('oauth')->getUserToken($userId,$provider,$type);
        if ($model === false) {
            return null;
        }

        return ($model->isExpired() == false) ? $model->access_token : null;
    }
}
