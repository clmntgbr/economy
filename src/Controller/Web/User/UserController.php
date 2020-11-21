<?php

namespace App\Controller\Web\User;

use App\Form\ChangePasswordFormType;
use App\Util\DotEnv;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/app/user", name="app_")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface */
    private $validator;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var DotEnv */
    private $dotEnv;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, DotEnv $dotEnv)
    {
        $this->entityManager = $entityManager;
        $this->dotEnv = $dotEnv;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/account", name="user_account")
     */
    public function getUserAccountAction(Request $request)
    {
        $user = $this->getUser();

        $resetForm = $this->createForm(ChangePasswordFormType::class);
        $resetForm->handleRequest($request);

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            $encodedPassword = $this->passwordEncoder->encodePassword(
                $user,
                $resetForm->get('plainPassword')->getData()
            );
            $user->setPassword($encodedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Your Password have been changed.');
        }

        if ($resetForm->isSubmitted() && !$resetForm->isValid()) {
            $message = '';
            foreach ($resetForm->getErrors(true) as $error) {
                $message .= sprintf("%s<br>", $error->getMessage());
            }
            $this->addFlash('error', $message);
        }

        return $this->render('user/user_account.html.twig', [
            'resetForm' => $resetForm->createView(),
        ]);
    }
}
