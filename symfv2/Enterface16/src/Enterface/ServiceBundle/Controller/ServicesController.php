<?php
namespace Enterface\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Enterface\UserBundle\Entity\Results;
use Enterface\UserBundle\Entity\Transactions;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;






class ServicesController extends Controller {

    /* add by el adoui*/
public function toolboxAction() {
             //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return $this->render('EnterfaceServiceBundle:Services:toolbox.html.twig', array());
    
    }
public function algotreatment1Action()
    {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $ftp_user_name=$this->container->getParameter('windows_user');
        $ftp_user_pass=$this->container->getParameter('windows_password');
        $ftp_host=$this->container->getParameter('windows_host');
        
        $nom_fichier = $_FILES['fichier']['tmp_name'];
        $nom_fichier1= $_FILES['fichier']['tmp_name'];
          
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
        }
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        $target_dir = $outputdir;
        $target_file ="";
        $entree_fichier = $target_dir ."/test.jpg";
        $typealgo=1;
        $myfile = fopen($target_dir ."/filtre.txt", "w");
        if($_POST["algo"]=="SIFT")
        {
            $target_file = $target_dir ."/sift.jpg";
            $typealgo=1;
        }
        else if($_POST["algo"]=="SURF")
        {
            $target_file = $target_dir ."/surf.jpg";
            $typealgo=1;
        }
        else if($_POST["algo"]=="Color_Histogram")
        {
            $target_file = $target_dir ."/histo_cou.jpg";
            $typealgo=1;
        }
        else if($_POST["algo"]=="Hsv_Histogram")
        {
            $target_file = $target_dir ."/histo_hsv.jpg";
            $typealgo=1;
        }
        else if($_POST["algo"]=="Gaussian Filter")
        {
            $target_file = $target_dir ."/gaussian.jpg";
            $typealgo=2;
        }
        else if($_POST["algo"]=="Blur Filter")
        {
            $target_file = $target_dir ."/blur.jpg";
            $typealgo=2;
        }
        else if($_POST["algo"]=="Bilateral Filter")
        {
            $target_file = $target_dir ."/bilateral.jpg";
            $typealgo=2;
        }
        else if($_POST["algo"]=="Median Filter")
        {
            $target_file = $target_dir ."/median.jpg";
            $typealgo=2;
        }
        else if($_POST["algo"]=="Harris")
        {
            $target_file = $target_dir ."/harris.jpg";
            $typealgo=3;
        }
        else if($_POST["algo"]=="Canny")
        {
            $target_file = $target_dir ."/canny.jpg";
            $typealgo=4;
        }
        else if($_POST["algo"]=="Countours")
        {
            $target_file = $target_dir ."/countours.jpg";
            $typealgo=3;
        }
        else if($_POST["algo"]=="Laplacien")
        {
            $target_file = $target_dir ."/laplacien.jpg";
            $typealgo=2;
        }
        else if($_POST["algo"]=="Water Shed")
        {
            $target_file = $target_dir ."/water.jpg";
            $typealgo=5;
        }
        if($typealgo==1 || $typealgo==2)
        {
            fwrite($myfile, "15");
            fclose($myfile);
        }
        else if($typealgo==3)
        {
            fwrite($myfile, "140");
            fclose($myfile);
        }
        else if($typealgo==4)
        {
            fwrite($myfile, "100");
            fclose($myfile);
        }
        else
        {
            fwrite($myfile, "5");
            fclose($myfile);
        }
        $algorithm=$_POST["algo"];
        $uploadOk = 1 ; // ??? ça fait quoi ?
        
        if(move_uploaded_file($nom_fichier, $target_file ))
        {
            $a=1; 
        }
        shell_exec("cp $target_file $entree_fichier");
        if($_POST["mode"]=="CPU")
        {
            exec("sudo /usr/local/bin/docker_algo $target_dir");
        }
        else
        {
            //shell_exec(" scp -i ~/.ssh/gpu $target_file amine@10.138.0.4:/home/amine/results/");
            $user1=$this->container->getParameter('gpu_user');
            $pass1=$this->container->getParameter('gpu_password');
            $host1=$this->container->getParameter('gpu_host');
            $destination=basename($target_file);
            $connection = ssh2_connect($host1, 22);
            if (ssh2_auth_password($connection, $user1,$pass1))
            {
                  ssh2_scp_send($connection, $target_file, "/home/test/results/".$destination);
                  ssh2_scp_send($connection, $target_dir ."/filtre.txt", "/home/test/results/filtre.txt");
                  $stream = ssh2_exec($connection, 'docker_algo /home/test/results/');
                  stream_set_blocking($stream, true);
                  stream_get_contents($stream);
                  ssh2_scp_recv($connection, '/home/test/results/algo_resultat.jpg', $target_dir.'/algo_resultat.jpg');
                  exec ("rm $target_file");
            } 
            else 
            {
                throw new AccessDeniedException('The GPU Machine is OFF.');
            }
            
        }
        $mode=$_POST["mode"];
        //return new RedirectResponse($this->container->get('router')->generate('enterface_service_showtracking'));
        return $this->render('EnterfaceServiceBundle:Services:toolboxresults.html.twig', array('works' => true,'algo'=>$algorithm,'type'=>$typealgo,'mode'=>$mode));
    }
    public function algotreatment2Action()
    {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
        }
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        $target_dir = $outputdir;
        $target_file ="";
        $entree_fichier = $target_dir ."/test.jpg";
        $typealgo=1;
        $myfile = fopen($target_dir ."/filtre.txt", "w");
        $param1=$_POST['aa'];
        fwrite($myfile, $param1);
        fclose($myfile);
    
        if($_POST["bb"]=="Gaussian Filter")
        {
            $target_file = $target_dir ."/gaussian.jpg";
            $typealgo=2;
        }
        else if($_POST["bb"]=="Blur Filter")
        {
            $target_file = $target_dir ."/blur.jpg";
            $typealgo=2;
        }
        else if($_POST["bb"]=="Bilateral Filter")
        {
            $target_file = $target_dir ."/bilateral.jpg";
            $typealgo=2;
        }
        else if($_POST["bb"]=="Median Filter")
        {
            $target_file = $target_dir ."/median.jpg";
            $typealgo=2;
        }
        else if($_POST["bb"]=="Harris")
        {
            $target_file = $target_dir ."/harris.jpg";
            $typealgo=3;
        }
        else if($_POST["bb"]=="Canny")
        {
            $target_file = $target_dir ."/canny.jpg";
            $typealgo=4;
        }
        else if($_POST["bb"]=="Countours")
        {
            $target_file = $target_dir ."/countours.jpg";
            $typealgo=3;
        }
        else if($_POST["bb"]=="Laplacien")
        {
            $target_file = $target_dir ."/laplacien.jpg";
            $typealgo=3;
        }
        else if($_POST["bb"]=="Water Shed")
        {
            $target_file = $target_dir ."/water.jpg";
            $typealgo=5;
        }
        $algorithm=$_POST["bb"];
        shell_exec("cp  $entree_fichier $target_file");
        if($_POST["cc"]=="CPU")
        {
            exec("sudo /usr/local/bin/docker_algo $target_dir");
        }
        else
        {
            //shell_exec(" scp -i ~/.ssh/gpu $target_file amine@10.138.0.4:/home/amine/results/");
            $user1=$this->container->getParameter('gpu_user');
            $pass1=$this->container->getParameter('gpu_password');
            $host1=$this->container->getParameter('gpu_host');
            $destination=basename($target_file);
            $connection = ssh2_connect($host1, 22);
            if (ssh2_auth_password($connection, $user1,$pass1))
            {
                  ssh2_scp_send($connection, $target_file, "/home/test/results/".$destination);
                  ssh2_scp_send($connection, $target_dir ."/filtre.txt", "/home/test/results/filtre.txt");
                  $stream = ssh2_exec($connection, 'docker_algo /home/test/results/');
                  stream_set_blocking($stream, true);
                  stream_get_contents($stream);
                  ssh2_scp_recv($connection, '/home/test/results/algo_resultat.jpg', $target_dir.'/algo_resultat.jpg');
                  exec ("rm $target_file");
            } 
            else 
            {
                die('Echec de l\'identification en utilisant une cl� publique');
            }
            
        }
        //return new RedirectResponse($this->container->get('router')->generate('enterface_service_showtracking'));
        return $this->render('EnterfaceServiceBundle:Services:affichealgo.html.twig', array('works' => true));
    }
    
public function TiffViewerAction() {
             //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
  
        
        return $this->render('EnterfaceServiceBundle:Services:TiffViewer.html.twig', array());
        
    }
        public function StlViewerAction() {
             //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
  
        
        return $this->render('EnterfaceServiceBundle:Services:StlViewer.html.twig', array());
        
    }
    
    public function VertebraPersonalizedAction() {
             //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
  
        
        return $this->render('EnterfaceServiceBundle:Services:VertebraPersonalized.html.twig', array());
        
    }

        public function mobilityAction() {
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        else{
        return $this->render('EnterfaceServiceBundle:Services:mobility.html.twig', array());
        }
    }
    
        public function ClesAction() {
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        else{
        return $this->render('EnterfaceServiceBundle:Services:cles.html.twig', array());
        }
    }
    

    public function ShowVertebraPersonalizedAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        
        
        $nom_fichier = $_FILES['fichier']['tmp_name'];
        
        //les valeur des parametres par défaut!!
        $param1= 5; // CLahe
        $param2= 2; //Mean shift value (SpatialRad) 
        $param3= 30; //Mean shift value (SpatialRad)
        $param4= 5; //Opening 
        $param5= 3; //Morphologie
        $param6=100; // ellipse high size
        $param7= 50; //ellipse low size
        $param8= 3; // polynome degree
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());}
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current'); }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');}
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        
        
        $target_dir = $outputdir;
        $target_file = $target_dir ."/1.jpg";
        $uploadOk = 1; 
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"&& $imageFileType != "gif" )
        {
            return $this->render('EnterfaceServiceBundle:Services:showvertebraPersonalized.html.twig', 
                            array('works' => false, 'msg' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
            $uploadOk = 0;
        }
        if(move_uploaded_file($nom_fichier, $target_file )){
            $a=1;
        }
        // changement du chemin           
        //Il faut noter que l'executable accepte 8 parametres
        
        /*Verivifie les choix de l'utilisateur 'Checkbox'*/
        
       
        
        
       exec("./VertebraAdcanced img1 $param1 $param2 $param3 $param4 $param5 $param6 $param7 $param8"); 
        
        //Payement
                $credit=$user->getCredit();
                $credit=$credit-2;
                $user->setCredit($credit);
                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                
        return $this->render('EnterfaceServiceBundle:Services:showvertebraPersonalized.html.twig', array('works' => true));        
    }

    public function showvertebraAdvancedAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        
        /*si la premier soumission du document */        
        $nom_fichier = $_FILES['fichier']['tmp_name'];
       
        
        /*sinon (au moment du refresh ! ) il faut recupérer le nom de la variable $nom_fichier !*/
        /*.....*/
        
        
        //les valeur des parametres par défaut!!
        /*$param1= 5; // CLahe
        $param2= 2; //Mean shift value (SpatialRad) 
        $param3= 30; //Mean shift value (SpatialRad)
        $param4= 5; //Opening 
        $param5= 3; //Morphologie
        $param6=100; // ellipse high size
        $param7= 50; //ellipse low size
        $param8= 3; // polynome degree*/
        $param1= 3; // CLahe
        $param2= 4; //Mean shift value (SpatialRad) 
        $param3= 24; //Mean shift value (ColorlRad)
        $param4= 5; //Opening 
        $param5= 2; //Morphologie
        $param6=100; // ellipse high size
        $param7= 40; //ellipse low size
        $param8= 3; // polynome degree
        
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());}
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current'); }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');}
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        
        
        $target_dir = $outputdir;
        $target_file = $target_dir ."/1.jpg";
        $uploadOk = 1; 
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"&& $imageFileType != "gif" )
        {
            return $this->render('EnterfaceServiceBundle:Services:showvertebraAdvanced.html.twig', 
                            array('works' => false, 'msg' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
            $uploadOk = 0;
        }
        if(move_uploaded_file($nom_fichier, $target_file )){
            $a=1;
        }
        $myfile = fopen($target_dir ."/file_adv.txt", "w");
        fwrite($myfile, "3 2 5 4 24 100 40 3");
        fclose($myfile);
         // changement du chemin           
        //Il faut noter que l'executable accepte 8 parametres dans ce mode (advanced on aura acces que à 5 paramtrer les 3 autres resterons par defaut)          
       
        exec("sudo docker_vertebrae_adv $outputdir",$output); 
        
        
        
        
        //Payement
        $credit=$user->getCredit();
        $credit=$credit-2;
        $user->setCredit($credit);
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->render('EnterfaceServiceBundle:Services:showvertebraAdvanced.html.twig', array('works' => true));
            
    }
    public function advancedvertebraAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        /*$param1= 5; // CLahe
        $param2= $_POST['bb'];; //Mean shift value (SpatialRad) 
        $param3= $_POST['cc'];; //Mean shift value (SpatialRad)
        $param4= $_POST['aa'];; //Opening 
        $param5= 3; //Morphologie
        $param6= $_POST['dd'];; // ellipse high size
        $param7= $_POST['ee'];; //ellipse low size
        $param8= 3; // polynome degree*/
        $param1= 3; // CLahe
        $param2= $_POST['bb'];; //Mean shift value (SpatialRad) 
        $param3= $_POST['cc'];; //Mean shift value (SpatialRad)
        $param4= $_POST['aa'];; //Opening 
        $param5= 2; //Morphologie
        $param6= $_POST['dd'];; // ellipse high size
        $param7= $_POST['ee'];; //ellipse low size
        $param8= 3; // polynome degree
        
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());}
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current'); }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');}
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        
        
        $target_dir = $outputdir;
        $target_file = $target_dir ."/1.jpg";
        $myfile = fopen($target_dir ."/file_adv.txt", "w");
        fwrite($myfile, $param1." ".$param5." ".$param4." ".$param2." ".$param3." ".$param6." ".$param7." ".$param8);
        fclose($myfile);
         // changement du chemin           
        //Il faut noter que l'executable accepte 8 parametres dans ce mode (advanced on aura acces que à 5 paramtrer les 3 autres resterons par defaut)          
       
        exec("sudo docker_vertebrae_adv $outputdir",$output); 
        return $this->render('EnterfaceServiceBundle:Services:affichageadv.html.twig', array('works' => true));


    }
    public function VertebraDetectionAction() {
               //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        return $this->render('EnterfaceServiceBundle:Services:Vertebradetection.html.twig', array());
    }
    public function dicomviewerAction(){
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return $this->render('EnterfaceServiceBundle:Services:dicomviewer.html.twig');    
    }



    /* ---------------------*/

    public function cleoAction() {
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        else{
        return $this->render('EnterfaceServiceBundle:Services:cleo.html.twig', array());
        }
    }
    /* cleo advanced input values */
    
    public function cleoadvAction() {
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<4){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        else{
        return $this->render('EnterfaceServiceBundle:Services:cleoadv.html.twig', array());
        }
    }
    /* */

     /* cleo personalised input values */
    
    public function cleopersoAction() {
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<6){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        else
        {
          $test1=$_GET['a'];
          $test2=$_GET['b'];
          $test3=$_GET['c'];
          return $this->render('EnterfaceServiceBundle:Services:cleoperso.html.twig', array('test1'=>$test1,'test2'=>$test2,'test3'=>$test3));
        }
    }
    /* */
    
    
    public function cleotreatmentAction() {
        /*commented by El Adoui*/
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
     $ftp_user_name=$this->container->getParameter('windows_user');
     $ftp_user_pass=$this->container->getParameter('windows_password');
     $ftp_host=$this->container->getParameter('windows_host');
     //$ftp_host="10.138.0.3";
     //ftp puts
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
        mkdir('/var/www/symfv2/Enterface16/UserResults/');
     }
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
        mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
     }
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
        mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
     }if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
         mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
     }

     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results/dicom')){
         mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results/dicom');
     }
     $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/';
     $outputdir=$target_dir."/Results/";
     $dicomdir=$outputdir."/dicom/";

     $files = glob($target_dir.'/*'); // get all file names
     foreach($files as $file){ // iterate files
           if(is_file($file))
                unlink($file); // delete file
     }
     $files = glob($outputdir.'/*'); // get all file names
     foreach($files as $file){ // iterate files
           if(is_file($file))
                unlink($file); // delete file
     }

     $connect_it = ftp_connect( $ftp_host );
     $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
     
     //vider le dossier
      $connection = ssh2_connect($ftp_host, 22);
     if (ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass))
     {
                 $stream2 = ssh2_exec($connection, 'cmd /C rmdir /Q /S C:\Users\mohamedamine_belarbi\Downloads\ftp\dicom');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
                 $stream2 = ssh2_exec($connection, 'cmd /C mkdir C:\Users\mohamedamine_belarbi\Downloads\ftp\dicom');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
     }
     $i=0;
     //ftp_put( $connect_it, $ser,$nom_fichier , FTP_BINARY ) ) 
     foreach ($_FILES["fichier"]["error"] as $key => $error) 
     {
         if ($error == UPLOAD_ERR_OK) 
         {
                 $tmp_name = $_FILES["fichier"]["tmp_name"][$key];
                 //$name = $_FILES["fichier"]["name"][$key];
                 $name="fichier".$i++.".dcm";
                 move_uploaded_file($tmp_name, $target_dir . "/$name");
		 //move_uploaded_file($tmp_name, $dicomdir . "$name");
                 $ser = "dicom/" . $name;
                 ftp_put( $connect_it, $ser,$target_dir . "/$name", FTP_BINARY );                 
         }  
     }
     
     $nom_bmd = $_FILES['bmd']['tmp_name'];
     $nom_phantom = $_FILES['phantom']['tmp_name'];
     $target_bmd = $outputdir ."BMDvalues1.txt";
     $target_phantom = $outputdir ."Phantom1.txt";
     move_uploaded_file( $nom_bmd, $target_bmd );
     move_uploaded_file( $nom_phantom, $target_phantom );
     
     ftp_put( $connect_it,"BMDvalues1.txt",$target_bmd, FTP_BINARY );
     ftp_put( $connect_it, "Phantom1.txt",$target_phantom, FTP_BINARY );
     
     //ssh connect
     $connection = ssh2_connect($ftp_host, 22);
     if (ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass))
     {       
             $stream2 = ssh2_exec($connection, 'cmd /C java -jar Downloads\ftp\BASIC_CLEO_V5A.jar Downloads\ftp\dicom\fichier0.dcm Downloads\ftp\BMDvalues1.txt Downloads\ftp\Phantom1.txt');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
                 $s1=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\BMD.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s1, true);
                 stream_get_contents($s1);
                 $s2=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\Input_values.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s2, true);
                 stream_get_contents($s2);
                 $s3=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s3, true);
                 stream_get_contents($s3);
                 $s4=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\Microarchitecture.tif C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s4, true);
                 stream_get_contents($s4);
     }
     else
     {
         echo "error";
     }
    
     // ftp get     
     $bmd1="$outputdir/BMD.txt";
     $bmd2="$outputdir/Input_values.txt";
     $bmd3="$outputdir/Results.txt";
     $bmd4="$outputdir/Microarchitecture.tif";
     ftp_get($connect_it, $bmd1, "BMD.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd2, "Input_values.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd3, "CLEO_Results.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd4, "Microarchitecture.tif", FTP_BINARY);
     ftp_close($connect_it);
     
     //exec("sudo /usr/local/bin/docker_cleo $outputdir"); 
     //Payement
     $credit=$user->getCredit();
     $credit=$credit-2;
     $user->setCredit($credit);
     $em=$this->getDoctrine()->getManager();
     $em->persist($user);
     $em->flush();
     return new RedirectResponse($this->container->get('router')->generate('enterface_service_showcleo'));
    }
    
    /* CLEO ADVANCED -------------------------------------------------------------------------  */
    public function cleotreatmentadvAction() {
        /*commented by El Adoui*/
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
     $ftp_user_name=$this->container->getParameter('windows_user');
     $ftp_user_pass=$this->container->getParameter('windows_password');
     $ftp_host=$this->container->getParameter('windows_host');
     //$ftp_host="10.138.0.3";
     //ftp puts
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
        mkdir('/var/www/symfv2/Enterface16/UserResults/');
     }
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
        mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
     }
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
        mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
     }if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
         mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
     }

     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results/dicom')){
         mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results/dicom');
     }
     $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/';
     $outputdir=$target_dir."/Results/";
     $dicomdir=$outputdir."/dicom/";

     $files = glob($target_dir.'/*'); // get all file names
     foreach($files as $file){ // iterate files
           if(is_file($file))
                unlink($file); // delete file
     }
     $files = glob($outputdir.'/*'); // get all file names
     foreach($files as $file){ // iterate files
           if(is_file($file))
                unlink($file); // delete file
     }

     $connect_it = ftp_connect( $ftp_host );
     $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
     
     //vider le dossier
      $connection = ssh2_connect($ftp_host, 22);
     if (ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass))
     {
                 $stream2 = ssh2_exec($connection, 'cmd /C rmdir /Q /S C:\Users\mohamedamine_belarbi\Downloads\ftp\dicom');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
                 $stream2 = ssh2_exec($connection, 'cmd /C mkdir C:\Users\mohamedamine_belarbi\Downloads\ftp\dicom');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
     }
     $i=0;
     //ftp_put( $connect_it, $ser,$nom_fichier , FTP_BINARY ) ) 
     foreach ($_FILES["fichier"]["error"] as $key => $error) 
     {
         if ($error == UPLOAD_ERR_OK) 
         {
                 $tmp_name = $_FILES["fichier"]["tmp_name"][$key];
                 //$name = $_FILES["fichier"]["name"][$key];
                 $name="fichier".$i++.".dcm";
                 move_uploaded_file($tmp_name, $target_dir . "/$name");
		 //move_uploaded_file($tmp_name, $dicomdir . "$name");
                 $ser = "dicom/" . $name;
                 ftp_put( $connect_it, $ser,$target_dir . "/$name", FTP_BINARY );                 
         }  
     }
     
     $nom_bmd = $_FILES['bmd']['tmp_name'];
     $nom_phantom = $_FILES['phantom']['tmp_name'];
     $target_bmd = $outputdir ."BMDvalues1.txt";
     $target_phantom = $outputdir ."Phantom1.txt";
     move_uploaded_file( $nom_bmd, $target_bmd );
     move_uploaded_file( $nom_phantom, $target_phantom );
     
     ftp_put( $connect_it,"BMDvalues1.txt",$target_bmd, FTP_BINARY );
     ftp_put( $connect_it, "Phantom1.txt",$target_phantom, FTP_BINARY );
     
     //ssh connect
     $connection = ssh2_connect($ftp_host, 22);
     if (ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass))
     {       
             $stream2 = ssh2_exec($connection, 'cmd /C java -jar Downloads\ftp\ADVANCED_CLEO_V5.jar Downloads\ftp\dicom\fichier0.dcm Downloads\ftp\BMDvalues1.txt Downloads\ftp\Phantom1.txt');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
                 $s1=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\BMD.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s1, true);
                 stream_get_contents($s1);
                 
                 $s2=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\Input_values.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s2, true);
                 stream_get_contents($s2);
                 
                 $s3=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results_Advanced.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s3, true);
                 stream_get_contents($s3);
                 
                 $s4=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results2_Advanced.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s4, true);
                 stream_get_contents($s4);
                 
                 $s5=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results3_Advanced.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s5, true);
                 stream_get_contents($s5);
     }
     else
     {
         echo "error";
     }
    
     // ftp get     
     $bmd1="$outputdir/BMD.txt";
     $bmd2="$outputdir/Input_values.txt";
     $bmd3="$outputdir/Results1.txt";
     $bmd4="$outputdir/Results2.txt";
     $bmd5="$outputdir/Results3.txt";
     //$bmd4="$outputdir/Microarchitecture.tif";
     ftp_get($connect_it, $bmd1, "BMD.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd2, "Input_values.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd3, "CLEO_Results_Advanced.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd4, "CLEO_Results2_Advanced.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd5, "CLEO_Results3_Advanced.txt", FTP_BINARY);
     //ftp_get($connect_it, $bmd4, "Microarchitecture.tif", FTP_BINARY);
     ftp_close($connect_it);
     
     //exec("sudo /usr/local/bin/docker_cleo $outputdir"); 
     //Payement
     $credit=$user->getCredit();
     $credit=$credit-4;
     $user->setCredit($credit);
     $em=$this->getDoctrine()->getManager();
     $em->persist($user);
     $em->flush();
     return new RedirectResponse($this->container->get('router')->generate('enterface_service_showCleoadv'));
    }
    
    /*----------------------------------------------------------------------------------------- */
    
    /* CLEO Personalized -------------------------------------------------------------------------  */
    public function cleotreatmentpersoAction() {
        /*commented by El Adoui*/
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
     $ftp_user_name=$this->container->getParameter('windows_user');
     $ftp_user_pass=$this->container->getParameter('windows_password');
     $ftp_host=$this->container->getParameter('windows_host');
     //$ftp_host="10.138.0.3";
     //ftp puts
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
        mkdir('/var/www/symfv2/Enterface16/UserResults/');
     }
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
        mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
     }
     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
        mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
     }if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
         mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
     }

     if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results/dicom')){
         mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results/dicom');
     }
     $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/';
     $outputdir=$target_dir."/Results/";
     $dicomdir=$outputdir."/dicom/";
     $var1=$_POST['test1'];
     $var2=$_POST['test2'];
     $var3=$_POST['test3'];
     
     $files = glob($target_dir.'/*'); // get all file names
     foreach($files as $file){ // iterate files
           if(is_file($file))
                unlink($file); // delete file
     }
     $files = glob($outputdir.'/*'); // get all file names
     foreach($files as $file){ // iterate files
           if(is_file($file))
                unlink($file); // delete file
     }
     $param=$outputdir."parameters.txt";
     $fichier = fopen($param, "w");
     fwrite($fichier,$var1.'\n');
     fwrite($fichier,$var2.'\n');
     fwrite($fichier,$var3);
     $connect_it = ftp_connect( $ftp_host );
     $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
     
     //vider le dossier
      $connection = ssh2_connect($ftp_host, 22);
     if (ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass))
     {
                 $stream2 = ssh2_exec($connection, 'cmd /C rmdir /Q /S C:\Users\mohamedamine_belarbi\Downloads\ftp\dicom');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
                 $stream2 = ssh2_exec($connection, 'cmd /C mkdir C:\Users\mohamedamine_belarbi\Downloads\ftp\dicom');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
     }
     $i=0;
     //ftp_put( $connect_it, $ser,$nom_fichier , FTP_BINARY ) ) 
     foreach ($_FILES["fichier"]["error"] as $key => $error) 
     {
         if ($error == UPLOAD_ERR_OK) 
         {
                 $tmp_name = $_FILES["fichier"]["tmp_name"][$key];
                 //$name = $_FILES["fichier"]["name"][$key];
                 $name="fichier".$i++.".dcm";
                 move_uploaded_file($tmp_name, $target_dir . "/$name");
		             //move_uploaded_file($tmp_name, $dicomdir . "$name");
                 $ser = "dicom/" . $name;
                 ftp_put( $connect_it, $ser,$target_dir . "/$name", FTP_BINARY );                 
         }  
     }
     
     $nom_bmd = $_FILES['bmd']['tmp_name'];
     $nom_phantom = $_FILES['phantom']['tmp_name'];
     $target_bmd = $outputdir ."BMDvalues1.txt";
     $target_phantom = $outputdir ."Phantom1.txt";
     $target_parameters=$outputdir."parameters.txt";
     move_uploaded_file( $nom_bmd, $target_bmd );
     move_uploaded_file( $nom_phantom, $target_phantom );
     
     ftp_put( $connect_it,"BMDvalues1.txt",$target_bmd, FTP_BINARY );
     ftp_put( $connect_it, "Phantom1.txt",$target_phantom, FTP_BINARY );
     ftp_put( $connect_it, "parameters.txt",$target_parameters, FTP_BINARY );
     
     //ssh connect
     $connection = ssh2_connect($ftp_host, 22);
     if (ssh2_auth_password($connection, $ftp_user_name, $ftp_user_pass))
     {       
             $stream2 = ssh2_exec($connection, 'cmd /C java -jar Downloads\ftp\PERSONALIZED_CLEO_V5A.jar Downloads\ftp\dicom\fichier0.dcm Downloads\ftp\BMDvalues1.txt Downloads\ftp\Phantom1.txt Downloads\ftp\parameters.txt');
                 stream_set_blocking($stream2, true);
                 stream_get_contents($stream2);
                 $s1=ssh2_exec($connection, 'cmd /C move -r C:\Users\mohamedamine_belarbi\Results C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s1, true);
                 stream_get_contents($s1);
                 
                 /*$s2=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\Input_values.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s2, true);
                 stream_get_contents($s2);
                 
                 $s3=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results_Advanced.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s3, true);
                 stream_get_contents($s3);
                 
                 $s4=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results2_Advanced.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s4, true);
                 stream_get_contents($s4);
                 
                 $s5=ssh2_exec($connection, 'cmd /C move C:\Users\mohamedamine_belarbi\CLEO_Results3_Advanced.txt C:\Users\mohamedamine_belarbi\Downloads\ftp');
                 stream_set_blocking($s5, true);
                 stream_get_contents($s5);*/
     }
     else
     {
         echo "error";
     }
    
     // ftp get     
     $bmd1="$outputdir/BMD.txt";
     $bmd2="$outputdir/Input_values.txt";
     $bmd3="$outputdir/Results1.txt";
     
     
     //$bmd4="$outputdir/Microarchitecture.tif";
     ftp_get($connect_it, $bmd1, "Results/BMD.txt", FTP_BINARY);
     ftp_get($connect_it, $bmd2, "Results/Input_values.txt", FTP_BINARY);
     //ftp_get($connect_it, $bmd3, "Results/CLEO_Results_Personalized.txt", FTP_BINARY);
     /*-------------------------------------------------------------------------------- */
     if($var1==1)
     {
       $bmd3="$outputdir/Results1.txt";
       ftp_get($connect_it, $bmd3, "Results/CLEO_Results_Personalized.txt", FTP_BINARY);
     }
     if($var2==1)
     {
       $bmd4="$outputdir/Results2.txt";
       ftp_get($connect_it, $bmd4, "Results/CLEO_Results2_Personalized.txt", FTP_BINARY);
       $bmd5="$outputdir/Results3.txt";
       ftp_get($connect_it, $bmd5, "Results/CLEO_Results3_Personalized.txt", FTP_BINARY);
     }
     if($var3==1)
     {
       $bmd6="$outputdir/Results4.txt";
       ftp_get($connect_it, $bmd6, "Results/CLEO_Results4_Personalized.txt", FTP_BINARY);
     }
     /*----------------------------------------------------------------------------------*/
     //ftp_get($connect_it, $bmd4, "Microarchitecture.tif", FTP_BINARY);
     ftp_close($connect_it);
     
     //exec("sudo /usr/local/bin/docker_cleo $outputdir"); 
     //Payement
     $credit=$user->getCredit();
     $credit=$credit-6;
     $user->setCredit($credit);
     $em=$this->getDoctrine()->getManager();
     $em->persist($user);
     $em->flush();
     return new RedirectResponse($this->container->get('router')->generate('enterface_service_showCleoperso($var1,$var2,$var3)'));
    }
    
    /*----------------------------------------------------------------------------------------- */
    public function downloadResultAction($file){
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current';
        $outputdir=$target_dir."/Results/";

        $response = new BinaryFileResponse("$outputdir/$file");
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        
        return $response;
    }

    public function showVertebraImageAction($a,$b,$c,$file){
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current';
        $outputdir=$target_dir."/Results/output/Result_".$a."_".$b."_".$c;

        $response = new BinaryFileResponse("$outputdir/$file");
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        
        return $response;
    }
    
    public function showCleoAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current';
        $outputdir=$target_dir."/Results";

               // Open the text file
        	     // Input Values :
               $bmd = fopen("$outputdir/Input_values.txt", "r");
               $BADU= fgets($bmd);
               $MBMD= fgets($bmd);
               $SD=fgets($bmd);
               fclose($bmd);
          
                 // Phantom Values :
                $phan = fopen("$outputdir/Phantom1.txt", "r");
                $HU1=fgets($phan,5);
                $HA1=fgets($phan,8);
                
                $HU2=fgets($phan,7);
                $HA2=fgets($phan,10);
                
                fgets($phan);
              
                $HU3=fgets($phan,10);
                $HA3=fgets($phan,13);
                
              
                $HU4=fgets($phan,10);
                $HA4=fgets($phan,13);
                
                $HU5=fgets($phan,10);
                $HA5=fgets($phan,14);
                
                $HU6=fgets($phan,10);
                $HA6=fgets($phan,14);
                fclose($phan);
                
                 //BMD2.txt
                $bmd2=fopen("$outputdir/BMD.txt","r");
                $BMC=fgets($bmd2);
                $BMD=fgets($bmd2);
                $TScor=fgets($bmd2);
                fclose($bmd2);
                // Results.txt
                $Res = fopen("$outputdir/Results.txt", "r");
                $BVolum=fgets($Res);
                $TVolum=fgets($Res);
                $VFraction=fgets($Res);
                $Conn=fgets($Res);
                $TN=fgets($Res);
                $TthM=fgets($Res);
                $TthDev=fgets($Res);
                $TthMax=fgets($Res);
                
                $TspM=fgets($Res);
                $TspDev=fgets($Res);
                $TspMax=fgets($Res);       
                $DA="Decommenter les lignes 669 et 570 dans ServiceController.php apres l'integration de la new clio dans la MV";
                $tDA="Decommenter les lignes 619 et 620 dans ServiceController.php apres l'integration de la new clio dans la MV";
              
                //$DA=fgets($Res,8);
                //$tDA=fgets($Res,7);
                $FDim=fgets($Res);
                $R2=fgets($Res);         
               
                fclose($Res);

               
    return $this->render('EnterfaceServiceBundle:Services:cleotreatment.html.twig', array('works' =>true,'BADU'=>$BADU, 'MBMD'=> $MBMD,
    'SD'=> $SD,'HU1'=> $HU1,'HA1'=> $HA1,'HU2'=> $HU2,'HA2'=> $HA2,'HU3'=> $HU3,'HA3'=> $HA3,'HU4'=> $HU4,'HA4'=> $HA4,'HU5'=> $HU5,'HA5'=> $HA5,'HU6'=> $HU6,'HA6'=> $HA6, 'BVolum'=> $BVolum,'TVolum'=> $TVolum, 'VFraction'=>$VFraction,'BMC'=>$BMC,'BMD'=>$BMD,'TScor'=>$TScor,'FDim'=>$FDim,
    'R2'=>$R2, 'Conn'=>$Conn,'TN'=>$TN, 'TthM'=>$TthM, 'TthDev'=>$TthDev, 'TthMax'=> $TthMax, 'TspM'=> $TspM, 'TspDev'=> $TspDev, 'TspMax'=>$TspMax,'DA'=>$DA,'tDA'=>$tDA));
         }
        
   /* SHOW CLEO ADVANCED */
       public function showCleoadvAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current';
        $outputdir=$target_dir."/Results";

     // Open the text file
	     // Input Values :
             $bmd = fopen("$outputdir/Input_values.txt", "r");
             $BADU= fgets($bmd);
             $MBMD= fgets($bmd);
             $SD=fgets($bmd);
             fclose($bmd);
          
             // Phantom Values :
                $phan = fopen("$outputdir/Phantom1.txt", "r");
                $HU1=fgets($phan,5);
                $HA1=fgets($phan,8);
                
                $HU2=fgets($phan,7);
                $HA2=fgets($phan,10);
                
                fgets($phan);
              
                $HU3=fgets($phan,10);
                $HA3=fgets($phan,13);
                
              
                $HU4=fgets($phan,10);
                $HA4=fgets($phan,13);
                
                $HU5=fgets($phan,10);
                $HA5=fgets($phan,14);
                
                $HU6=fgets($phan,10);
                $HA6=fgets($phan,14);
                fclose($phan);
                
             //BMD2.txt
                $bmd2=fopen("$outputdir/BMD.txt","r");
               
                $BMC=fgets($bmd2);
                $BMD=fgets($bmd2);
                $TScor=fgets($bmd2);
                
                fclose($bmd2);
                
                // Results1.txt
                
                $Res = fopen("$outputdir/Results1.txt", "r");
                $BVolum=fgets($Res);
                $TVolum=fgets($Res);
                $VFraction=fgets($Res);
                $Conn=fgets($Res);
                $TN=fgets($Res);
                $TthM=fgets($Res);
                $TthDev=fgets($Res);
                $TthMax=fgets($Res);
                
                $TspM=fgets($Res);
                $TspDev=fgets($Res);
                $TspMax=fgets($Res); 
                $DA="Decommenter les lignes 669 et 570 dans ServiceController.php apres l'integration de la new clio dans la MV";
                $tDA="Decommenter les lignes 619 et 620 dans ServiceController.php apres l'integration de la new clio dans la MV";
                //$DA=fgets($Res,8);
                //$tDA=fgets($Res,7);
                $FDim=fgets($Res);
                $R2=fgets($Res);         
                fclose($Res);
                 
                 // Results2.txt
                 $Res2=fopen("$outputdir/Results2.txt", "r");
                 $BCODE=fgets($Res2);
                 $CSA=fgets($Res2);
                 $XCENT=fgets($Res2);
                 $YCENT=fgets($Res2);
                 $DENS=fgets($Res2);
                 $THETA=fgets($Res2);
                 $FMIN=fgets($Res2);
                 $FMAX=fgets($Res2);
                 $FANGLE=fgets($Res2);
                 $PERI=fgets($Res2);
                 $MEANTH=fgets($Res2);
                 fclose($Res2);
                 
                 // Results3.txt
                 $Res3=fopen("$outputdir/Results3.txt", "r");
                 $XC=fgets($Res3);
                 $YC=fgets($Res3);
                 $ZC=fgets($Res3);
                 $VOLL=fgets($Res3);
                 $MASS=fgets($Res3);
                 $ICXX=fgets($Res3);
                 $ICYY=fgets($Res3);
                 $ICZZ=fgets($Res3);
                 $I1=fgets($Res3);
                 $I2=fgets($Res3);
                 $I3=fgets($Res3);
                 fclose($Res3);

                
                return $this->render('EnterfaceServiceBundle:Services:affichecleoadv.html.twig', array('works' =>true,'BADU'=>$BADU, 'MBMD'=> $MBMD,
    'SD'=> $SD,'HU1'=> $HU1,'HA1'=> $HA1,'HU2'=> $HU2,'HA2'=> $HA2,'HU3'=> $HU3,'HA3'=> $HA3,'HU4'=> $HU4,'HA4'=> $HA4,'HU5'=> $HU5,'HA5'=> $HA5,'HU6'=> $HU6,'HA6'=> $HA6, 'BVolum'=> $BVolum,'TVolum'=> $TVolum, 'VFraction'=>$VFraction,'BMC'=>$BMC,'BMD'=>$BMD,'TScor'=>$TScor,'FDim'=>$FDim,
    'R2'=>$R2, 'Conn'=>$Conn,'TN'=>$TN, 'TthM'=>$TthM, 'TthDev'=>$TthDev, 'TthMax'=> $TthMax, 'TspM'=> $TspM, 'TspDev'=> $TspDev, 'TspMax'=>$TspMax,'DA'=>$DA,'tDA'=>$tDA, 'BCODE'=>$BCODE,'CSA'=>$CSA,'XCENT'=>$XCENT,'YCENT'=>$YCENT,'DENS'=>$DENS,'THETA'=>$THETA,'FMIN'=>$FMIN,'FMAX'=>$FMAX,'FANGLE'=>$FANGLE,'PERI'=>$PERI,'MEANTH'=>$MEANTH,'XC'=>$XC,'YC'=>$YC,'ZC'=>$ZC,'VOLL'=>$VOLL,'MASS'=>$MASS,'ICXX'=>$ICXX,'ICYY'=>$ICYY,'ICZZ'=>$ICZZ,'I1'=>$I1,'I2'=>$I2,'I3'=>$I3));
         }
   
   
   /*------------------------------------------------------------------------------------------------------------- */      

    /* SHOW CLEO personalized */
       public function showCleopersoAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $target_dir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current';
        $outputdir=$target_dir."/Results";

               // Open the text file
	             // Input Values :
                 $bmd = fopen("$outputdir/Input_values.txt", "r");
                 $BADU= fgets($bmd);
                 $MBMD= fgets($bmd);
                 $SD=fgets($bmd);
                 fclose($bmd);
          
                 // Phantom Values :
                $phan = fopen("$outputdir/Phantom1.txt", "r");
                $HU1=fgets($phan,5);
                $HA1=fgets($phan,8);
                
                $HU2=fgets($phan,7);
                $HA2=fgets($phan,10);
                
                fgets($phan);
              
                $HU3=fgets($phan,10);
                $HA3=fgets($phan,13);
                
              
                $HU4=fgets($phan,10);
                $HA4=fgets($phan,13);
                
                $HU5=fgets($phan,10);
                $HA5=fgets($phan,14);
                
                $HU6=fgets($phan,10);
                $HA6=fgets($phan,14);
                fclose($phan);
                
               //BMD2.txt
                $bmd2=fopen("$outputdir/BMD.txt","r");
               
                $BMC=fgets($bmd2);
                $BMD=fgets($bmd2);
                $TScor=fgets($bmd2);
                
                fclose($bmd2);
                
                // Results1.txt
                
                $Res = fopen("$outputdir/Results1.txt", "r");
                $BVolum=fgets($Res);
                $TVolum=fgets($Res);
                $VFraction=fgets($Res);
                $Conn=fgets($Res);
                $TN=fgets($Res);
                $TthM=fgets($Res);
                $TthDev=fgets($Res);
                $TthMax=fgets($Res);
                
                $TspM=fgets($Res);
                $TspDev=fgets($Res);
                $TspMax=fgets($Res); 
                $DA="Decommenter les lignes 669 et 570 dans ServiceController.php apres l'integration de la new clio dans la MV";
                $tDA="Decommenter les lignes 619 et 620 dans ServiceController.php apres l'integration de la new clio dans la MV";
                //$DA=fgets($Res,8);
                //$tDA=fgets($Res,7);
                $FDim=fgets($Res);
                $R2=fgets($Res);         
                fclose($Res);
                 
                 // Results2.txt
                 $Res2=fopen("$outputdir/Results2.txt", "r");
                 $BCODE=fgets($Res2);
                 $CSA=fgets($Res2);
                 $XCENT=fgets($Res2);
                 $YCENT=fgets($Res2);
                 $DENS=fgets($Res2);
                 $THETA=fgets($Res2);
                 $FMIN=fgets($Res2);
                 $FMAX=fgets($Res2);
                 $FANGLE=fgets($Res2);
                 $PERI=fgets($Res2);
                 $MEANTH=fgets($Res2);
                 fclose($Res2);
                 
                 // Results3.txt
                 $Res3=fopen("$outputdir/Results3.txt", "r");
                 $XC=fgets($Res3);
                 $YC=fgets($Res3);
                 $ZC=fgets($Res3);
                 $VOLL=fgets($Res3);
                 $MASS=fgets($Res3);
                 $ICXX=fgets($Res3);
                 $ICYY=fgets($Res3);
                 $ICZZ=fgets($Res3);
                 $I1=fgets($Res3);
                 $I2=fgets($Res3);
                 $I3=fgets($Res3);
                 fclose($Res3);

                
                return $this->render('EnterfaceServiceBundle:Services:affichecleoperso.html.twig', array('works' =>true,'BADU'=>$BADU, 'MBMD'=> $MBMD,
    'SD'=> $SD,'HU1'=> $HU1,'HA1'=> $HA1,'HU2'=> $HU2,'HA2'=> $HA2,'HU3'=> $HU3,'HA3'=> $HA3,'HU4'=> $HU4,'HA4'=> $HA4,'HU5'=> $HU5,'HA5'=> $HA5,'HU6'=> $HU6,'HA6'=> $HA6, 'BVolum'=> $BVolum,'TVolum'=> $TVolum, 'VFraction'=>$VFraction,'BMC'=>$BMC,'BMD'=>$BMD,'TScor'=>$TScor,'FDim'=>$FDim,
    'R2'=>$R2, 'Conn'=>$Conn,'TN'=>$TN, 'TthM'=>$TthM, 'TthDev'=>$TthDev, 'TthMax'=> $TthMax, 'TspM'=> $TspM, 'TspDev'=> $TspDev, 'TspMax'=>$TspMax,'DA'=>$DA,'tDA'=>$tDA, 'BCODE'=>$BCODE,'CSA'=>$CSA,'XCENT'=>$XCENT,'YCENT'=>$YCENT,'DENS'=>$DENS,'THETA'=>$THETA,'FMIN'=>$FMIN,'FMAX'=>$FMAX,'FANGLE'=>$FANGLE,'PERI'=>$PERI,'MEANTH'=>$MEANTH,'XC'=>$XC,'YC'=>$YC,'ZC'=>$ZC,'VOLL'=>$VOLL,'MASS'=>$MASS,'ICXX'=>$ICXX,'ICYY'=>$ICYY,'ICZZ'=>$ICZZ,'I1'=>$I1,'I2'=>$I2,'I3'=>$I3));
         }
   
   
   /*----------------------------------------------------------------------------------------------------------------- */      


    public function VertebraSegmentationAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if($user->getCredit()<2){
            
            $this->get('session')->getFlashBag()->add('info', 'Please buy credits'); 
            return $this->container->get('router')->generate('fos_user_profile_show');
        }
        return $this->render('EnterfaceServiceBundle:Services:VertebraSegmentation.html.twig', array());
    }


    
    public function CardiotrackingAction(){
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return $this->render('EnterfaceServiceBundle:Services:Cardiotracking.html.twig', array('works' => true));
    }
     
    public function TrackingtreatmentAction(){
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $ftp_user_name=$this->container->getParameter('windows_user');
        $ftp_user_pass=$this->container->getParameter('windows_password');
        $ftp_host=$this->container->getParameter('windows_host');
        
          $nom_fichier = $_FILES['fichier']['tmp_name'];
          
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
        }
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';

          $target_dir = $outputdir;
	  $target_file = $target_dir ."/2.mov";
          $uploadOk = 1 ; // ??? ça fait quoi ?
    
          if(move_uploaded_file($nom_fichier, $target_file ))
          {
                  $a=1;  // ??? ça fait quoi ?
          }
         // exec("sudo /usr/local/bin/docker_cardiotracking $target_dir");
        exec("sudo /usr/local/bin/cardiotracking $target_dir",$output);  
	return new RedirectResponse($this->container->get('router')->generate('enterface_service_showtracking'));
    }
    public function showTrackingAction(){
         //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
         return $this->render('EnterfaceServiceBundle:Services:Trackingtreatment.html.twig', array('works' => true));
    }


    
    public function segmentationAction() {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $nom_fichier = $_FILES['fichier']['tmp_name'];
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
        }
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';

        $target_dir = $outputdir;
        $target_file = $target_dir ."/1.jpg";
        $uploadOk = 1;
        $sortie=$target_dir."/output";
        
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"&& $imageFileType != "gif" )
	{
		return $this->render('EnterfaceServiceBundle:Services:segmentation.html.twig', 
                        array('works' => false, 'msg' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
		$uploadOk = 0;
	}
        if(move_uploaded_file($nom_fichier, $target_file )){
            $a=1;
        }
        $myfile = fopen($target_dir ."/file.txt", "w");
        fwrite($myfile, "65 7 29");
        fclose($myfile);
                    
        exec("sudo /usr/local/bin/docker_vertebrae $target_dir", $output); 
        
        //Payement
                $credit=$user->getCredit();
                $credit=$credit-2;
                $user->setCredit($credit);
                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
         
        return new RedirectResponse($this->container->get('router')->generate('enterface_service_showvertebra'));
    }
    public function vertebrachangeAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
        }
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';

        $target_dir = $outputdir;
        $target_file = $target_dir ."/1.jpg";
        $myfile = fopen($target_dir ."/file.txt", "w");
        $a=$_POST["edge"];
        $b=$_POST["kernel"];
        $c=$_POST["low"];
        fwrite($myfile, $a." ".$b." ".$c);
        fclose($myfile);
        exec("sudo /usr/local/bin/docker_vertebrae $target_dir", $output); 
        //return "test";
        //return new RedirectResponse($this->container->get('router')->generate('enterface_service_showvertebra'));
        return $this->render('EnterfaceServiceBundle:Services:affichage.html.twig', array('works' => true,'a'=>$a, 'b'=> $b,'c'=> $c));
    }
    
    public function showVertebraAction()    {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return $this->render('EnterfaceServiceBundle:Services:segmentation.html.twig', array('works' => true));
    }
    
    public function sendmessageAction()    {
        
         //Recuperation requete
        $request = $this->get('request');
        
        if( $request->getMethod() == 'POST' ) {
        //Recuperation des entrÃ©es du formulaire
        if($request->get('txtAddress2')==""){
        $subject=$request->get('txtSubject');
        $fromEmail=$this->container->getParameter('mailer_user');
        $toEmail=$this->container->getParameter('mailer_user'); //We send this mail to ourselves
        $body=$request->get('CommentContact');
         
        $body = "Name : " . $request->get('txtName') . "\n";
        $body .= "EMail : " . $request->get('txtEmail') . "\n";
        $body .= "Address : " . $request->get('txtAddress'). "\n";
        $body .= "PhoneNumber : " . $request->get('txtPhone'). "\n";
        $body .= "Content : " . $request->get('CommentContact');
        
         
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->get('mailer')->send($message);
        }
        
        }
        
        return new RedirectResponse($this->getRedirectionUrl());
    
    }

    protected function getRedirectionUrl()    {
        return $this->container->get('router')->generate('enterface_mail_homepage');
    }
    
    public function saveResultsAction($name)    {
        //Check authentification
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        //Creation of new Results
        $result=new Results();
        //$resultId=$result->getId();
        $result->setApplicationname($name);
        $result->setUser($user);
        $result->setIpp('TEST_IPP');
        $result->setImageResultType('.txt');
        $result->setUrldata('temp');
        $result->setUrlkey('temp');
        $result->setUrlkeyslave('temp');
        $result->seturldirectory('temp');

        
        //Load Doctrine Service 
        $em=$this->getDoctrine()->getManager();
        
        //Save
        $em->persist($result);
        $em->flush();
        
        $resultId=$result->getId();
        
        //Directories arbo
        $userid=$user->getId();
        
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results')){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results');
        }
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        
        $resultdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/'.$resultId;
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/'.$resultId)){
            mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/'.$resultId);
        }
        
        rename ($outputdir,"$resultdir/Results");
        mkdir ($outputdir);
        
        //Execute crypt script
        exec("sudo /usr/local/bin/runcrypto $resultdir");

        //Complete information for results
        $result->setUrldata($resultdir.'/Results.tar.gz.asc');
        $result->setUrlkey($resultdir.'/master.txt.gpg');
        $result->setUrlkeyslave($resultdir.'/'.$userid.'.pub');
        $result->seturldirectory($resultdir);
        
        //Load Doctrine Service 
        $em=$this->getDoctrine()->getManager();
        
        //Save
        $em->persist($result);
        $em->flush();
        
        return new RedirectResponse($this->container->get('router')->generate('fos_user_profile_show'));
    }

    public function restoreResultsAction($id)    {
        //Check authentification
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }        
        //Load Doctrine Service 
        $em=$this->getDoctrine()->getManager();
        
        //Restore
        $resultsRepository = $em->getRepository('Enterface\UserBundle\Entity\Results');
        $result = $resultsRepository->find($id);
        
        //Directories arbo
        $userid=$user->getId();

        function rrmdir($dir) { 
            if (is_dir($dir)) { 
                $objects = scandir($dir); 
                foreach ($objects as $object) { 
                  if ($object != "." && $object != "..") { 
                    if (is_dir($dir."/".$object)) {
                        rrmdir($dir."/".$object); 
                    }
                    else {
                        unlink($dir."/".$object); 
                    }
                  } 
                }
                rmdir($dir); 
            } 
        }
        
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/');
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId())){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId());
        }
        if(!is_dir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current')){
           mkdir('/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current');
        }
        $resultdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/'.$id;
        $outputdir='/var/www/symfv2/Enterface16/UserResults/'.$user->getId().'/Current/Results';
        rrmdir($outputdir);
       
        //Execute crypt script
        exec("sudo /usr/local/bin/rundecrypt $resultdir");
        rename ("$resultdir/Results","$outputdir");
        
        $appname=$result->getApplicationname();
        
        switch($appname){
            case 'Osteoporosis' : return new RedirectResponse($this->container->get('router')->generate('enterface_service_showcleo'));
            case 'Scoliosis' : return new RedirectResponse($this->container->get('router')->generate('enterface_service_showvertebra'));
            case 'Toolbox' : return new RedirectResponse($this->container->get('router')->generate('enterface_service_showtracking'));
        }
        return new RedirectResponse($this->container->get('router')->generate('fos_user_profile_show'));
    } 
}