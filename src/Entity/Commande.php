<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_commande;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="boolean")
     */
    private $exped;

    /**
     * @ORM\OneToMany(targetEntity=Detailcommande::class, mappedBy="commande", orphanRemoval=true)
     */
    private $detailcommandes;

    public function __construct()
    {
        $this->detailcommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date_commande): self
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function isExped(): ?bool
    {
        return $this->exped;
    }

    public function setExped(bool $exped): self
    {
        $this->exped = $exped;

        return $this;
    }

    /**
     * @return Collection<int, Detailcommande>
     */
    public function getDetailcommandes(): Collection
    {
        return $this->detailcommandes;
    }

    public function addDetailcommande(Detailcommande $detailcommande): self
    {
        if (!$this->detailcommandes->contains($detailcommande)) {
            $this->detailcommandes[] = $detailcommande;
            $detailcommande->setCommande($this);
        }

        return $this;
    }

    public function removeDetailcommande(Detailcommande $detailcommande): self
    {
        if ($this->detailcommandes->removeElement($detailcommande)) {
            // set the owning side to null (unless already changed)
            if ($detailcommande->getCommande() === $this) {
                $detailcommande->setCommande(null);
            }
        }
        return $this;
    }
}
