<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Oauth\Subscribers;

use Arikaim\Core\Events\EventSubscriber;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;
use Arikaim\Core\Db\Model;

/**
 * Execute oauth tokens actions 
*/
class UserSubscriber extends EventSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->subscribe('user.before.delete','deleteUserTokens');
    }

    /**
     * Delete okens for user
     *
     * @param EventInterface $event
     * @return bool
     */
    public function deleteUserTokens($event)
    {
        $data = $event->getParameters(); 
        $userId = $data['id'] ?? null;
        if (empty($userId) == false) {
            // delete user tokens
            $tokens = Model::OauthTokens('oauth');
            
            return (bool)$tokens->userTokensQuery($userId)->delete();
        }

        return false;
    }
}
