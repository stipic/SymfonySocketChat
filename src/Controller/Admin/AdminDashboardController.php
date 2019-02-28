<?php
namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminDashboardController extends Controller
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function dashboard()
    {      
        return new Response('Admin DASHBOARD');
    }
}