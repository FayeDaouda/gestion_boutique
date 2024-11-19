<?php
// src/Form/DetteType.php
namespace App\Form;

use App\Entity\Dette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('montantVerser', NumberType::class, [
                'label' => 'Montant Versé',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('montantRestant', NumberType::class, [
                'label' => 'Montant Restant',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter Dette',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dette::class,
        ]);
    }
}
