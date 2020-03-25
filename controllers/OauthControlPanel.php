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

use Arikaim\Core\Controllers\ControlPanelApiInterface;
use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

use Arikaim\Core\Controllers\Traits\Status;

/**
 * Oauth control panel controller
*/
class OauthControlPanel extends ApiController implements ControlPanelApiInterface
{
    use Status;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('oauth::admin.messages');
    }

    /**
     * Constructor
     * 
     * @param Container $container
     * @return void
     */
    public function __construct($container)
    {
        parent::__construct($container);
        
        $this->setExtensionName('oauth');
        $this->setModelClass('OauthTokens');
    }

    /**
     * Delete token
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function deleteController($request, $response, $data)
    { 
        $this->requireControlPanelPermission();

        $this->onDataValid(function($data) {
            $uuid = $data->get('uuid');
            $model = Model::OauthTokens('oauth')->findByid($uuid);

            $result = $model->delete();
            $this->setResponse($result,function() use($uuid) {            
                $this
                    ->message('delete')
                    ->field('uuid',$uuid);  
            },'errors.delete');
        }); 
        $data->validate();
    }
}
