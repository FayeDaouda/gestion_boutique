<?php
// src/Controller/DebtController.php
namespace App\Controller;

use App\Entity\Dette;
use App\Entity\Client;
use App\Form\DetteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DebtController extends AbstractController
{
    #[Route('/client/{id}/ajouter-dette', name: 'add_dette', methods: ['GET', 'POST'])]
    public function addDette(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de Dette
        $dette = new Dette();
        $dette->setClient($client); // Associer la dette au client

        // Créer le formulaire pour ajouter une dette
        $form = $this->createForm(DetteType::class, $dette);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        // Initialiser les montants à zéro
        $totalMontant = 0;
        $totalMontantVerser = 0;
        $totalMontantRestant = 0;

        // Calculer les montants avant d'ajouter une nouvelle dette
        foreach ($client->getDettes() as $existingDette) {
            $totalMontant += $existingDette->getMontant();
            $totalMontantVerser += $existingDette->getMontantVerser();
            $totalMontantRestant += $existingDette->getMontantRestant();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            // Afficher les données récupérées pour le débogage
            dump($formData); // Affiche l'objet Dette récupéré

            // Afficher des informations spécifiques sur les montants
            dump([
                'Montant' => $formData->getMontant(),
                'Montant Versé' => $formData->getMontantVerser(),
                'Montant Restant' => $formData->getMontantRestant()
            ]);

            // Sauvegarder la dette dans la base de données
            $entityManager->persist($dette);
            $entityManager->flush();

            // Mettre à jour les montants après l'ajout de la nouvelle dette
            $totalMontant += $dette->getMontant();
            $totalMontantVerser += $dette->getMontantVerser();
            $totalMontantRestant += $dette->getMontantRestant();

            // Ajouter un message flash pour confirmer l'ajout
            $this->addFlash('success', 'Dette ajoutée avec succès.');

            // Réafficher la page client mise à jour
            return $this->render('client/show.html.twig', [
                'client' => $client,
                'form' => $form->createView(),
                'totalMontant' => $totalMontant,
                'totalMontantVerser' => $totalMontantVerser,
                'totalMontantRestant' => $totalMontantRestant,
                'dettes' => $client->getDettes(),
            ]);
        }

        // Si le formulaire n'est pas valide ou n'a pas encore été soumis
        if ($form->isSubmitted() && !$form->isValid()) {
            // Ajouter un message flash en cas de formulaire invalide
            $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez vérifier les champs.');
        }

        // Afficher la vue avec le formulaire (si soumis mais invalide ou non soumis)
        return $this->render('client/show.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
            'totalMontant' => $totalMontant,
            'totalMontantVerser' => $totalMontantVerser,
            'totalMontantRestant' => $totalMontantRestant,
            'dettes' => $client->getDettes(),
        ]);
    }
}
