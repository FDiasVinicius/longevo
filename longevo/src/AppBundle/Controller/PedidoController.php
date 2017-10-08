<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Pedido;
use Doctrine\ORM\EntityManager;
use AppBundle\Helper\Pedido as PedidoHelper;

class PedidoController extends Controller
{
    /**
     * @Route("/pedido", name="pedidos")
     */
    public function ListarAction(Request $request)
    {
        $pedidoRepo = $this->getDoctrine()->getRepository(Pedido::class);
        
        $pedidos = $pedidoRepo->findBy([], ["dataCriacao" => "desc"]);
        
        $relHeader = ["Código", "Cliente", "Estado",  "Data Criação"];
        $relBody = [];
        
        foreach ($pedidos as $pedido) {
            $relBody[] = [
                $pedido->getId(),
                $pedido->getIdCliente()->getNome(),
                PedidoHelper::statusDescribe($pedido->getStatus()),
                $pedido->getDataCriacao()->format('d/m/Y \à\s H:i:s')
            ];
        }
        
        $btns = [
            [
                "classe" => "primary",
                "icon" => "plus-sign",
                "txt" => "Novo",
                "callback" => "novoCliente()"
                
            ]
        ];
        
        $topbar = ["btns"=>$btns];
        
        return $this->render('Pedido/listar.html.twig', [
            'title' => "SAC - Pedidos", 
            "menu" => ["current"=>"pedidos"],
            "table" => ["header" => $relHeader, "body" => $relBody],
            "topBar" => $topbar
        ]);
    }
}
