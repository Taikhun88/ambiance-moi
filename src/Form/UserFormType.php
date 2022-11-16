<?php 

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $route = $_SERVER['REQUEST_URI'];
        $regexRoute = preg_match('/\/backoffice\/user\/edit/', $route);
        $avatarFieldsType = $regexRoute == true ? null : FileType::class;
        // dd($avatarFieldsType);

        $builder
            ->add('pseudo', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('avatar', FileType::class, ['mapped' => false]) 
            ->add('save', SubmitType::class)
        ;
        // TODO input pour update image sur page edit user
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}