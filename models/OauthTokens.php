<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Ads\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Traits\Db\DateCreated;

/**
 * OauthTokens model class
 */
class OauthTokens extends Model  
{
    use Uuid,    
        Status,    
        DateCreated,    
        Find;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = "oauth_tokens";

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'access_token',
        'token_type',
        'resource_owner_id',
        'refresh_token',
        'user_id',       
        'date_created',
        'date_expired'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;
}
