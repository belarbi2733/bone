<?php
namespace Enterface\UserBundle\Command;

/**
 * Description of CreateUserCommand
 *
 * @author eladoui
 */
 
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Command\CreateUserCommand as BaseCommand;
 
class CreateUserCommand extends BaseCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('fos:user:create')
            ->getDefinition()->addArguments(array(
                new InputArgument('firstname', InputArgument::REQUIRED, 'The firstname'),
                new InputArgument('lastname', InputArgument::REQUIRED, 'The lastname'),
                new InputArgument('title', InputArgument::REQUIRED, 'The title'),
                new InputArgument('adress', InputArgument::REQUIRED, 'The adress'),
                new InputArgument('company', InputArgument::REQUIRED, 'The company'),
                
            ))
        ;
        $this->setHelp(<<<EOT
// L'aide qui va bien
EOT
            );
    }
 
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $firstname  = $input->getArgument('firstname');
        $lastname   = $input->getArgument('lastname');
        $title      = $input->getArgument('title');
        $adress     = $input->getArgument('adress');
        $company    = $input->getArgument('company');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');
        //$credit = $input->getOption('credit');
 
        /** @var \FOS\UserBundle\Model\UserManager $user_manager */
        $user_manager = $this->getContainer()->get('fos_user.user_manager');
 
        /** @var \Enterface\UserBundle\Entity\User $user */
        $user = $user_manager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setTitle($title);
        $user->setAdress($adress);
        $user->setCompany($company);
        $user->setEnabled((Boolean) !$inactive);
        $user->setSuperAdmin((Boolean) $superadmin);
        $user->setCredit(5);
    
        
 
        $user_manager->updateUser($user);
 
        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }
 
    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        if (!$input->getArgument('firstname')) {
            $firstname = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a firstname:',
                function($firstname) {
                    if (empty($firstname)) {
                        throw new \Exception('Firstname can not be empty');
                    }
 
                    return $firstname;
                }
            );
            $input->setArgument('firstname', $firstname);
        }
        if (!$input->getArgument('lastname')) {
            $lastname = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a lastname:',
                function($lastname) {
                    if (empty($lastname)) {
                        throw new \Exception('Lastname can not be empty');
                    }
 
                    return $lastname;
                }
            );
            $input->setArgument('lastname', $lastname);
        }
        
           if (!$input->getArgument('title')) {
            $title = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a title:',
                function($title) {
                    if (empty($title)) {
                        throw new \Exception('title can not be empty');
                    }
 
                    return $title;
                }
            );
            $input->setArgument('title', $title);
        }
        
        if (!$input->getArgument('adress')) {
            $adress = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an Adress:',
                function($adress) {
                    if (empty($adress)) {
                        throw new \Exception('Adress can not be empty');
                    }
 
                    return $adress;
                }
            );
            $input->setArgument('adress', $adress);
        }
        
        if (!$input->getArgument('company')) {
            $company = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a Company:',
                function($company) {
                    if (empty($company)) {
                        throw new \Exception('The company can not be empty');
                    }
 
                    return $company;
                }
            );
            $input->setArgument('company', $company);
        }
        
        
        
    }
     
}
