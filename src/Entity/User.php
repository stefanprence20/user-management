<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     routePrefix="/v1",
 *     collectionOperations={"post"={"path"="/users/"}},
 *     itemOperations={
 *          "get",
 *          "delete",
 *          "post_login"={
 *              "method"="POST",
 *              "path"="/login",
 *              "controller"="UserConcroller::class",
 *              "openapi_context"={
 *                  "requestBody": {
 *                     "content": {
 *                         "application/json": {
 *                             "schema": {
 *                                 "type": "object",
 *                                 "properties": {
 *                                     "email": {"type": "string", "example": "admin@example.com"},
 *                                     "password": {"type": "string", "example": "pass_1234"},
 *                                 },
 *                             },
 *                         },
 *                     },
 *                  },
 *                  "summary"="Login user",
 *                  "description"="Login user and get jwt token",
 *              },
 *          },
 *          "post_attach"={
 *              "method"="POST",
 *              "path"="/users/{id}/attach-group",
 *              "controller"="UserConcroller::class",
 *              "openapi_context"={
 *                  "requestBody": {
 *                     "content": {
 *                         "application/json": {
 *                             "schema": {
 *                                 "type": "object",
 *                                 "properties": {
 *                                     "group_id": {"type": "integer", "example": "1"},
 *                                 },
 *                             },
 *                         },
 *                     },
 *                  },
 *                  "summary"="Attach group to the user",
 *                  "description"="Attach group to the user",
 *              },
 *          },
 *          "delete_detach"={
 *              "method"="DELETE",
 *              "path"="/users/{id}/detach-group",
 *              "controller"="UserConcroller::class",
 *              "openapi_context"={
 *                  "requestBody": {
 *                     "content": {
 *                         "application/json": {
 *                             "schema": {
 *                                 "type": "object",
 *                                 "properties": {
 *                                     "group_id": {"type": "integer", "example": "1"},
 *                                 },
 *                             },
 *                         },
 *                     },
 *                  },
 *                  "summary"="Detach group from the user",
 *                  "description"="Detach group from the user",
 *              },
 *          }
 *     },
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups("read")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups("write")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, columnDefinition="full_name")
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     */
    private $fullName;

    /**
     * Many Users have Many Groups.
     * @ManyToMany(targetEntity="Group", inversedBy="users")
     * @JoinTable(name="user_group")
     * @Groups("read")
     */
    private $groups;

    /**
     * The constructor of User
     */
    public function __construct() {
        $this->groups = new ArrayCollection();
    }

    /**
     * Attach group to user
     * @param Group $group
     */
    public function addGroup(Group $group)
    {
        $this->groups->add($group);
    }

    /**
     * Detach group from user
     * @param Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups Collection
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
//         $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return $this
     */
    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }
}
