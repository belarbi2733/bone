<?php

// src/AppBundle/Entity/Results.php

namespace Enterface\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_results")
 */
class Results 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @ORM\Column(name="applicationname",type="string", length=255)
     */
    private  $applicationname;
    
     /**
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
    * @ORM\Column(name="urldata", type="string", length=255)
    */
    private $urldata;
    
    /**
    * @ORM\Column(name="urlkey", type="string", length=255)
    */
    private $urlkey;
    
    /**
    * @ORM\ManyToOne(targetEntity="Enterface\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;
    
    /**
    * @ORM\Column(name="ipp", type="string", length=255)
    */
    private $ipp;
    
    /**
    * @ORM\Column(name="imageResultType", type="string", length=255)
    */
    private $imageResultType;
    
    /**
    * @ORM\Column(name="urlkeyslave", type="string", length=255)
    */
    private $urlkeyslave;
    
    /**
    * @ORM\Column(name="urldirectory", type="string", length=255)
    */
    private $urldirectory;
    
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
     * Set applicationname
     *
     * @param string $applicationname
     * @return Results
     */
    public function setApplicationname($applicationname)
    {
        $this->applicationname = $applicationname;

        return $this;
    }

    /**
     * Get applicationname
     *
     * @return string 
     */
    public function getApplicationname()
    {
        return $this->applicationname;
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
     * Set urldata
     *
     * @param string $urldata
     * @return Results
     */
    public function setUrldata($urldata)
    {
        $this->urldata = $urldata;

        return $this;
    }

    /**
     * Get urldata
     *
     * @return string 
     */
    public function getUrldata()
    {
        return $this->urldata;
    }
    
    /**
     * Set urlkey
     *
     * @param string $urlkey
     * @return Results
     */
    public function setUrlkey($urlkey)
    {
        $this->urlkey = $urlkey;

        return $this;
    }

    /**
     * Get urlkey
     *
     * @return string 
     */
    public function getUrlkey()
    {
        return $this->urlkey;
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
    
    /**
     * Set ipp
     *
     * @param string $ipp
     * @return Results
     */
    public function setIpp($ipp)
    {
        $this->ipp = $ipp;

        return $this;
    }

    /**
     * Get ipp
     *
     * @return string 
     */
    public function getIpp()
    {
        return $this->ipp;
    }
    
    /**
     * Set imageResultType
     *
     * @param string $imageResultType
     * @return Results
     */
    public function setImageResultType($imageResultType)
    {
        $this->imageResultType = $imageResultType;

        return $this;
    }

    /**
     * Get imageResultType
     *
     * @return string 
     */
    public function getImageResultType()
    {
        return $this->imageResultType;
    }
    
    /**
     * Set urlkeyslave
     *
     * @param string $urlkeyslave
     * @return Results
     */
    public function setUrlkeyslave($urlkeyslave)
    {
        $this->urlkeyslave = $urlkeyslave;

        return $this;
    }

    /**
     * Get urlkeyslave
     *
     * @return string 
     */
    public function getUrlkeyslave()
    {
        return $this->urlkeyslave;
    }
    
    
    /**
     * Set urldirectory
     *
     * @param string $urldirectory
     * @return Results
     */
    public function seturldirectory($urldirectory)
    {
        $this->urldirectory = $urldirectory;

        return $this;
    }

    /**
     * Get urldirectory
     *
     * @return string 
     */
    public function geturldirectory()
    {
        return $this->urldirectory;
    }
}
