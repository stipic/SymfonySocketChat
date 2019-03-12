<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
}
