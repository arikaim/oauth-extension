<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Ads\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiInterface;
use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;

use Arikaim\Core\Controllers\Traits\Status;

/**
 * Ads control panel controller
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
        $this->loadMessages('ads::admin.messages');
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
     *  Create new ad
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addController($request, $response, $data) 
    {       
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {            
            $model = Model::OauthTokens('oauth');

            if (is_object($model->findByColumn($data['title'],'title')) == true) {
                $this->error('errors.exist');
                return;
            }
            $newModel = $model->createAd($data['title'],$data['code'],$data['description']);
                    
            $this->setResponse(is_object($newModel),function() use($newModel) {                                
                $this
                    ->message('add')
                    ->field('uuid',$newModel->uuid);                         
            },'errors.add');
        });
        $data           
            ->addRule('text:min=2','tags')           
            ->validate();       
    }

    /**
     * Update ad
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateController($request, $response, $data) 
    {       
        $this->requireControlPanelPermission();
        
        $this->onDataValid(function($data) {
            $uuid = $data->get('uuid');
            $model = Model::Ads('ads');

            $exists = $model->where('title','=',$data['title'])->where('uuid','<>',$uuid)->exists();
            if ($exists == true) {
                $this->error('errors.exist');
                return;
            }

            $ad = $model->findById($uuid);
            $result = $ad->update($data->toArray());
               
            $this->setResponse($result,function() use($model) {                                
                $this
                    ->message('update')
                    ->field('uuid',$model->uuid);                         
            },'errors.update');
        });
        $data           
            ->addRule('text:min=2','title')                        
            ->validate();       
    }
   
    /**
     * Delete ad
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
            $model = Model::Ads('ads')->findByid($uuid);

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
