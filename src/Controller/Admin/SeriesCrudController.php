<?php

namespace App\Controller\Admin;

use App\Entity\Series;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SeriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Series::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Title'),
            BooleanField::new('isOneShot', 'Is One Shot'),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            NumberField::new('length', 'Number of Volumes')->hideOnIndex(),
            ImageField::new('image', 'Series Cover')
                ->setBasePath('/uploads/series_images')
                ->setUploadDir('public/uploads/series_images')
                ->setRequired(false),
            TextField::new('imageFile', 'Upload Image')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            AssociationField::new('books', 'Books')
                ->setCrudController(BookCrudController::class)
                ->hideOnForm(),
            
        ];
    }
}
