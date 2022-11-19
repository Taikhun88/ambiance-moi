<?php

namespace App\Controller\Backoffice;

use App\Entity\ProductCategory;
use App\Form\Type\ProductCategoryFormType;
use App\Repository\ProductCategoryRepository;
use App\Service\ImageUploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/product/category', name: 'app_backoffice_product_category_', requirements: ['id' => '\d+'])]
class ProductCategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        $listProductsCategories = $productCategoryRepository->findAll();
        // dd($listProductsCategories);

        return $this->render('backoffice/product_category/index.html.twig');
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ImageUploader $imageUploader, EntityManagerInterface $entityManager): Response
    {
        $productCategory = new ProductCategory();
        $date = $productCategory->setCreatedAt(new DateTimeImmutable());

        $form = $this->createForm(ProductCategoryFormType::class, $productCategory);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productCategory = $form->getData();

            // On effectue l'upload du fichier grâce au service ImageUploader
            // Le formType qui fait appel au service ImageUploader est un FileType et non ImageType
            // Dans le formType, FileType est en mapped false pour éviter de check la correspondance des data entre les data des objets et tables
            // ! le second paramètre est le nom de la property de l'entité concernée ici "image"
            $newFilename = $imageUploader->upload($form, 'image', 'images/categories/');

            // on met à jour la propriété image 
            if ($newFilename) {
                $productCategory->setImage(strval($newFilename));
            }

            $entityManager->persist($productCategory);
            $entityManager->flush();

            $this->addFlash('success', 'La nouvelle catégorie de produits a bien été créée');

            return $this->redirectToRoute('app_backoffice_home');
        }

        return $this->renderForm('backoffice/product_category/new.html.twig', [
            'form' => $form,
        ]);
    }
}
