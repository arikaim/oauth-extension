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
class OauthTokensSchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = "oauth_tokens";

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
        $table->integer('type')->nullable(true)->default(1);
        $table->string('resource_owner_id')->nullable(true);  
        $table->string('refresh_token')->nullable(true);  
        $table->string('driver')->nullable(false);   
        $table->userId();
        $table->dateCreated();
        $table->dateExpired();
        // index
        $table->index('access_token');
        $table->index('resource_owner_id');
        $table->unique(['access_token','driver']);
        $table->unique(['resource_owner_id','driver']);
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {              
    }

    /**
     * Insert or update rows in table
     *
     * @param Seed $seed
     * @return void
     */
    public function seeds($seed)
    {       
    }
}
