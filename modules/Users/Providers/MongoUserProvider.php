<?php

namespace Modules\Users\Providers;

use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Modules\Users\Entities\User;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;

class MongoUserProvider implements UserProvider
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * Create a new database user provider.
     *
     * @param  LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $filter = [
            'id' => $identifier,
            'st' => User::ACTIVE_STATUS
        ];
        return $this->dm->getRepository(User::class)->findOneBy($filter);
        // optimization
//        $qb = $this->dm->createQueryBuilder(User::class);
//        $qb->select(['un', 'pw', 'to']);
//        $qb->field('_id')->equals($identifier);
//        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $filter = [
            'id' => $identifier,
            'token' => $token,
            'st' => User::ACTIVE_STATUS
        ];
        return $this->dm->getRepository(User::class)->findOneBy($filter);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);
        $this->dm->persist($user);
        $this->dm->flush();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // retrieve user entity by email|username
        $qb = $this->dm->createQueryBuilder(User::class);
        //optimization
        //$qb->select(['un', 'pw', 'to']);
        foreach ($credentials as $key => $value) {
            if ( ! Str::contains($key, 'password')) {
                $qb->field($key)->equals($value);
            }
        }
        $qb->field('st')->equals(User::ACTIVE_STATUS);
        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        return $user->getAuthPassword() == $credentials['password'];
    }
}
