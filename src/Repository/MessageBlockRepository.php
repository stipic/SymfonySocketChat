<?php

namespace App\Repository;

use App\Entity\MessageBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
/**
 * @method MessageBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageBlock[]    findAll()
 * @method MessageBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageBlock::class);
    }
}
