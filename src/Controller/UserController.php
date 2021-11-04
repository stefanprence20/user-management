<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * User Controller
 *
 * @Route("/api/v1/users", name="api_v1_users_")
 */
class UserController extends AbstractFOSRestController
{

    /** @var UserRepository  */
    private $userRepository;
    /** @var GroupRepository  */
    private $groupRepository;

    public function __construct(UserRepository $userRepository, GroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Create a user if authenticated user has role ROLE_ADMIN
     *
     * @Route(
     *     "/",
     *     methods={"POST"},
     *     name="user.create"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent());

        $user = $this->userRepository->findOneBy([
            'email' => $data->email,
        ]);

        if (!is_null($user)) {
            return $this->handleView($this->view([
                'message' => 'User already exists!'
            ], Response::HTTP_CONFLICT));
        }
        $user = $this->userRepository->create($data);

        return $this->handleView($this->view($user, Response::HTTP_CREATED));
    }

    /**
     * Delete a user if authenticated user has role ROLE_ADMIN
     *
     * @Route(
     *     "/{id}",
     *     methods={"DELETE"},
     *     name="user.delete"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $user = $this->userRepository->find($id);

        if (is_null($user)) {
            return $this->handleView($this->view([
                'message' => 'User do not exists!'
            ], Response::HTTP_CONFLICT));
        }
        $result = $this->userRepository->delete($user);

        return $this->handleView($this->view($result, Response::HTTP_OK));
    }

    /**
     * Attach group to the user
     *
     * @Route(
     *     "/{id}/attach-group",
     *     methods={"POST"},
     *     name="user.attach_group"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function attachGroup($id, Request $request): Response
    {
        $data = json_decode($request->getContent());

        $user = $this->userRepository->find($id);

        if (is_null($user)) {
            return $this->handleView($this->view([
                'message' => 'User do not exists!'
            ], Response::HTTP_CONFLICT));
        }

        $group = $this->groupRepository->find($data->group_id);

        if($user->getGroups()->contains($group)){
            return $this->handleView($this->view([
                'message' => 'User already exist in this group!'
            ], Response::HTTP_CONFLICT));
        }

        if (is_null($group)) {
            return $this->handleView($this->view([
                'message' => 'Group do not exists!'
            ], Response::HTTP_CONFLICT));
        }

        $response = $this->userRepository->attachGroup($user, $group);

        return $this->handleView($this->view($response, Response::HTTP_OK));
    }

    /**
     * Detach group from the user
     *
     * @Route(
     *     "/{id}/detach-group",
     *     methods={"DELETE"},
     *     name="user.detach_group"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function detachGroup($id, Request $request): Response
    {
        $data = json_decode($request->getContent());

        $user = $this->userRepository->find($id);

        if (is_null($user)) {
            return $this->handleView($this->view([
                'message' => 'User do not exists!'
            ], Response::HTTP_CONFLICT));
        }

        $group = $this->groupRepository->find($data->group_id);

        if(!$user->getGroups()->contains($group)){
            return $this->handleView($this->view([
                'message' => 'User do not exist in this group!'
            ], Response::HTTP_CONFLICT));
        }

        if (is_null($group)) {
            return $this->handleView($this->view([
                'message' => 'Group do not exists!'
            ], Response::HTTP_CONFLICT));
        }

        $response = $this->userRepository->detachGroup($user, $group);

        return $this->handleView($this->view($response, Response::HTTP_OK));
    }

    /**
     * Get user data
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="user.show"
     * )
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if (is_null($user)) {
            return $this->view([
                'message' => 'User do not exists!'
            ], Response::HTTP_CONFLICT);
        }

        return $this->handleView($this->view($user, Response::HTTP_OK));
    }


}