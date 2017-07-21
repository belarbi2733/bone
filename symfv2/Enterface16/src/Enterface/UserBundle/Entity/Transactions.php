<?php

// src/AppBundle/Entity/Transactions.php

namespace Enterface\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_transactions")
 */
class Transactions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
    * @ORM\Column(name="transactionID", type="string", length=255)
    */
    private $transactionID;
    
    /**
    * @ORM\ManyToOne(targetEntity="Enterface\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;
    
    
    
    public function __construct()

    {
    $this->date = new \Datetime();
    
    }
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \Datetime $date
     * @return Results
     */
    public function setDate(\Datetime $date)
    {
        $this->date = $date;

        return $date;
    }

    /**
     * Get date
     *
     * @return \Datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set transactionID
     *
     * @param string $transactionID
     * @return Results
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    /**
     * Get transactionID
     *
     * @return string 
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    
    
    
    /**
     * Set user
     *
     * @param User $user
     * @return Results
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    
}
