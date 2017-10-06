<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Cliente;
use Doctrine\ORM\EntityManager;

class ClienteController extends Controller
{
    /**
     * @Route("/cliente", name="clientes")
     */
    public function ListarAction(Request $request)
    {
        $clienteRepo = $this->getDoctrine()->getRepository(Cliente::class);
        
        $clientes = $clienteRepo->findAll();
        var_dump($clientes);die();
        // replace this example code with whatever you need
        return $this->render('home.html.twig', [
            'title' => "SAC"
        ]);
    }
}
