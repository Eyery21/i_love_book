<?php

namespace App\Controller\Admin;
use App\Entity\GroupeOfCharacter;
use App\Entity\Series;
use App\Entity\Collections;
use App\Entity\User;
use App\Entity\Book;
use App\Entity\Character;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('I Love Book');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Books', 'fa fa-book', Book::class);
        yield MenuItem::linkToCrud('Character', 'fa fa-book', entityFqcn: Character::class);
        yield MenuItem::linkToCrud('Series', 'fa fa-book', entityFqcn: Series::class);
        yield MenuItem::linkToCrud('groupe','fa fa-book',entityFqcn:GroupeOfCharacter::class);
        yield MenuItem::linkToCrud('collections','fa fa-book',entityFqcn:Collections::class);


    }
}