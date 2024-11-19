<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Dette;
use App\Entity\Client;
class DebtController extends AbstractController
{
    #[Route('/client/{id}/ajouter-dette', name: 'add_dette', methods: ['POST'])]
    public function addDette(Request $request, Client $client, EntityManagerInterface $entityManager): RedirectResponse
    {
        $montant = $request->request->get('montant');
        $montantVerser = $request->request->get('montantVerser', 0);
    
        if ($montant === null) {
            return new JsonResponse(['error' => 'Le montant est obligatoire'], 400);
        }
    
        $dette = new Dette();

        // Assurer que la date est définie
        $dette->setDate(new \DateTime());  // Assigner la date actuelle (date du jour)
        $dette->setMontant($montant)
              ->setMontantVerser($montantVerser)
              ->setClient($client);
    
        $entityManager->persist($dette);
        $entityManager->flush();

        // Rediriger vers la page de détails du client avec les informations mises à jour
        return $this->redirectToRoute('client_show', ['id' => $client->getId()]);
    }
}

