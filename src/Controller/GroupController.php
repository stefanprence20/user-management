<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Group Controller
 *
 * @Route("/api/v1/groups", name="api_v1_groups_")
 */
class GroupController extends AbstractFOSRestController
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Create a group if authenticated user has role ROLE_ADMIN
     *
     * @Route(
     *     "/",
     *     methods={"POST"},
     *     name="group.create"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent());

        $group = $this->groupRepository->findOneBy([
            'name' => $data->name,
        ]);

        if (!is_null($group)) {
            return $this->handleView($this->view([
                'message' => 'Group already exists!'
            ], Response::HTTP_CONFLICT));
        }
        $group = $this->groupRepository->create($data);

        return $this->handleView($this->view($group, Response::HTTP_CREATED));
    }

    /**
     * Delete a group if it doesn't have related users and authenticated user has role ROLE_ADMIN
     *
     * @Route(
     *     "/{id}",
     *     methods={"DELETE"},
     *     name="group.delete"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $group = $this->groupRepository->find($id);

        if (is_null($group)) {
            return $this->handleView($this->view([
                'message' => 'User do not exists!'
            ], Response::HTTP_CONFLICT));
        }

        if($group->hasUsers()) {
            return $this->handleView($this->view([
                'message' => 'Group has related users!'
            ], Response::HTTP_CONFLICT));
        }
        $result = $this->groupRepository->delete($group);

        return $this->handleView($this->view($result, Response::HTTP_OK));
    }

    /**
     * Get group data
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="group.show"
     * )
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $group = $this->groupRepository->find($id);

        if (is_null($group)) {
            return $this->handleView($this->view([
                'message' => 'Group do not exists!'
            ], Response::HTTP_CONFLICT));
        }

        return $this->handleView($this->view($group, Response::HTTP_OK));
    }
}