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
            AssociationField::new('collectionsList')
                ->setCrudController(CollectionsCrudController::class)
                ->setLabel('Collections')
                ->setFormTypeOptions([
                    'choice_label' => 'name',
                    'by_reference' => false,
                    'multiple' => true
                ])
                ->formatValue(function ($value, $entity) {
                    $collections = $entity->getCollectionsList();
                    if ($collections && $collections->count() > 0) {
                        // Créer une liste des noms de collections
                        $names = [];
                        foreach ($collections as $collection) {
                            $names[] = $collection->getName();
                        }
                        return implode(', ', $names);
                    }
                    return 'Aucune collection';
                }),
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

            AssociationField::new('previousSeries')
                ->setCrudController(SeriesCrudController::class)
                ->setLabel('Série précédente')
                ->setFormTypeOptions([
                    'choice_label' => 'title'
                ]),

            // Ce champ doit être visible uniquement en mode détail (pas en édition)
            // car on ne peut pas éditer directement la collection de nextSeries
            AssociationField::new('nextSeries')
                ->setCrudController(SeriesCrudController::class)
                ->setLabel('Séries suivantes')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    $nextSeries = $entity->getNextSeries();
                    if ($nextSeries->count() === 0) {
                        return 'Aucune';
                    }

                    $titles = [];
                    foreach ($nextSeries as $series) {
                        $titles[] = $series->getTitle();
                    }

                    return implode(', ', $titles);
                })

        ];
    }
}
