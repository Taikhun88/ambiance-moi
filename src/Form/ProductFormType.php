<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    // To be able to import the whole object Category in a list form
    private $categories;
    public function __construct()
    {
        $this->categories = new ProductCategory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('image_front', FileType::class, [
                'label' => 'Image de face'
            ])
            ->add('image_profile', FileType::class, [
                'label' => 'Image de profil'
            ])
            ->add('image_top', FileType::class, [
                'label' => 'Image d\'en haut'
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'Poids',
                'attr' => [
                    'min' => 0,
                    'max' => 40,
                ]
            ])
            ->add('volume', IntegerType::class, [
                'label' => 'Volume',
                'attr' => [
                    'min' => 0,
                    'max' => 5000,
                ]
            ])
            ->add('height', IntegerType::class, [
                'label' => 'Hauteur',
                'attr' => [
                    'min' => 0,
                    'max' => 5000,
                ]
            ])
            ->add('length', IntegerType::class, [
                'label' => 'Longueur',
                'attr' => [
                    'min' => 0,
                    'max' => 5000,
                ]
            ])
            // ->add('created_at')
            // the specific formtype will be put by default with null as 2nd parameter
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'mapped' => false,
                // https://nouvelle-techno.fr/questions/details/Entity-of-type-Doctrine-Common-Collections-ArrayCollection-passed-to-the-choice-field-must-be-managed-Maybe-you-forget-to-persist-it-in-the-entity-manager
                'class' => ProductCategory::class,
                'multiple' => true,
                'choices' => $this->categories->getName(),
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
