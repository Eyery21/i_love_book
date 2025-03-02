<?php

namespace App\Controller\Admin;

use App\Entity\Collections;
use App\Form\BookCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CollectionsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Collections::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom de la collection'),

            ChoiceField::new('name', 'Choix de la collection')
                ->setChoices([
                    'DC Classique' => BookCategory::DC_CLASSIQUE->value,
                    'DC Essentiels' => BookCategory::DC_ESSENTIELS->value,
                    'DC Renaissance' => BookCategory::DC_RENAISSANCE->value,
                    'DC Rebirth' => BookCategory::DC_REBIRTH->value,
                    'DC Black Label' => BookCategory::DC_BLACK_LABEL->value,
                ])
                ->allowMultipleChoices(false) // Une seule valeur possible
                ->renderExpanded(false), // Menu déroulant

            AssociationField::new('books', 'Livres')
                ->setCrudController(BookCrudController::class)
                ->autocomplete()
                ->setFormTypeOptions([
                    'by_reference' => false, // Permet de gérer les ajouts/suppressions d’éléments
                    'multiple' => true, // Permet de sélectionner plusieurs livres
                ]),
        ];
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
