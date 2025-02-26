<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FloatField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextField::new('subtitle', 'SubTitre'),

            TextEditorField::new('description', 'Description'),
            TextField::new('parution', 'Date de parution'),
            BooleanField::new('posseded', 'Possédé'),
            ImageField::new('image', 'Aperçu de l’image')
                ->setBasePath('/uploads/images')
                ->onlyOnIndex(),
            TextField::new('imageFile', 'Télécharger une image')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            TextField::new('author', 'Auteur'),
            TextField::new('illustrator', 'Illustrateur'),
            MoneyField::new('price', 'Prix')
                ->setCurrency('EUR')
                ->setStoredAsCents(false), // Ajuste si tu utilises des centimes ou des décimales
            IntegerField::new('page_count', 'Nombre de pages'),
            DateTimeField::new('updatedAt', 'Dernière mise à jour')
                ->hideOnForm(),
            AssociationField::new('series', 'Series')
            ->setCrudController(SeriesCrudController::class)
            ->setFormTypeOption('choice_label', 'title'), // Utilise le titre pour l'affichage

            AssociationField::new('characters', 'Characters')
            ->setCrudController(CharacterCrudController::class)
            ->setFormTypeOption('choice_label', 'name')

        ];
    }
}
