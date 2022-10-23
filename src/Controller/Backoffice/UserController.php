<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\Type\UserFormType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

// #[IsGranted('ROLE_ADMIN')]
#[Route('/backoffice/user', name: 'backoffice_user_', requirements: ['id' => '\d+'])]
class UserController extends AbstractController
{
    #[Route('/', name: 'index', methods:'GET')]
    public function getUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name:'new', methods:['GET', 'POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new DateTimeImmutable());
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouvel utilisateur a bien été créé');

            return $this->redirectToRoute('backoffice_user_index');
        }

        return $this->renderForm('backoffice/user/new.html.twig', [
            'form' => $form,
        ]);
    
        //    return $this->render('backoffice/user/create-user.html.twig');
        // TODO créer les méthodes show, edit et delete. Vérifier le fonctionnement des addFlash
    }
}
