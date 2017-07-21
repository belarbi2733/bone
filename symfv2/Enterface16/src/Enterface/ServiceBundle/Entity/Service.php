<?php

namespace Enterface\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="service", uniqueConstraints={@ORM\UniqueConstraint(name="idx_Service", columns={"Id_service"})})
 * @ORM\Entity
 */
class Service
{
    /**
     * @var string
     *
     * @ORM\Column(name="Label", type="string", length=100, nullable=false)
     */
    private $label;

    /**
     * @var integer
     *
     * @ORM\Column(name="Id_service", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idService;



    /**
     * Set label
     *
     * @param string $label
     * @return Service
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
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
}
