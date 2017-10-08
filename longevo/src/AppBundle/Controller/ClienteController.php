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
use Doctrine\ORM\Tools\Pagination\Paginator;

class ClienteController extends Controller
{
    const PAGINACAO_LIMITE_PAGINA = 5;
    
    /**
     * @Route("/cliente/{page}", name="clientes", requirements={"page": "\d+"}, defaults={"page" = 1})
     */
    public function ListarAction(Request $request, $page)
    {
        $page = isset($page)?$page:1;
        $filtroEmail = $request->request->has('email')?htmlspecialchars($request->request->get('email')):null;
        
        $qb = $this->getDoctrine()->getRepository(Cliente::class)->createQueryBuilder('cliente');
        $qb->select('cliente');
        
        if (!empty($filtroEmail)) {
            $qb->where('cliente.email = :email')->setParameter('email', $filtroEmail);
        }
        
        $qb->orderBy('cliente.nome', 'ASC')
        ->setFirstResult(($page-1)*self::PAGINACAO_LIMITE_PAGINA)
        ->setMaxResults(self::PAGINACAO_LIMITE_PAGINA);
        
        $query = $qb->getQuery();
        
        $pagination = new Paginator($query, $fetchJoinCollection = true);
        
        $relHeader = ["Código", "Nome", "E-mail"];
        $relBody = [];
        
        $registros = count($pagination);
        
        foreach ($pagination as $cliente) {
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
        
        $filtros = [
            "fields" =>[
                ['name' => 'email', 'placeholder' => 'E-mail', 'value' => $filtroEmail]
            ]
        ];
        
        $topbar = ["btns"=>$btns, 'filtro' => $filtros];
        
        $topbar = ["btns"=>$btns, "filtro" => $filtros];
        
        $nPaginas = ceil($registros/self::PAGINACAO_LIMITE_PAGINA);
        $paginacao = [
            "nPaginas" => ($nPaginas==0?1:$nPaginas),
            "paginaAtual" => $page,
            "action" => "/cliente"
        ];
        
        return $this->render('Cliente/listar.html.twig', [
            'title' => "SAC - Clientes", 
            "menu" => ["current"=>"clientes"],
            "table" => ["header" => $relHeader, "body" => $relBody],
            "topBar" => $topbar,
            "paginacao" => $paginacao
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
