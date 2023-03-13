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
        'access_token_secret',
        'type',
        'resource_owner_id',
        'refresh_token',
        'user_id',     
        'scopes',  
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
     * @return array|null
     */
    public function getUserByCredentials(array $credentials): ?array
    {
        $token = $credentials['token'] ?? null;
        $driver = $credentials['driver'] ?? null;

        $model = $this->getToken($token,$driver);
        if ($model === false) {
            return null;
        }

        // Check is expired
        if ($model->isExpired() == true) {
            return null;
        }

        // Check is disabled by admin 
        if ($model->status != 1) {
            return null;
        }

        // Check for user relation
        if (empty($model->user_id) == true) {
            return null;
        }

        $user = $model->user()->first();
        if (empty($user) == true) {
            return null;
        }

        $authId = $user->getAuthId();
        $user = $user->toArray();
        $user['auth_id'] = $authId;

        return $user;
    }

    /**
     * Return true if password is correct.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return false;
    }

    /**
     * Return user details by auth id
     *
     * @param string|integer $id
     * @return array|null
     */
    public function getUserById($id): ?array
    {
        $user = new Users();
        $model = $user->findById($id);
        
        return ($model == null) ? null : $model->toArray();
    }

    /**
     * Get user relation
     *
     * @return Relation|null
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
    public function isExpired(): bool
    {
        if (empty($this->date_expired) == true) {
            return false;
        }

        return (\time() > $this->date_expired);
    }

    /**
     * Return true if token exist
     *
     * @param string $token
     * @param string $driver
     * @return boolean
     */
    public function hasToken(string $token, string $driver): bool
    {
        $model = $this->where('access_token','=',$token)->where('driver','=',$driver)->first();

        return ($model != null);
    }

    /**
     * Get token data
     *
     * @param string $token
     * @param string $driver
     * @return Model|false
     */
    public function getToken(string $token, string $driver)
    {
        $model = $this->where('access_token','=',$token)->where('driver','=',$driver)->first();

        return ($model !== null) ? $model : false;
    }

    /**
     * Get user token data
     *
     * @param integer $userId
     * @param string $driver
     * @return Model|false
     */
    public function getUserToken(int $userId, string $driver, int $type = Self::OAUTH1)
    {
        $model = $this->where('user_id','=',$userId)
            ->where('driver','=',$driver)
            ->where('type','=',$type)
            ->first();

        return ($model !== null) ? $model : false;
    }

    /**
     * Find token by resource id 
     *
     * @param string $resouceId
     * @param string $driver
     * @return Model|false
     */
    public function findByResourceId($resouceId, string $driver)
    {
        $model = $this->where('resource_owner_id','=',$resouceId)->where('driver','=',$driver)->first();

        return ($model !== null) ? $model : false;
    }

    /**
     * Save user id
     *
     * @param string $token
     * @param string $driver
     * @param integer $userId
     * @return boolean
     */
    public function saveUserId(string $accessToken, string $driver, int $userId): bool
    {
        $token = $this->getToken($accessToken,$driver);
        if ($token === false) {           
            return false;
        }
        $token->user_id = $userId;

        return (bool)$token->save();
    }

    /**
     * User tokens query
     *
     * @param Builder $query
     * @param integer $userId
     * @return Builder
     */
    public function scopeUserTokensQuery($query, int $userId)
    {
        return $query->where('user_id','=',$userId);
    }

    /**
     * Save access token
     *
     * @param string $token
     * @param string|null $tokenSecret
     * @param string|null $refreshToken
     * @param string $driver
     * @param string resourceOwnerId
     * @param int|null $expire
     * @param integer $type
     * @param string|null $scopes
     * @return Model|false
     */
    public function saveToken(
        string $token, 
        ?string $tokenSecret, 
        string $driver, 
        $resourceOwnerId, 
        ?int $expire = null, 
        int $type = Self::OAUTH1, 
        ?string $refreshToken = null,
        ?int $userId = null,
        ?string $scopes = null
    )
    {
        $resourceOwnerId = (empty($resourceOwnerId) == true) ? $userId : $resourceOwnerId;
        
        $model = $this->findByResourceId($resourceOwnerId,$driver);
        if ($model !== false) {
           $model->delete(); 
        }
        if ($this->hasToken($token,$driver) == true) {
            return false;
        }

        return $this->create([
            'access_token'        => $token,
            'access_token_secret' => $tokenSecret,
            'refresh_token'       => $refreshToken,
            'driver'              => $driver,
            'type'                => $type,
            'date_expired'        => $expire,
            'resource_owner_id'   => $resourceOwnerId,
            'scopes'              => $scopes,
            'user_id'             => (empty($userId) == true) ? null : $userId
        ]);
    }
}
