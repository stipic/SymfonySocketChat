<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function dashboard()
    {      
        // dohvati sve razgovore od usera i random odaberi jedan i redirektaj na njega.
        
        $userConversations = $this->getUser()->getConversations()->getValues();
        $conversation = array_rand($userConversations);
        $url = $this->generateUrl(
            'app_conversation',
            ['id' => $userConversations[$conversation]->getId()]
        );

        return new RedirectResponse($url);
    }
}