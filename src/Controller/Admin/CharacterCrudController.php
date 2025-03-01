<?php

namespace App\Controller\Admin;

use App\Entity\Character;
use App\Entity\GroupeOfCharacter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CharacterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Character::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            
            // Champs texte
            TextField::new('name', 'Nom'),
            TextField::new('realName', 'Nom Réel'),
            TextField::new('alias', 'Alias')->hideOnIndex(),
            TextField::new('alignment', 'Alignement')->hideOnIndex(),
            TextField::new('espece', 'Espèce')->hideOnIndex(),
            TextField::new('baseOfOperations', 'Base d’Opérations')->hideOnIndex(),
            TextField::new('nemesys', 'Némésis')->hideOnIndex(),
            TextField::new('occupation', 'Occupation')->hideOnIndex(),
            
            // Relation avec GroupeOfCharacter
            AssociationField::new('groupeOfCharacters', 'Groupes Associés')
                ->setCrudController(GroupeOfCharacterCrudController::class)
                ->setFormTypeOption('choice_label', 'name') // Utilise le champ `name` de GroupeOfCharacter pour l'affichage
                ->hideOnIndex(),

            // Champs texte étendus
            TextEditorField::new('originStory', 'Histoire d’Origine')->hideOnIndex(),

            AssociationField::new('books', 'Book')
            ->setCrudController(BookCrudController::class)
            ->setFormTypeOption('choice_label', 'displayName'),

            ArrayField::new('power', 'Pouvoirs')->hideOnIndex(),
            ArrayField::new('equipement', 'Équipements')->hideOnIndex(),

            // Gestion des images (background_page)
            ImageField::new('background_page', 'Aperçu de l’image de fond')
                ->setBasePath('/uploads/images')
                ->onlyOnIndex(),
            TextField::new('backgroundPageFile', 'Télécharger une image de fond')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),

            // Gestion des images (background_navbar)
            ImageField::new('background_navbar', 'Aperçu de l’image de la barre de navigation')
                ->setBasePath('/uploads/images')
                ->onlyOnIndex(),
            TextField::new('backgroundNavbarFile', 'Télécharger une image pour la barre de navigation')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),

            
        ];

    }
}
