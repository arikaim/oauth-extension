<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Oauth\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 * Oauth tokens db table
 */
class OauthTokens extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'oauth_tokens';

    /**
     * Create table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function create($table) 
    {            
        // columns    
        $table->id();      
        $table->prototype('uuid'); 
        $table->status();           
        $table->string('access_token')->nullable(false); 
        $table->string('access_token_secret')->nullable(true);                
        $table->integer('type')->nullable(true)->default(1);
        $table->string('resource_owner_id')->nullable(true);  
        $table->string('refresh_token')->nullable(true);  
        $table->string('driver')->nullable(false);  
        $table->text('scopes')->nullable(true);       
        $table->string('session_id')->nullable(true);   
        $table->options();     
        $table->userId();
        $table->dateCreated();
        $table->dateExpired();
        // index
        $table->unique(['access_token','driver']);
        $table->unique(['user_id','driver','type']);
        $table->unique(['resource_owner_id','driver']);
        $table->unique(['session_id']);
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {  
        if ($this->hasColumn('options') == false) {
            $table->options();
        } 

        if ($this->hasColumn('session_id') == false) {
            $table->string('session_id')->nullable(true);    
        } 

        if ($this->hasColumn('access_token_secret') == false) {
            $table->string('access_token_secret')->nullable(true);    
        } 

        if ($this->hasColumn('scopes') == false) {
            $table->text('scopes')->nullable(true);     
        }     
    }
}
