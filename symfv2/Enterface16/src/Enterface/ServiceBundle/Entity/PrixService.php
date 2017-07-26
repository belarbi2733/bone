<?php

namespace Enterface\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrixService
 *
 * @ORM\Table(name="prix_service", uniqueConstraints={@ORM\UniqueConstraint(name="idx_service", columns={"Id_service", "Id_type_service"})}, indexes={@ORM\Index(name="fk_type_service", columns={"Id_type_service"})})
 * @ORM\Entity
 */
class PrixService
{
    /**
     * @var integer
     *
     * @ORM\Column(name="Prix", type="integer", nullable=false)
     */
    private $prix;

    /**
     * @var integer
     *
     * @ORM\Column(name="Id_service", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idService;

    /**
     * @var integer
     *
     * @ORM\Column(name="Id_type_service", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idTypeService;



    /**
     * Set prix
     *
     * @param integer $prix
     * @return PrixService
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set idService
     *
     * @param integer $idService
     * @return PrixService
     */
    public function setIdService($idService)
    {
        $this->idService = $idService;

        return $this;
    }

    /**
     * Get idService
     *
     * @return integer 
     */
    public function getIdService()
    {
        return $this->idService;
    }

    /**
     * Set idTypeService
     *
     * @param integer $idTypeService
     * @return PrixService
     */
    public function setIdTypeService($idTypeService)
    {
        $this->idTypeService = $idTypeService;

        return $this;
    }

    /**
     * Get idTypeService
     *
     * @return integer 
     */
    public function getIdTypeService()
    {
        return $this->idTypeService;
    }
}
