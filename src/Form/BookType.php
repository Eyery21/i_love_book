<?php

namespace App\Form;

use App\Entity\Book;
use App\Form\BookCategory;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'Sous-titre',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('parution', DateType::class, [
                'label' => 'Date de parution',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('posseded', CheckboxType::class, [
                'required' => false,
                'label' => 'Possédé',
            ])
            ->add('Collection', ChoiceType::class, [
                'choices' => [
                    'DC Classique' => BookCategory::DC_CLASSIQUE->value,
                    'DC Essentiels' => BookCategory::DC_ESSENTIELS->value,
                    'DC Renaissance' => BookCategory::DC_RENAISSANCE->value,
                    'DC Rebirth' => BookCategory::DC_REBIRTH->value,
                    'DC Black Label' => BookCategory::DC_BLACK_LABEL->value,
                ],
                'label' => 'Collection',
                'expanded' => false, // Affichage sous forme de menu déroulant
                'multiple' => false, // Un seul choix possible
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image (JPEG/PNG)',
                'mapped' => false, // Important pour éviter un problème de mappage avec l'entité
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
                ],
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'required' => false,
            ])
            ->add('illustrator', TextType::class, [
                'label' => 'Illustrateur',
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'required' => false,
            ])
            ->add('page_count', IntegerType::class, [
                'label' => 'Nombre de pages',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
