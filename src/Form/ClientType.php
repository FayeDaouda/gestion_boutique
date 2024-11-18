<?php
// src/Form/ClientType.php
namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Lier un utilisateur existant à ce client
            ->add('userAccount', EntityType::class, [
                'class' => User::class, 
                'choice_label' => 'login', // Attribut de l'entité User affiché dans les options
                'required' => false,
                'placeholder' => 'Sélectionner un utilisateur', // Ajout d'un placeholder
            ])
            // Champ "Nom" pour le client
            ->add('surname', TextType::class, [
                'label' => 'Nom',  // Ajouter un label
                'required' => false,
            ])
            // Champ "Téléphone" pour le client
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone', // Ajouter un label
                'required' => false,
            ])
            // Champ "Adresse" pour le client
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',  // Ajouter un label
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
