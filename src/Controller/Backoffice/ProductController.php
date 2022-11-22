<?php

namespace App\Controller\Backoffice;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backoffice/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_backoffice_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        
        return $this->render('backoffice/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backoffice_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Une relation ManyToMany entre entity Product et ProductCategory
            // Donc pas de champ catégory dans Product en BDD
            // Les relations sont générées dans la table RELATIONNELLE product_productCategory
            // Manuellement ajouter le get Data pour les relations
            $listCategories = $form->get('category')->getData();
            foreach ($listCategories as $key => $value) {
                $product->addCategory($value);
            }

            $product->setCreatedAt(new DateTimeImmutable());    

            $productRepository->add($product, true);

            return $this->redirectToRoute('app_backoffice_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backoffice_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('backoffice/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backoffice_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // données soumises 
            $listCategories = $form->get('category')->getData();

            // données en bdd
            $initialList = $product->getCategory();
            foreach ($initialList as $initialKey => $initialValue) {
                // condition qui compare l'objet soumis avec tous les objets en bdd un par un
                if ($initialValue != array_values($listCategories->getValues())) {
                    // getValues permet de récupérer les data d'une Collection
                    // retire l'objet en bdd si différente de celle soumise
                    $product->removeCategory($initialValue);
                }
            }

            foreach ($listCategories as $key => $value) {
                if ($value != array_values($initialList->getValues())) {
                    // ajoute le nouvel objet
                    $product->addCategory($value);
                }
            }

            $productRepository->add($product, true);

            return $this->redirectToRoute('app_backoffice_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backoffice_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_backoffice_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
