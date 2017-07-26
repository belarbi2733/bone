<?php

namespace Enterface\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Enterface\UserBundle\Entity\Results;
use Enterface\UserBundle\Entity\Transactions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(false);
        $user->setLastLogin(new \DateTime());

        $this->container->get('fos_user.user_manager')->updateUser($user);
        $response = new RedirectResponse($this->container->get('router')->generate('fos_user_registration_confirmed'));
        //$this->authenticateUser($user, $response);

        //send user confirmation to administrator
        $message = \Swift_Message::newInstance()
                ->setSubject($user->getUsername(). ' has registered')
                ->setFrom('medicalboneprocess@gmail.com')
                ->setTo('Sidi.MAHMOUDI@umons.ac.be')
                ->setBody($user->getUsername(). ' has registered')
            ;
        $this->get('mailer')->send($message);

        return $response;
    }


    /*public function connexionAction()
    {
        return $this->render('EnterfaceUserBundle:User:connexion.html.twig', array());
    }*/
    
      public function loginAction()
    {  
        $user= $this->getUser() ;
        return $this->render('FOSUserBundle:Security:login.html.twig', array('user'=>$user));
    }
    public function profileAction()
    {  
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        
        
        //Load Doctrine Service 
        $em=$this->getDoctrine()->getManager();
        
        
        
        //Access to repository of Results
        $resultsRepository = $em->getRepository('Enterface\UserBundle\Entity\Results');
        $listResults = $resultsRepository->findBy(
                array('user'=>$user),
                array('date'=>'desc'),
                null,
                0
                );
        
        return $this->render('EnterfaceUserBundle:Profile:show.html.twig', array('user'=>$user,'listResults' => $listResults));
    }

    public function ForumAction()
    {
        return $this->render('YosimitsoWorkingForumBundle:Forum:index.html.twig');
    }
    
    public function teamAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->query->get('id');
        return $this->render('EnterfaceUserBundle:Team:profiles.html.twig', array('id' => $id));
    }
    public function logoutAction()
    {
        //$request = Request::createFromGlobals();
        //$id = $request->query->get('id');
        $request = $this->getRequest();
        $session = $request->getSession();
        $session->remove();
        return $this->render('EnterfaceServiceBundle:Ressources:views:Default:index.html.twig', array());
    }
    
    public function deleteAction($id)
    {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        //Load Doctrine Service 
        $em=$this->getDoctrine()->getManager();
        $resultsRepository = $em->getRepository('Enterface\UserBundle\Entity\Results');
        //Check correspondance between id and user id
        $test = $resultsRepository->findBy(
                array('user'=>$user,'id'=>$id),
                array('date'=>'desc'),
                null,
                0
                );
        if (empty($test)) {
            return new RedirectResponse($this->getRedirectionUrl());
        }
        //Find result to remove
        $result = $resultsRepository->find($id);
        //Test if result exists before trying to delete it
        if (empty($result)) {
            return new RedirectResponse($this->getRedirectionUrl());
        }
        //Delete the File Results from server HDD
        $path=$result->getUrldata();
        
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

        $target_dir = $path=$result->geturldirectory();
        rrmdir($target_dir);
        
        //Delete from DB
        $em->remove($result);
        $em->flush(); 
        return new RedirectResponse($this->getRedirectionUrl());
    }
    
    /**
     * Generate the redirection url when editing is completed.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl()
    {
        return $this->container->get('router')->generate('fos_user_profile_show');
    }
    
    public function buyAction()
    {
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        //Recuperation requete
        $request = $this->get('request');
        
        if( $request->getMethod() == 'POST' ) {
            
            //Recuperation valeur de credit
            $credit=$request->get('buycredit');
            if($credit<=0){
                
                $this->get('session')->getFlashBag()->add('info', 'Enter a valid value for credits order');
            }
            else {
            $credits=$credit+ $user->getCredit();
            //Link PayPal/PaymenBundle
            $userpaypal=$this->container->getParameter('userpaypal');
            $password=$this->container->getParameter('password_paypal');
            $signature=$this->container->getParameter('signature_paypal');
            $params=array(
                'METHOD'=>'SetExpressCheckout',
                'VERSION'=>'74.0',
                'USER'=>$userpaypal,
                'SIGNATURE'=>$signature,
                'PWD'=>$password,
                'RETURNURL'=>'http://'.$_SERVER['HTTP_HOST'].'/buycheck/'.$credits,
                'CANCELURL'=>'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                'PAYMENTREQUEST_0_AMT'=>$credit,
                'PAYMENTREQUEST_0_CURRENCYCODE'=>'EUR'
            );
            $params= http_build_query($params);
            //var_dump($params);
            $endpoint='https://api-3t.sandbox.paypal.com/nvp';
            //Appeler l'URL
            $curl=curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL =>$endpoint,
                CURLOPT_POST =>1,
                CURLOPT_POSTFIELDS =>$params,
                CURLOPT_RETURNTRANSFER =>1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE => 1
            ));
            $response= curl_exec($curl); //Exécuter la requete
            $responseArray=array();
            parse_str($response, $responseArray);
            var_dump($responseArray);                  //Checker si tout s'est bien passé
            //Checker si le curl et l'API ont fonctionné
            if(curl_errno($curl)){
                var_dump(curl_error($curl));
                curl_close($curl);
                //die();
                $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
                return new RedirectResponse($this->getRedirectionUrl());
            }else{
                if($responseArray['ACK']=='Success'){
                    //Si cela a fonctionné -> vers Paypal             
                    $paypal='https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' .$responseArray['TOKEN'];
                    return $this->redirect($paypal);
                    
                }else{
                    var_dump('responseArray');
                    //die();
                    $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
                    return new RedirectResponse($this->getRedirectionUrl());
                }
                
                curl_close($curl);
            }

            }
        }
        // This situation corresponds to unauthorized submit
        return new RedirectResponse($this->getRedirectionUrl());
    }
    
    public function buycheckAction($id){
        
        //Get current User and Check the access
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        
        // GetExpressCheckoutDetails
        $userpaypal=$this->container->getParameter('userpaypal');
        $password=$this->container->getParameter('password_paypal');
        $signature=$this->container->getParameter('signature_paypal');
        $params=array(
            'METHOD'=>'GetExpressCheckoutDetails',
            'VERSION'=>'74.0',
            'TOKEN' =>$_GET['token'],
            'USER'=>$userpaypal,
            'SIGNATURE'=>$signature,
            'PWD'=>$password
            
        );
        $params= http_build_query($params);//On reconstruit les paramètres sous forme tableau
        //var_dump($params);
        $endpoint='https://api-3t.sandbox.paypal.com/nvp';
        //Appeler l'URL
        $curl=curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>$endpoint,
            CURLOPT_POST =>1,
            CURLOPT_POSTFIELDS =>$params,
            CURLOPT_RETURNTRANSFER =>1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1
        ));
        $response= curl_exec($curl); //Exécuter la requete
        $responseArray=array();
        parse_str($response, $responseArray);

        //Checker si le curl et l'API ont fonctionné
        if(curl_errno($curl)){
            var_dump(curl_error($curl));
            curl_close($curl);
            //die();
            $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
            return new RedirectResponse($this->getRedirectionUrl());
        }else{
            if($responseArray['ACK']=='Success'){
             
                //Check if the payment has already been done ! If no, go to DoExpressCheckout
                if($responseArray['CHECKOUTSTATUS']=='PaymentActionCompleted'){
                    //Add money to DB credit
                    $user->setCredit($id);
                    //Load Doctrine Service 
                    $em=$this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('info', 'Credits added');  
                
                    //Think to save transaction ID
                    $paypal_transaction_id=$responseArray['PAYMENTINFO_0_TRANSACTIONID'];
                    $transaction = new Transactions();
                    $transaction->setUser($user);
                    $transaction->setTransactionID($paypal_transaction_id);
                
                    //Ajouter classe dans DB
                    $em=$this->getDoctrine()->getManager();
                    $em->persist($transaction);
                    $em->flush();
                
                    //Redirect to user profile
                    return new RedirectResponse($this->getRedirectionUrl());
                }
                //Check if the amt is correct: if not, fake transation
                if($id-$user->getCredit() != $responseArray['PAYMENTREQUEST_0_AMT'] ){
                    $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
                    return new RedirectResponse($this->getRedirectionUrl());
                }
                
                
            }else{
                var_dump($responseArray);
                //die();
                $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
                return new RedirectResponse($this->getRedirectionUrl());
            }
            curl_close($curl);
        }

        
        // DoExpressCheckout
        $params2=array(
            'METHOD'=>'DoExpressCheckoutPayment',
            'VERSION'=>'74.0',
            'TOKEN' =>$_GET['token'],
            'USER'=>$userpaypal,
            'SIGNATURE'=>$signature,
            'PWD'=>$password,
            'PAYERID'=>$_GET['PayerID'],
            'PAYMENTACTION'=>'Sale',
            'PAYMENTREQUEST_0_AMT'=>$responseArray['PAYMENTREQUEST_0_AMT'],
            'PAYMENTREQUEST_0_CURRENCYCODE'=>$responseArray['PAYMENTREQUEST_0_CURRENCYCODE']
            
        );
        $params2= http_build_query($params2);//On reconstruit les paramètres sous forme tableau
        //var_dump($params2);
        $endpoint='https://api-3t.sandbox.paypal.com/nvp';
        //Appeler l'URL
        $curl=curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>$endpoint,
            CURLOPT_POST =>1,
            CURLOPT_POSTFIELDS =>$params2,
            CURLOPT_RETURNTRANSFER =>1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1
        ));
        $response2= curl_exec($curl); //Exécuter la requete
        $responseArray2=array();
        parse_str($response2, $responseArray2);

        //Checker si le curl et l'API ont fonctionné
        if(curl_errno($curl)){
            var_dump(curl_error($curl));
            curl_close($curl);
            //die();
            $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
            return new RedirectResponse($this->getRedirectionUrl());
        }else{
            if($responseArray2['ACK']=='Success'){
                //Payment went well at this stage
     
                //Add money to DB credit
                $user->setCredit($id);
                //Load Doctrine Service 
                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Transaction succeeded : Credits added');  
                
                //Think to save transaction ID
                $paypal_transaction_id=$responseArray2['PAYMENTINFO_0_TRANSACTIONID'];
                $transaction = new Transactions();
                $transaction->setUser($user);
                $transaction->setTransactionID($paypal_transaction_id);
                
                //Ajouter classe dans DB
                $em=$this->getDoctrine()->getManager();
                $em->persist($transaction);
                $em->flush();
                
                //Redirect to user profile
                return new RedirectResponse($this->getRedirectionUrl());
            }else{
                var_dump($responseArray2);
                //die();
                $this->get('session')->getFlashBag()->add('info', 'Payment Canceled, please try later');  
                return new RedirectResponse($this->getRedirectionUrl());
            }

            curl_close($curl);
        }
     
        
    }
    

}
