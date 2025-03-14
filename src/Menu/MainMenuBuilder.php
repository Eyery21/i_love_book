<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class MainMenuBuilder
{
    private $factory;
    private $router;
    private $requestStack;

    public function __construct(FactoryInterface $factory, RouterInterface $router, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        // Obtenir la route actuelle
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');

        $menu->addChild('Accueil', [
            'route' => 'app_home',
            'label' => 'Accueil',
            'attributes' => [
                'class' => 'menu-item',
            ],
            'display' => $currentRoute !== 'app_home', 
        ]);

        $menu->addChild('Book', [
            'route' => 'app_book_index',
            'label' => 'Book',
            'attributes' => [
                'class' => 'menu-item',
            ],
            'display' => $currentRoute !== 'app_book_index',
        ]);

        $menu->addChild('Contact', [
            'route' => 'app_contact',
            'label' => 'Contact',
            'attributes' => [
                'class' => 'menu-item',
            ],
            'display' => $currentRoute !== 'app_contact', 
        ]);
        $menu->addChild('Connexion', [
            'route' => 'app_login',
            'label' => 'Connexion',
            'attributes' => [
                'class' => 'menu-item',
            ],
            'display' => $currentRoute !== 'app_login', 
        ]);
        $menu->addChild('Inscription', [
            'route' => 'app_register',
            'label' => 'Inscription',
            'attributes' => [
                'class' => 'menu-item',
            ],
            'display' => $currentRoute !== 'app_register', 
        ]);

        $menu->addChild('Profile', [
            'route' => 'app_profile_index',
            'label' => 'Profile',
            'attributes' => [
                'class' => 'menu-item',
            ],
            'display' => $currentRoute !== 'app_profile_index',
        ]);

        return $menu;
    }
}
