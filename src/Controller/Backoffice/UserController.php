<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\Type\UserFormType;
use App\Repository\UserRepository;
use App\Service\ImageUploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
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
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name:'new', methods:['GET', 'POST'])]
    public function new(Request $request, ImageUploader $imageUploader, EntityManagerInterface $entityManager): Response
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

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouvel utilisateur a bien été créé');

            return $this->redirectToRoute('backoffice_user_index');
        }

        return $this->renderForm('backoffice/user/new.html.twig', [
            'form' => $form,
        ]);
    
        //    return $this->render('backoffice/user/create-user.html.twig');
        // TODO créer les méthodes show, edit et delete.
    }

    #[Route('/edit/{id}', name:'edit', methods:['GET', 'POST'])]
    public function edit(Request $request, User $user, ImageUploader $imageUploader,EntityManagerInterface $entityManager): Response
    {   
        // dd($user);

        $form = $this->createForm(UserFormType::class, $user);
        
        $form->handleRequest($request);

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
}
