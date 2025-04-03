<?php

namespace App\Repository;

use App\Entity\Notes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Notes>
 *
 * @method Notes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notes[]    findAll()
 * @method Notes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotesRepository extends ServiceEntityRepository
{
    private $em;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($registry, Notes::class);
    }

    public function add(Notes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function create_note() {
        $connection = $this->em->getConnection();
        
        $requete = "INSERT INTO Notes (titre, description) VALUES (:titre, :description)";
        $statement = $connection->prepare($requete);
        $statement->execute([
                ':titre' => null,
                ':description' => null
            ]);
        $dernier_id = $connection->lastInsertId();
        return $dernier_id;
    }

    public function update_note($id, $type, $text) {
        $connection = $this->em->getConnection();
    
        if($type === 'input') {
            $sql = "UPDATE Notes SET titre = :text WHERE id = :id";
        }else if($type=== 'textarea') {
            $sql = "UPDATE Notes SET description = :text WHERE id = :id";
        }
        $statement = $connection->prepare($sql);
        $statement->execute([
            'text' => $text,
            'id' => $id
        ]);
  
    }

    function all_notes() {
        $connection = $this->em->getConnection();
        $requete = "select * from Notes order by id desc";
        $statement = $connection->executeQuery($requete);
        return  $statement->fetchAllAssociative();
    }
//    /**
//     * @return Notes[] Returns an array of Notes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notes
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
