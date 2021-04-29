<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findNumberOfUnreadedMessages($userid, $conversationid)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT 
            COUNT(mb.conversation) as count, 
            c.id as conversationId, 
            c.created_by as conversationCreatedBy
        FROM user_message as um 
        INNER JOIN 
            users as u ON um.user_id=u.id AND u.id=:userid
        INNER JOIN 
            messages as m ON um.message_id=m.id
        INNER JOIN 
            message_blocks as mb ON m.messageBlock=mb.id AND mb.conversation=:conversationid
        INNER JOIN 
            conversations as c ON c.id=mb.conversation
        GROUP BY(mb.conversation);';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['userid' => $userid, 'conversationid' => $conversationid]);

        return $stmt->fetchAll();
    }

    public function findByRole(array $roles, array $exludeUsers = array())
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $roleif = ''; 
        $i = 0;
        $roleParams = [];
        foreach($roles as $role)
        {
            $roleParams['role_' . $i] = $role;
            $roleif .= ($i != 0 ? ' OR ' : '') . 'g.role=:role_' . $i . '';

            $i ++;
        }

        $userIf = '';
        $i = 0;
        foreach($exludeUsers as $singleUser)
        {
            $roleParams['user_exclude_' . $i] = $singleUser->getId();
            $userIf .= ($i != 0 ? ' AND ' : '') . 'u.id!=:user_exclude_' . $i . '';

            $i ++;
        }

        $sql = '
        SELECT 
            u.*
        FROM groups as g
        INNER JOIN 
            user_group as ug ON ug.group_id=g.id ' . (!empty($roleif) ? "AND (" . $roleif . ")" : "") .
        'INNER JOIN
            users as u ON u.id=ug.user_id '. (!empty($userIf) ? "AND (" . $userIf . ")" : "");

        $stmt = $conn->prepare($sql);
        $stmt->execute($roleParams);

        $userIds = [];
        $usersWithAccessToRole = $stmt->fetchAll();
        foreach($usersWithAccessToRole as $user)
        {
            $userIds[] = $user['id'];
        }

        return $this->findBy([
            'id' => $userIds
        ]);
    }
}
