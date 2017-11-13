<?php

// src/AppBundle/Entity/User.php

namespace Enterface\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements \Yosimitso\WorkingForumBundle\Entity\UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
     /**
     * @ORM\Column(type="string", length=255)
     */
    private  $lastname;
    
     /**
     * @ORM\Column(type="string", length=255 )
     */
    private $firstname;
    
     /**
     * @ORM\Column(type="string", length=255 )
     */
    private $title;
    
     /**
     * @ORM\Column(type="string", length=255 )
     */
    private $adress;
    
     /**
     * @ORM\Column(type="string", length=255 )
     */
    private $company;
    
    
    /**
     * @ORM\Column(type="integer")
     */
    
    private $credit;

    
    public function __construct()
    {
        parent::__construct();
        // your own logic
        $credit=50;
        $this->setCredit($credit);
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return User
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set adress
     *
     * @param string $adress
     * @return User
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return string 
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return User
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }
    
    public function getCredit()
    {
        return $this->credit;
    }
    
  /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_url", type="string", nullable=true)
     */
    protected $avatarUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_post", type="integer", nullable=true)
     */
    protected $nbPost;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="banned", type="boolean", nullable=true)
     */
    protected $banned;

   
 public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @param string $avatarUrl
     *
     * @return User
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * @param int $nbPost
     *
     * @return User
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBanned()
    {
        return $this->banned;
    }

    /**
     * @param bool $banned
     *
     * @return User
     */
    public function setBanned($banned)
    {
        $this->banned = $banned;

        return $this;
    }  
    
    public function addNbPost($nb)
    {
        $this->nbPost += $nb;

        return $this;
    }

}