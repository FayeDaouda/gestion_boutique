<?php
// src/Controller/MenuController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu", name="menu")
     * @IsGranted("ROLE_USER")  // Sécurise l'accès à la route, les utilisateurs doivent être connectés
     */
    public function index(): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $menu = [];

        if ($user) {
            $roles = $user->getRoles();
            $role = $roles[0]; // On prend le premier rôle de l'utilisateur

            // Menu en fonction du rôle
            if ($role === 'ROLE_BOUTIQUIER') {
                $menu = [
                    'Gestion' => ['Dashboard', 'Clients', 'Dettes', 'Articles', 'Demandes', 'Utilisateurs'],
                    'Boutique' => ['Produits', 'Commandes'],
                ];
            } elseif ($role === 'ROLE_VENDEUR') {
                $menu = [
                    'Boutique' => ['Dettes', 'Clients'],
                ];
            } elseif ($role === 'ROLE_ADMIN') {
                $menu = [
                    'Administration' => ['Utilisateurs', 'Rôles', 'Paramètres'],
                    'Gestion' => ['Clients', 'Dettes', 'Articles'],
                ];
            } else {
                // Menu par défaut pour les utilisateurs sans rôle spécifique
                $menu = [
                    'Accueil' => ['Voir les produits'],
                ];
            }
        }

        // Retourner le menu à la vue Twig
        return $this->render('menu/index.html.twig', [
            'menu' => $menu,
        ]);
    }
}
