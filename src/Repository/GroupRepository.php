<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * Create the group
     *
     * @param $data
     * @return Group|array
     */
    public function create($data)
    {
        $group = new Group();
        $group->setName($data->name);
        $group->setCreatedAt(new \DateTime('now'));

        try {
            $this->_em->persist($group);
            $this->_em->flush();
        } catch (\Exception $e ) {
            return [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return $group;
    }

    /**
     * Remove the group
     *
     * @param Group $group
     * @return array
     */
    public function delete(Group $group): array
    {
        try {
            $this->_em->persist($group);
            $this->_em->flush();
        } catch (\Exception $e ) {
            return [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return ['message' => 'Group deleted successfully!'];
    }
}
