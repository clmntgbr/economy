<?php

namespace App\Controller\Api\User;

use App\Util\ResponseBody;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ResponseBody */
    private $responseBody;

    public function __construct(EntityManagerInterface $entityManager, ResponseBody $responseBody)
    {
        $this->entityManager = $entityManager;
        $this->responseBody = $responseBody;
    }

    /**
     * @Rest\Get(path="/user", name="user")
     * @Rest\View
     */
    public function getUserAction(Request $request, Security $security)
    {
        return $this->responseBody->create(Response::HTTP_OK, $security->getUser(), []);
    }
}
