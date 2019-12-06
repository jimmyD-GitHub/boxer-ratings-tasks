<?php

namespace App\Repository;

use DateTime;
use App\Entity\Email;
use Doctrine\ORM\ORMException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    /**
     * @return Email[]
     */
    public function findAllEmailsThatNeedToBeSent(): array
    {
        $entityManager = $this->getEntityManager();

        $dql = 'SELECT e
            FROM App\Entity\Email e
            WHERE e.sent_at IS NULL
            ORDER BY e.created_at ASC';

        return $entityManager->createQuery($dql)
            ->getResult();
    }

    /**
     * @param Email $email
     * @throws ORMException
     */
    public function save(Email $email): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($email);
        $entityManager->flush();
    }

    /**
     * @param Email $email
     * @param DateTime|null $dateTime
     * @throws ORMException
     */
    public function markEmailAsSent(Email $email, DateTime $dateTime = null): void
    {
        $email->setSentAt($dateTime ?? new DateTime());
        $this->save($email);
    }
}
