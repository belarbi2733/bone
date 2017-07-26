<?php

namespace Enterface\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeService
 *
 * @ORM\Table(name="type_service", uniqueConstraints={@ORM\UniqueConstraint(name="idx_type_service", columns={"Id_Type_service"})})
 * @ORM\Entity
 */
class TypeService
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
     * @ORM\Column(name="Id_Type_service", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTypeService;



    /**
     * Set label
     *
     * @param string $label
     * @return TypeService
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
     * Get idTypeService
     *
     * @return integer 
     */
    public function getIdTypeService()
    {
        return $this->idTypeService;
    }
}
