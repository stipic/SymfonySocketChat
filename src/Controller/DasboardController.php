<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DasboardController extends Controller
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function dasboard()
    {      
        // Ovo dohvaća sve od nekog usera, npr. list conversation-a, opce podatke o useru
        $userConversations = $this->getUser()->getConversations()->getValues();

        $userRepository = $this->get('doctrine')->getManager()->getRepository('App:User');
        $users = $userRepository->findAll();

        $this->get('craue_config')->set('register', 0);

        return $this->render('page/dashboard.html.twig', [
            'conversations' => $userConversations,
            'users' => $users
        ]);
    }
}