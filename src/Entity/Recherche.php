<?php

namespace App\Entity;

use App\Repository\RechercheRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RechercheRepository::class)
 */
class Recherche
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id;

    /**
     * @ORM\Column(type="Campus", length=100, nullable=true)
     */
    private $campus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stringRecherche;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkOrganisateur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkInscrit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkNonInscrit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkPassee;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getStringRecherche(): ?string
    {
        return $this->stringRecherche;
    }

    public function setStringRecherche(string $stringRecherche): self
    {
        $this->stringRecherche = $stringRecherche;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function isCheckOrganisateur(): ?bool
    {
        return $this->checkOrganisateur;
    }

    public function setCheckOrganisateur(bool $checkOrganisateur): self
    {
        $this->checkOrganisateur = $checkOrganisateur;

        return $this;
    }

    public function isCheckInscrit(): ?bool
    {
        return $this->checkInscrit;
    }

    public function setCheckInscrit(bool $checkInscrit): self
    {
        $this->checkInscrit = $checkInscrit;

        return $this;
    }

    public function isCheckNonInscrit(): ?bool
    {
        return $this->checkNonInscrit;
    }

    public function setCheckNonInscrit(bool $checkNonInscrit): self
    {
        $this->checkNonInscrit = $checkNonInscrit;

        return $this;
    }

    public function isCheckPassee(): ?bool
    {
        return $this->checkPassee;
    }

    public function setCheckPassee(bool $checkPassee): self
    {
        $this->checkPassee = $checkPassee;

        return $this;
    }

}
