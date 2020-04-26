<?php
namespace CommandeBundle\Repository;
class CommandeRepository extends Doctrine\ORM\EntityRepository
{


public function findCommande($motcle)
{
    $query=$repository->createQueryBuilder('f')
        ->where('f.numCommande like :numCommande')
        ->setParameter('numCommande', $motcle.'%')
        ->orderBy('f.numCommande','ASC')
        ->getQuery();
    return $query->getresult();

}


}