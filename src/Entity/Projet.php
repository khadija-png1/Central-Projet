<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Projet
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $accesCodeSource = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $accesEnvironnement = null;

    #[ORM\Column(type: 'string', length: 20, options: ['default' => 'à faire'])]
    #[Assert\Choice(choices: ['à faire', 'en cours', 'terminé'], message: 'Choix invalide pour le statut.')]
    private string $status = 'à faire';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToMany(targetEntity: Technologie::class, inversedBy: 'projets')]
    private Collection $technologie;

    #[ORM\ManyToMany(targetEntity: Developpeur::class, inversedBy: 'projets')]
    private Collection $developpeur;

    #[ORM\ManyToMany(targetEntity: Hebergement::class, inversedBy: 'projets')]
    private Collection $hebergement;

    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: Notification::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $notifications;

    public function __construct()
    {
        $this->technologie = new ArrayCollection();
        $this->developpeur = new ArrayCollection();
        $this->hebergement = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAccesCodeSource(): ?string
    {
        return $this->accesCodeSource;
    }

    public function setAccesCodeSource(?string $accesCodeSource): static
    {
        $this->accesCodeSource = $accesCodeSource;
        return $this;
    }

    public function getAccesEnvironnement(): ?string
    {
        return $this->accesEnvironnement;
    }

    public function setAccesEnvironnement(?string $accesEnvironnement): static
    {
        $this->accesEnvironnement = $accesEnvironnement;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getTechnologie(): Collection
    {
        return $this->technologie;
    }

    public function addTechnologie(Technologie $technologie): static
    {
        if (!$this->technologie->contains($technologie)) {
            $this->technologie->add($technologie);
        }
        return $this;
    }

    public function removeTechnologie(Technologie $technologie): static
    {
        $this->technologie->removeElement($technologie);
        return $this;
    }

    public function getDeveloppeur(): Collection
    {
        return $this->developpeur;
    }

    public function addDeveloppeur(Developpeur $developpeur): static
    {
        if (!$this->developpeur->contains($developpeur)) {
            $this->developpeur->add($developpeur);
        }
        return $this;
    }

    public function removeDeveloppeur(Developpeur $developpeur): static
    {
        $this->developpeur->removeElement($developpeur);
        return $this;
    }

    public function getHebergement(): Collection
    {
        return $this->hebergement;
    }

    public function addHebergement(Hebergement $hebergement): static
    {
        if (!$this->hebergement->contains($hebergement)) {
            $this->hebergement->add($hebergement);
        }
        return $this;
    }

    public function removeHebergement(Hebergement $hebergement): static
    {
        $this->hebergement->removeElement($hebergement);
        return $this;
    }

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setProjet($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getProjet() === $this) {
                $notification->setProjet(null);
            }
        }

        return $this;
    }
}
