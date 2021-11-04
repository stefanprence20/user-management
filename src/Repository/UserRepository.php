<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Create the user
     *
     * @param $data
     * @return array|User
     */
    public function create($data)
    {
        $user = new User();
        $user->setFullName($data->full_name);
        $user->setEmail($data->email);
        $user->setRoles(['ROLE_USER']);
        $password = $this->passwordEncoder->encodePassword($user, $data->password);
        $user->setPassword($password);

        try {
            $this->_em->persist($user);
            $this->_em->flush();
        } catch (\Exception $e ) {
            return [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return $user;
    }

    /**
     * Remove the user
     *
     * @param User $user
     * @return array
     */
    public function delete(User $user): array
    {
        try {
            $this->_em->persist($user);
            $this->_em->flush();
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return ['message' => 'User deleted successfully!'];
    }

    /**
     * Attach group to specified user
     *
     * @param User $user
     * @param Group $group
     * @return User|array
     */
    public function attachGroup(User $user, Group $group)
    {
        $user->addGroup($group);

        try {
            $this->_em->persist($user);
            $this->_em->flush();
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return $user;
    }

    /**
     * Detach group from specified user
     *
     * @param User $user
     * @param Group $group
     * @return User|array
     */
    public function detachGroup(User $user,Group $group)
    {
        $user->removeGroup($group);

        try {
            $this->_em->persist($user);
            $this->_em->flush();
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return $user;
    }
}
