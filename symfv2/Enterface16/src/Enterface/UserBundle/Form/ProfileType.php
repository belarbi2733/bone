<?php

/**
 * Description of RegistrationType
 *
 * @author leplat
 */


namespace Enterface\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword as OldUserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
 * */




class ProfileType extends AbstractType {
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
        return 'fos_user_profile';
    }
    public function getName(){
        return "enterface_user_profile";
    }
    
}

