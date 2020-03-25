<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Oauth\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Access\Interfaces\UserProviderInterface;
use Arikaim\Core\Models\Users;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Access\Traits\Auth;

/**
 * OauthTokens model class
 */
class OauthTokens extends Model implements UserProviderInterface  
{
    const OAUTH1 = 1;
    const OAUTH2 = 2;

    use Uuid,    
        Status, 
        Auth,   
        DateCreated,    
        Find;
    
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'oauth_tokens';

    /**
     * Auth id column name
     *
     * @var string
     */
    protected $authIdColumn = 'user_id';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'access_token',
        'type',
        'resource_owner_id',
        'refresh_token',
        'user_id',       
        'driver',
        'date_created',
        'date_expired'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get user credentials
     *
     * @param array $credential
     * @return mixed|false
     */
    public function getUserByCredentials(array $credentials)
    {
        $token = (isset($credentials['token']) == true) ? $credentials['token'] : null;
        $driver = (isset($credentials['driver']) == true) ? $credentials['driver'] : null;

        $model = $this->getToken($token,$driver);
        if (is_object($model) == false) {
            return false;
        }

        // Check is expired
        if ($model->isExpired() == true) {
            return false;
        }

        // Check is disabled by admin 
        if ($model->status != 1) {
            return false;
        }

        // Check for user relation
        if (empty($model->user_id) == true) {
            return false;
        }

        return $model->user;
    }

    /**
     * Return true if password is correct.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return false;
    }

    /**
     * Return user details by auth id
     *
     * @param string|integer $id
     * @return array|false
     */
    public function getUserById($id)
    {
        $user = new Users();
        $model = $user->findById($id);
        
        if (is_object($model) == false) {
            return false;
        }

        return $model->toArray();
    }

    /**
     * Get user relation
     *
     * @return mixed
     */
    public function user()
    {      
        return $this->belongsTo(Users::class);
    }

    /**
     * expired attribute
     *
     * @return boolean
     */
    public function getExpiredAttribute()
    {
        return $this->isExpired();
    }

    /**
     * Return true if token is expired
     *
     * @return boolean
     */
    public function isExpired()
    {
        if (empty($this->date_expired) == true) {
            return false;
        }

        return (time() > $this->date_expired);
    }

    /**
     * Return true if token exist
     *
     * @param string $token
     * @param string $driver
     * @return boolean
     */
    public function hasToken($token, $driver)
    {
        $model = $this->where('access_token','=',$token)->where('driver','=',$driver)->first();

        return is_object($model);
    }

    /**
     * Get token data
     *
     * @param string $token
     * @param string $driver
     * @return Model|false
     */
    public function getToken($token, $driver)
    {
        $model = $this->where('access_token','=',$token)->where('driver','=',$driver)->first();

        return (is_object($model) == true) ? $model : false;
    }

    /**
     * Find token by resource id 
     *
     * @param string $resouceId
     * @param string $driver
     * @return Model|false
     */
    public function findByResourceId($resouceId, $driver)
    {
        $model = $this->where('resource_owner_id','=',$resouceId)->where('driver','=',$driver)->first();

        return (is_object($model) == true) ? $model : false;
    }

    /**
     * Save user id
     *
     * @param string $token
     * @param string $driver
     * @param integer $userId
     * @return boolean
     */
    public function saveUserId($accessToken, $driver, $userId)
    {
        $token = $this->getToken($accessToken, $driver);
        if (is_object($token) == false) {           
            return false;
        }
        $token->user_id = $userId;

        return $token->save();
    }

    /**
     * Save access token
     *
     * @param string $token
     * @param string|null $refreshToken
     * @param string $driver
     * @param string resourceOwnerId
     * @param int|null $expire
     * @param integer $type
     * @return Model|false
     */
    public function saveToken($token, $driver, $resourceOwnerId, $expire = null, $type = Self::OAUTH1, $refreshToken = null)
    {
        $model = $this->findByResourceId($resourceOwnerId,$driver);
        if ($model !== false) {
           $model->delete(); 
        }
        if ($this->hasToken($token,$driver) == true) {
            return false;
        }

        return $this->create([
            'access_token'      => $token,
            'refresh_token'     => $refreshToken,
            'driver'            => $driver,
            'type'              => $type,
            'date_expired'      => $expire,
            'resource_owner_id' => $resourceOwnerId
        ]);
    }
}
