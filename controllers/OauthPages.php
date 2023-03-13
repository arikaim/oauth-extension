<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Oauth\Controllers;

use Arikaim\Core\Controllers\Controller;
use Arikaim\Core\Db\Model;
use Arikaim\Modules\Oauth\Oauth;
use Arikaim\Core\Http\Session;

/**
 * Oauth pages controler
*/
class OauthPages extends Controller
{
    /**
     * Oauth callback page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function callback($request, $response, $data) 
    { 
        $language = $this->getPageLanguage($data);
        $oauthModule = new Oauth();
        $provider = $data->get('provider'); 
        $provider = ($provider == 'google') ? 'google.oauth' : $provider;

        $action = $oauthModule->getAction(); 

        // custom driver config
        $driverConfig = $data->get('config',null);
        if (empty($driverConfig) == false) {
            $config = $this->get('driver')->getDriver($driverConfig);
            $config = $config['config'] ?? null;
        }

        $driver = $this->get('driver')->create($provider,['action' => $action],$config ?? null);
        if ($driver == null) {
            return $this->pageLoad($request,$response,$data,'oauth>oauth.error',$language); 
        }
        $oauthModule->clearAction();
        $tokens = Model::OauthTokens('oauth');

        // OAuth 1
        if ($driver->getType() == 1) {     
            $oauthToken = $this->getQueryParam($request,'oauth_token');
            $oauthVerifier = $this->getQueryParam($request,'oauth_verifier');
            if (empty($oauthToken) == true) {
                return $this->pageLoad($request,$response,$data,'oauth>oauth.error',$language); 
            }

            $temporaryCredentials = $oauthModule->getTemporaryCredentials();
            $tokenCredentials = $driver->getInstance()->getTokenCredentials($temporaryCredentials,$oauthToken,$oauthVerifier);
            $oauthModule->clearTemporaryCredentials();
            $accessToken = $tokenCredentials->getIdentifier();
            $tokenSecret = $tokenCredentials->getSecret();
            $user = $driver->getInstance()->getUserDetails($tokenCredentials);
            $userData = $user->getIterator()->getArrayCopy();
            $resourceOwnerId = $driver->getInstance()->getUserUid($tokenCredentials);
            $resourceInfo = $driver->getResourceInfo($tokenCredentials);
            $refreshToken = null; 
            $expireDate = null;
        }
        // OAuth 2
        if ($driver->getType() == 2) {
            $state = $this->getQueryParam($request,'state'); 
            if ($state != $oauthModule->getState() || empty($state) == true) {
                $oauthModule->clearState();

                return $this->pageLoad($request,$response,$data,'oauth>oauth.error',$language);                
            }       
            // get token
            $code = $this->getQueryParam($request,'code');  
            
            $token = $driver->getInstance()->getAccessToken('authorization_code',[
                'code' => $code
            ]);

            $user = $driver->getInstance()->getResourceOwner($token);
            $resourceOwnerId = $user->getId();
            $userData = $user->toArray();
            $accessToken = $token->getToken();
            $tokenSecret = null;
            $refreshToken = $token->getRefreshToken();
            $expireDate = $token->getExpires();
            $resourceInfo = $driver->getResourceInfo($token);
        }
        
        Session::set('vars.access-token',$accessToken);

        // save access token to db
        $tokens->saveToken(
            $accessToken,
            $tokenSecret,
            $provider,
            $resourceOwnerId,
            $expireDate,
            $driver->getType(),
            $refreshToken,
            $this->getUserId(),
            Session::get('oauth.scope',null)
        );
        
        // dispatch event     
        $this->get('event')->dispatch('oauth.auth',[
            'respurce_owner' => $userData,
            'access_token'   => $accessToken,
            'driver'         => $provider,
            'action'         => $action,
            'user'           => $resourceInfo->toArray(),           
            'type'           => $driver->getType()
        ]);

        if ($action != 'get-token') {       
            $data['redirect_url'] = \trim($this->get('options')->get('users.login.redirect'));
        }
              
        $data['action'] = $action;
        $data['access_token'] = $accessToken;

        return $this->pageLoad($request,$response,$data,'current>oauth.success',$language); 
    }

    /**
     * Oauth authentication page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function authentication($request, $response, $data) 
    { 
        $provider = $data->get('provider');
        $driverConfig = $data->get('config',null);
        $action = $data->get('action',null);
      
        if (empty($driverConfig) == false) {
            $config = $this->get('driver')->getDriver($driverConfig);
            $config = $config['config'] ?? null;
        }
        
        $driver = $this->get('driver')->create($provider,['action' => $action],$config ?? null);

        if ($driver == null) {
            return $this->pageLoad($request,$response,$data,'current>oauth.error'); 
        }
        $oauthModule = new Oauth();
        $oauthModule->saveAction($action);

        // OAuth1 
        if ($driver->getType() == 1) {
            $credentials = $driver->getInstance()->getTemporaryCredentials();
            $oauthModule->saveTemporaryCredentials($credentials);
            
            $authUrl = $driver->getInstance()->getAuthorizationUrl($credentials);  
        }

        // OAuth2
        if ($driver->getType() == 2) {
            $options = $driver->getOptions();          
            $authUrl = $driver->getInstance()->getAuthorizationUrl($options);
            $oauthModule->saveState($driver->getInstance()->getState());
        }
       
        return $this->withRedirect($response,$authUrl);
    }
}
