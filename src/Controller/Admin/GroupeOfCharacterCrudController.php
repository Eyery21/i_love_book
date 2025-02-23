<?php
namespace App\Controller\Admin;

use App\Entity\GroupeOfCharacter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class GroupeOfCharacterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupeOfCharacter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('name', 'Group Name'),

            TextEditorField::new('description', 'Description')
                ->hideOnIndex(),

            DateField::new('fonded_at', 'Founded At')
                ->setFormat('dd-MM-yyyy'),

            ChoiceField::new('alignment', 'Alignment')
                ->setChoices([
                    'Heroic' => 'Heroic',
                    'Villainous' => 'Villainous',
                    'Neutral' => 'Neutral',
                ])
                ->allowMultipleChoices(false)
                ->renderExpanded(false),

            TextField::new('headquarters', 'Headquarters')
                ->hideOnIndex(),

            ImageField::new('image', 'Image')
                ->setBasePath('/uploads/group_images')
                ->setUploadDir('public/uploads/group_images')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),

            AssociationField::new('members', 'Members')
                ->autocomplete()
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex(),

            AssociationField::new('rival_groups', 'Rival Groups')
                ->autocomplete()
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex(),

            DateTimeField::new('created_at', 'Created At')
                ->hideOnForm(),

            DateTimeField::new('updated_at', 'Updated At')
                ->hideOnForm(),
        ];
    }
}
