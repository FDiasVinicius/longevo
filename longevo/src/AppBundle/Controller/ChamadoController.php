<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Chamado;
use Doctrine\ORM\EntityManager;
use AppBundle\Helper\Chamado  as ChamadoHelper;
use Symfony\Component\BrowserKit\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Pedido;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ChamadoController extends Controller
{
    const PAGINACAO_LIMITE_PAGINA = 5;
    
    /**
     * @Route("/chamado/{page}", name="chamados", requirements={"page": "\d+"}, defaults={"page" = 1}))
     */
    public function ListarAction(Request $request, $page)
    {
        $page = isset($page)?$page:1;
        $filtroPedido = $request->request->has('pedido')?intval($request->request->get('pedido')):null;
        $filtroEmail = $request->request->has('email')?$request->request->get('email'):null;
        
        $hasFiltro = (!is_null($filtroEmail) || !is_null($filtroPedido));
        
        $qb = $this->getDoctrine()->getRepository(Chamado::class)->createQueryBuilder('chamado');
        $qb->select('chamado')->innerJoin(Cliente::class, 'cliente', Join::WITH, 'chamado.idCliente = cliente.id');
        
        if ($hasFiltro) {
            if (is_null($filtroPedido)) {
                $filtroPedido = 0;
            }
            if (empty($filtroEmail)) {
                $filtroEmail = "";
            }
            
            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('cliente.email', ':email'),
                    $qb->expr()->eq('chamado.idPedido', ':idPedido')
                )
            )->setParameter('email', $filtroEmail)->setParameter('idPedido', $filtroPedido);
        }
        
        $qb->orderBy('cliente.nome', 'ASC')->setFirstResult($page)->setMaxResults(self::PAGINACAO_LIMITE_PAGINA);
        
        $query = $qb->getQuery();
        
        $pagination = new Paginator($query, $fetchJoinCollection = true);
        
        $relHeader = ["Código", "Titulo", "Cliente", "Pedido", "Estado", "Data de Abertura"];
        $relBody = [];
        
        $registros = count($pagination);
        
        foreach ($pagination as $chamado) {
            $auxBody = [
                $chamado->getId(),
                $chamado->getTitulo(),
                $chamado->getIdCliente()->getNome()
            ];
            
            $auxBody[] = $chamado->hasPedido()?$chamado->getIdPedido()->getId():"Não definido";
                
            $auxBody[] = ChamadoHelper::statusDescribe($chamado->getStatus());
                
            $auxBody[] = $chamado->getDataCriacao()->format('d/m/Y \à\s H:i:s');

            $relBody[] = $auxBody;
        }

        $btns = [
            [
                "classe" => "primary",
                "icon" => "plus-sign",
                "txt" => "Novo",
                "callback" => "novoChamado()"
                
            ]
        ];
        
        
        $filtros = [
            "fields" =>[
                ['name' => 'email', 'placeholder' => 'E-mail', 'value' => ""],
                ['name' => 'pedido', 'placeholder' => 'Pedido', 'value' => ""]
            ],
            "action" => "/chamado"
        ];
        
        $topbar = ["btns"=>$btns, 'filtro' => $filtros];
        
        $paginacao = [
            "nPaginas" => ceil($registros/self::PAGINACAO_LIMITE_PAGINA), 
            "paginaAtual" => $page, 
            "action" => "/chamado"
        ];
        
        return $this->render('Chamado/listar.html.twig', [
            'title' => "SAC - Chamados", 
            "menu" => ["current"=>"chamados"],
            "table" => ["header" => $relHeader, "body" => $relBody],
            "topBar" => $topbar,
            "paginacao" => $paginacao
        ]);
    }
    
    /**
     * @Route("/chamado/novo", name="cadastrar.chamado")
     */
    public function CadastrarAction(Request $request)
    {
        return $this->render('Chamado/cadastro.html.twig', [
            'title' => "SAC - Novo Chamado",
            "menu" => ["current"=>"chamads"]
        ]);
    }
    
    /**
     * @Route("/chamado/salvar", name="salvar.chamado")
     * @Method({"POST"})
     */
    public function SalvarAction(Request $request)
    {
        $safeForm = ChamadoHelper::getFormCadastro($request);
        
        $doctrine = $this->getDoctrine();
        $manager = $this->getDoctrine()->getManager();
        $clienteRepo = $doctrine->getRepository(Cliente::class);
        
        $cliente = $clienteRepo->findOneBy(["email" => $safeForm['email']]);

        if (empty($cliente)) {
            $cliente = new Cliente();
            $cliente->setNome($safeForm['nome']);
            $cliente->setEmail($safeForm['email']);
            $manager->persist($cliente);
            $manager->flush();
            $cliente = $clienteRepo->findOneBy(["email" => $safeForm['email']]);
            
            if (empty($cliente)) {
                throw new \Exception("Houve um erro ao salvar o usuario");
            } 
        } else if ($cliente->getNome() != $safeForm['nome']) {
            $cliente->setNome($safeForm['nome']);
            $manager->merge($cliente);
            $manager->flush();
        }
        
        $pedidoRepo = $doctrine->getRepository(Pedido::class);
        
        $pedido = $pedidoRepo->findOneBy(["id" => $safeForm['pedido']]);
        if (empty($pedido)) {
            throw new \Exception("O pedido informado não existe!");
        }
        
        $chamado = new Chamado();
        $chamado->setTitulo($safeForm['titulo']);
        $chamado->setStatus(ChamadoHelper::STATUS_PENDENTE);
        $chamado->setIdCliente($cliente);
        $chamado->setDescricao($safeForm['descricao']);
        $chamado->setIdPedido($pedido);
        $chamado->setDataCriacao(new \DateTime(strtotime("NOW")) );
        
        $manager->persist($chamado);
        $manager->flush();
        
        return $this->render('info.html.twig', [
            'title' => "SAC - Chamado aberto",
            "classe" => "success",
            "msg" => "Chamado aberto com <strong>sucesso</strong>!",
            "redirect" => "/chamado"
        ]);
    }
    
}
