<?php
namespace App\Controller;

use App\Entity\Client;

use App\Form\ClientType; 
use App\Entity\User;  // Importez correctement la classe User
use App\Entity\Dette;
use App\Form\DetteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    
    // Méthode pour lister les clients et leurs dettes
#[Route('/clients', name: 'client_list', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    // Récupérer la liste des clients
    $clients = $entityManager->getRepository(Client::class)->findAll();

    // Récupérer la liste des utilisateurs
    $users = $entityManager->getRepository(User::class)->findAll(); // Ajoutez cette ligne

    // Préparer un tableau pour stocker les clients avec le montant dû
    $clientsWithDebt = [];

    foreach ($clients as $client) {
        // Calculer le montant total restant pour chaque client
        $totalMontantRestant = $client->getTotalMontantRestant();  // Utilise la méthode définie dans Client.php
        
        // Ajouter le montant dû au tableau avec le client
        $clientsWithDebt[] = [
            'client' => $client,
            'montantRestant' => $totalMontantRestant
        ];
    }

    return $this->render('client/index.html.twig', [
        'clientsWithDebt' => $clientsWithDebt,
        'users' => $users, // Assurez-vous de passer la variable 'users' à la vue
    ]);
}

#[Route('/client/create', name: 'client_create', methods: ['POST'])]
public function create(Request $request, EntityManagerInterface $entityManager): Response
{
    $formData = $request->request->all();
    $surname = $formData['surname'] ?? null;
    $telephone = $formData['telephone'] ?? null;
    $adresse = $formData['adresse'] ?? null;
    $userAccountId = $formData['user_account_id'] ?? null;

    // Vérifier que les champs obligatoires sont présents
    if (!$surname || !$telephone) {

        $this->addFlash('error', 'Les champs Nom et Téléphone sont obligatoires.');
        return $this->redirectToRoute('client_create');
    }

    // Vérification d'unicité pour le nom et le téléphone
    $existingSurname = $entityManager->getRepository(Client::class)->findOneBy(['surname' => $surname]);
    $existingTelephone = $entityManager->getRepository(Client::class)->findOneBy(['telephone' => $telephone]);

    if ($existingSurname) {
        $this->addFlash('error', 'Un client avec ce nom existe déjà.');
        return $this->redirectToRoute('client_create');
        
    }

    if ($existingTelephone) {
        $this->addFlash('error', 'Un client avec ce téléphone existe déjà.');
        return $this->redirectToRoute('client_create');
        
    }

    // Créer et remplir l'entité Client
    $client = new Client();
    $client->setSurname($surname);
    $client->setTelephone($telephone);
    $client->setAdresse($adresse);

    if ($userAccountId) {
        $userAccount = $entityManager->getRepository(User::class)->find($userAccountId);
        if ($userAccount) {
            $client->setUserAccount($userAccount);
        } else {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('client_create');
        }
    }

    // Sauvegarde dans la base de données
    $entityManager->persist($client);
    $entityManager->flush();

    return $this->redirectToRoute('client_show', ['id' => $client->getId()]);
}



    // Méthode pour afficher un client spécifique avec ses dettes
    #[Route('/client/{id}', name: 'client_show', methods: ['GET', 'POST'])]
    public function show($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le client
        $client = $entityManager->getRepository(Client::class)->find($id);

        // Récupérer la valeur du filtre
        $filter = $request->query->get('filter', 'all');  // Valeur par défaut : 'all'

        // Appliquer le filtre sur les dettes
        $queryBuilder = $entityManager->getRepository(Dette::class)->createQueryBuilder('d')
            ->where('d.client = :client')
            ->setParameter('client', $client);

        // Filtrer en fonction du statut
        if ($filter === 'solder') {
            $queryBuilder->andWhere('d.montant = d.montantVerser');
        } elseif ($filter === 'non_solder') {
            $queryBuilder->andWhere('d.montant != d.montantVerser');
        }

        // Exécuter la requête
        $dettes = $queryBuilder->getQuery()->getResult();

        // Calculer les montants totaux
        $totalMontant = 0;
        $totalMontantVerser = 0;
        $totalMontantRestant = 0;

        foreach ($dettes as $dette) {
            $totalMontant += $dette->getMontant();
            $totalMontantVerser += $dette->getMontantVerser();
            $totalMontantRestant += ($dette->getMontant() - $dette->getMontantVerser());
        }

        // Retourner la vue avec les dettes filtrées
        return $this->render('client/show.html.twig', [
            'client' => $client,
            'dettes' => $dettes,
            'totalMontant' => $totalMontant,
            'totalMontantVerser' => $totalMontantVerser,
            'totalMontantRestant' => $totalMontantRestant,
        ]);
    }

    // Méthode pour afficher les détails d'un client
    #[Route('/client/{id}/details', name: 'client_details')]
    public function clientDetails(Client $client): Response
    {
        // Récupérer toutes les dettes du client
        $dettes = $this->getDoctrine()
            ->getRepository(Dette::class)
            ->findBy(['client' => $client]);

        // Calculer le total des montants, des montants versés, et des montants restants
        $totalMontant = 0;
        $totalMontantVerser = 0;
        $totalMontantRestant = 0;

        foreach ($dettes as $dette) {
            $totalMontant += $dette->getMontant();
            $totalMontantVerser += $dette->getMontantVerser();
            $totalMontantRestant += ($dette->getMontant() - $dette->getMontantVerser());
        }

        return $this->render('client/details.html.twig', [
            'client' => $client,
            'dettes' => $dettes,
            'totalMontant' => $totalMontant,
            'totalMontantVerser' => $totalMontantVerser,
            'totalMontantRestant' => $totalMontantRestant,
        ]);
    }

   

}
