<?php


/**
 * Description of RegistrationType
 *
 * @author eladoui
 */
namespace Enterface\UserBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationType extends AbstractType {
    //put your code here
   
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add("title")
                ->add("firstname")
                ->add("lastname")
                ->add("company")
                ->add("adress")
                                ;
    }
    public function getParent() {
        return "fos_user_registration";
    }
    public function getName(){
        return "enterface_user_registration";
    }
    
}
