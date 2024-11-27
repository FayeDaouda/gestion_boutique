<?php

namespace App\Entity;

use App\Repository\DetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetteRepository::class)]
class Dette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantVerser = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantRestant = null;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'dettes')]
    private Collection $articles;

    #[ORM\ManyToOne(inversedBy: 'dettes')]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'dette', targetEntity: Paiement::class)]
    private Collection $paiements;

    #[ORM\OneToMany(mappedBy: 'dette', targetEntity: DetteArticle::class)]
    private Collection $detteArticles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->detteArticles = new ArrayCollection();
        $this->montantVerser = 0.0;
        $this->montantRestant = $this->montant;  // Initialisation du montant restant
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;
        $this->montantRestant = $montant - $this->montantVerser;  // Recalcul du montant restant
        return $this;
    }

    public function getMontantVerser(): ?float
    {
        return $this->montantVerser;
    }

    public function setMontantVerser(?float $montantVerser): static
    {
        $this->montantVerser = $montantVerser;
        $this->montantRestant = $this->montant - $montantVerser;  // Recalcul du montant restant
        return $this;
    }

    public function getMontantRestant(): ?float
    {
        return $this->montantRestant;
    }

    public function setMontantRestant(?float $montantRestant): static
    {
        $this->montantRestant = $montantRestant;
        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        $this->articles->removeElement($article);

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setDette($this); // Assurez-vous que Paiement a une méthode setDette
            // Recalculer montant restant après ajout du paiement
            $this->montantVerser += $paiement->getMontant();
            $this->montantRestant = $this->montant - $this->montantVerser;
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            $paiement->setDette(null);
            // Recalculer montant restant après suppression du paiement
            $this->montantVerser -= $paiement->getMontant();
            $this->montantRestant = $this->montant - $this->montantVerser;
        }

        return $this;
    }

    /**
     * @return Collection<int, DetteArticle>
     */
    public function getDetteArticles(): Collection
    {
        return $this->detteArticles;
    }

    public function setDetteArticles(Collection $detteArticles): static
    {
        $this->detteArticles = $detteArticles;
        return $this;
    }

    // Méthode de recalcul automatique du montant restant
    public function recalculerMontantRestant(): void
    {
        $totalVerser = 0;
        foreach ($this->paiements as $paiement) {
            $totalVerser += $paiement->getMontant();
        }
        $this->montantRestant = $this->montant - $totalVerser;
    }
}



