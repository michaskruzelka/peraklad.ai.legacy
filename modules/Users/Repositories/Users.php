<?php

namespace Modules\Users\Repositories;

use Doctrine\ODM\MongoDB\DocumentRepository;

class Users extends DocumentRepository
{
    /**
     * @param string $username
     * @return bool
     */
    public function checkByUsername($username)
    {
        return !! $this->createQueryBuilder()
            ->select()
            ->field('_id')
            ->equals($username)
            ->getQuery()
            ->execute()
            ->count()
        ;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function checkByEmail($email)
    {
        return !! $this->createQueryBuilder()
            ->select('em')
            ->field('em')
            ->equals($email)
            ->getQuery()
            ->execute()
            ->count()
        ;
    }

    /**
     * @param bool $hydrate
     * @param array|bool $userIds
     * @return array
     */
    public function getAllBasic($hydrate = false, $userIds = false)
    {
        $qb = $this->createQueryBuilder()->hydrate($hydrate)->select(['avatar.fn', 'st']);
        if ($userIds) {
            $qb->field('_id')->in( (array) $userIds);
        }
        return $qb->getQuery()->execute();
    }
}