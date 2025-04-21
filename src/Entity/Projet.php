<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
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

    /**
     * @var Collection<int, Technologie>
     */
    #[ORM\ManyToMany(targetEntity: Technologie::class, inversedBy: 'projets')]
    private Collection $technologie;

    /**
     * @var Collection<int, Developpeur>
     */
    #[ORM\ManyToMany(targetEntity: Developpeur::class, inversedBy: 'projets')]
    private Collection $developpeur;

    /**
     * @var Collection<int, Hebergement>
     */
    #[ORM\ManyToMany(targetEntity: Hebergement::class, inversedBy: 'projets')]
    private Collection $hebergement;

    public function __construct()
    {
        $this->technologie = new ArrayCollection();
        $this->developpeur = new ArrayCollection();
        $this->hebergement = new ArrayCollection();
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

    /**
     * @return Collection<int, Technologie>
     */
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

    /**
     * @return Collection<int, Developpeur>
     */
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

    /**
     * @return Collection<int, Hebergement>
     */
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
}
