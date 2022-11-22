<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\Type\UserFormType;
use App\Repository\UserRepository;
use App\Service\ImageUploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// #[IsGranted('ROLE_ADMIN')]
#[Route('/backoffice/user', name: 'backoffice_user_', requirements: ['id' => '\d+'])]
class UserController extends AbstractController
{
    #[Route('/', name: 'index', methods:'GET')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name:'new', methods:['GET', 'POST'])]
    public function new(Request $request, ImageUploader $imageUploader, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new DateTimeImmutable());
            $user = $form->getData();

            // On effectue l'upload du fichier grâce au service ImageUploader
            $newFilename = $imageUploader->upload($form, 'avatar', 'images/avatars/');

            // on met à jour la propriété image 
            if ($newFilename) {
                $user->setAvatar(strval($newFilename));
            }

            // Backoffice user allows to create user password and hash it by using the UserPasswordHasherInterface
            // Therefore, form gets password property data with the get method
            // Password input not needed in backoffice as the user only should be able to reset it through the front interface. See Reset password documentation
            // https://symfony.com/doc/current/security/passwords.html
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData() == null || "" ? "123456" : $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouvel utilisateur a bien été créé');

            return $this->redirectToRoute('backoffice_user_index');
        }

        return $this->renderForm('backoffice/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name:'edit', methods:['GET', 'POST'])]
    public function edit(Request $request, User $user, ImageUploader $imageUploader, EntityManagerInterface $entityManager): Response
    {   
        $form = $this->createForm(UserFormType::class, $user);
        
        $form->handleRequest($request);

        $this->denyAccessUnlessGranted('ROLE_USER', $user, 'Vous n\'avez pas les droits pour modifier cet utilisateur');

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            
            // On effectue l'upload du fichier grâce au service ImageUploader
            $newFilename = $imageUploader->upload($form, 'avatar', 'images/avatars/');
            
            // on met à jour la propriété image 
            if ($newFilename) {
                $user->setAvatar(strval($newFilename));
            }            
            
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $user->getPseudo() . ' bien été mis à jour');

            return $this->redirectToRoute('backoffice_user_index');
        }

        return $this->renderForm('backoffice/user/edit.html.twig', [
            'form' => $form,
        ]);
    }

    // Pas d'appel de méthode pour le bon fonctionnement de la méthode Delete
    #[Route('/delete/{id}', name:'delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {   
        // check if logged user has the required role to proceed with the action
        $this->denyAccessUnlessGranted('ROLE_USER', $user, 'Vous n\'avez pas les droits pour supprimer cet utilisateur');

        // Checks if the token (id of current user) submitted through the form delete is valid
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Delete then updates the data with the method remove and flush of the EntityManagerInterface
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('danger', 'Cet utilisateur a bien été supprimé');
        }

        return $this->redirectToRoute('backoffice_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
