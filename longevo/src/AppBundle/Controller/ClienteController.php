<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Cliente;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Helper\Cliente as ClienteHelper;

class ClienteController extends Controller
{
    /**
     * @Route("/cliente", name="clientes")
     */
    public function ListarAction(Request $request)
    {
        $clienteRepo = $this->getDoctrine()->getRepository(Cliente::class);
        
        $clientes = $clienteRepo->findBy([], ["nome" => "asc"]);
        
        $relHeader = ["Código", "Nome", "E-mail"];
        $relBody = [];
        
        foreach ($clientes as $cliente) {
            $relBody[] = [
                $cliente->getId(),
                $cliente->getNome(),
                $cliente->getEmail()
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
        
        return $this->render('Cliente/listar.html.twig', [
            'title' => "SAC - Clientes", 
            "menu" => ["current"=>"clientes"],
            "table" => ["header" => $relHeader, "body" => $relBody],
            "topBar" => $topbar
        ]);
    }
    
    /**
     * @Route("/cliente/novo", name="cadastrar.cliente")
     */
    public function CadastrarAction(Request $request)
    {
        return $this->render('Cliente/cadastro.html.twig', [
            'title' => "SAC - Novo Cliente",
            "menu" => ["current"=>"clientes"]
        ]);
    }
    
    /**
     * @Route("/cliente/salvar", name="salvar.cliente")
     * @Method({"POST"})
     */
    public function SalvarAction(Request $request)
    {
        $safeForm = ClienteHelper::getFormCadastro($request);
        $db = $this->getDoctrine()->getManager();
        $cliente = new Cliente();
        $cliente->setNome($safeForm['nome']);
        $cliente->SetEmail($safeForm['email']);
        
        $db->persist($cliente);
        $db->flush();
        return $this->render('info.html.twig', [
            'title' => "SAC - Usuario cadastrado",
            "classe" => "success",
            "msg" => "Usuário cadastrado com <strong>sucesso</strong>!",
            "redirect" => "/cliente"
        ]);
    }
}
