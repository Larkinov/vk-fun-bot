<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Conversation;
use App\Domain\Repository\ProfileRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository implements ProfileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function getAdminId(int $peerId):?int{
        return $this->createQueryBuilder('c')
        ->select('u.admin_id')
        ->where('u.peer_id = :peer_id')
        ->setParameter('peer_id',$peerId)
        ->getQuery()
        ->getSingleScalarResult();
    }

}
