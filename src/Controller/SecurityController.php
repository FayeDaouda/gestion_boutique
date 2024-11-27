<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        // Gérer l'affichage de la page de connexion
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // Logout sera géré automatiquement par Symfony
    }
}
