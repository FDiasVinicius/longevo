<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Pedido;
use Doctrine\ORM\EntityManager;
use AppBundle\Helper\Pedido as PedidoHelper;
use AppBundle\Entity\Cliente;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PedidoController extends Controller
{
    const PAGINACAO_LIMITE_PAGINA = 5;
    
    /**
     * @Route("/pedido/{page}", name="pedidos", requirements={"page": "\d+"}, defaults={"page" = 1})
     */
    public function ListarAction(Request $request, $page)
    {
        $page = isset($page)?$page:1;
        $filtroEmail = $request->request->has('email')?htmlspecialchars($request->request->get('email')):null;
        
        $pedidoRepo = $this->getDoctrine()->getRepository(Pedido::class);
        $pedidos = $pedidoRepo->findBy([], ["dataCriacao" => "desc"]);
        
        $qb = $this->getDoctrine()->getRepository(Pedido::class)->createQueryBuilder('pedido');
        $qb->select('pedido')->innerJoin(Cliente::class, 'cliente', Join::WITH, 'pedido.idCliente = cliente.id');
        
        if (!empty($filtroEmail)) {
            $qb->where('cliente.email = :email')->setParameter('email', $filtroEmail);
        }
        
        $qb->orderBy('pedido.dataCriacao', 'DESC')
        ->setFirstResult(($page-1)*self::PAGINACAO_LIMITE_PAGINA)
        ->setMaxResults(self::PAGINACAO_LIMITE_PAGINA);
        
        $query = $qb->getQuery();
        
        $pagination = new Paginator($query, $fetchJoinCollection = true);
        
        $relHeader = ["Código", "Cliente", "Estado",  "Data Criação"];
        $relBody = [];
        
        $registros = count($pagination);
        
        foreach ($pagination as $pedido) {
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
                "callback" => "novoPedido()"
                
            ]
        ];
        
        $filtros = [
            "fields" =>[
                ['name' => 'email', 'placeholder' => 'E-mail', 'value' => $filtroEmail]
            ]
        ];
        
        $topbar = ["btns"=>$btns, "filtro" => $filtros];
        
        $nPaginas = ceil($registros/self::PAGINACAO_LIMITE_PAGINA);
        $paginacao = [
            "nPaginas" => ($nPaginas==0?1:$nPaginas),
            "paginaAtual" => $page,
            "action" => "/chamado"
        ];
        
        return $this->render('Pedido/listar.html.twig', [
            'title' => "SAC - Pedidos", 
            "menu" => ["current"=>"pedidos"],
            "table" => ["header" => $relHeader, "body" => $relBody],
            "topBar" => $topbar,
            "paginacao" => $paginacao
        ]);
    }
    
    /**
     * @Route("/pedido/novo", name="pedido.cadastrar")
     */
    public function CadastrarAction(Request $request)
    {
        $clienteRepo = $this->getDoctrine()->getRepository(Cliente::class);
        $clientes = $clienteRepo->findBy([], ["nome" => "asc"]);
        
        $select = [];
        
        foreach ($clientes as $cliente) {
            $select[] = ["id" => $cliente->getId(), "nome" => $cliente->getNome()];    
        }
        
        return $this->render('Pedido/cadastro.html.twig', [
            'title' => "SAC - Novo Pedido",
            "menu" => ["current"=>"pedidos"],
            "clientes" => $select
        ]);
    }
    
    /**
     * @Route("/pedido/salvar", name="pedido.salvar")
     * @Method({"POST"})
     */
    public function SalvarAction(Request $request)
    {
        $clienteId = intval($request->request->get("cliente"));
        
        if ($clienteId <= 0) {
            throw new \Exception("Cliente inválido!");
        }
        
        $doctrine = $this->getDoctrine();
        
        $clienteRepo = $doctrine->getRepository(Cliente::class);
        $cliente = $clienteRepo->findOneBy(["id" => $clienteId]);
        
        if (empty($cliente)) {
            throw new \Exception("Cliente não encontrado!");
        }
        
        $pedido = new Pedido();
        $pedido->setIdCliente($cliente)
        ->setStatus(PedidoHelper::STATUS_PENDENTE)
        ->setDataCriacao(new \DateTime());
        
        $manager = $doctrine->getManager();
        $manager->persist($pedido);
        $manager->flush();
        
        return $this->render('info.html.twig', [
            'title' => "SAC - Pedido cadastrado",
            "classe" => "success",
            "msg" => "Pedido cadastrado com <strong>sucesso</strong>!",
            "redirect" => "/pedido"
        ]);
    }
}
