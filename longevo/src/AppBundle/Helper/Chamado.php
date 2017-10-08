<?php
namespace AppBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

class Chamado
{
    /*
     * Status de chamado pendente
     * @var int
     */
    const STATUS_PENDENTE = 1;
    /*
     * Status de chamado resolvido
     * @var int
     */
    const STATUS_RESOLVIDO = 2;
    /*
     * Status de chamado cancelado
     * @var int
     */
    const STATUS_CANCELADO = 3;
    
    /**
     * retorna uma descrição de estado a partir do id dele
     * @param int $status
     * @return string
     */
    static public function statusDescribe($status)
    {
        switch($status) {
            case self::STATUS_PENDENTE:
                return "<span class=\"label label-warning\">Pendente</span>";
            case self::STATUS_RESOLVIDO:
                return "<span class=\"label label-success\">Resolvido</span>";
            case self::STATUS_CANCELADO:
                return "<span class=\"label label-default\">Cancelado</span>";
        }
    }
    
    /**
     * Valida form de cadastro e retorna dados limpos
     * @param Request
     * @return array
     */
    static public function getFormCadastro(Request $request)
    {
        $nome = $request->request->get('nome');
        $email = $request->request->get('email');
        $titulo = htmlspecialchars($request->request->get('titulo'));
        $descricao = htmlspecialchars($request->request->get('descricao'));
        $pedido = intval($request->request->get('pedido'));
        
        $userData = Cliente::getFormCadastro($request);
        
        if (empty($nome)) {
            throw new \Exception("Titulo não informado.");
        }
        if (strlen($titulo) > 100) {
            throw new \Exception("Titulo do chamado ultrapassou o limite de 100 caracteres");
        }
        if ($pedido <= 0) {
            throw new \Exception("Informe um numero de pedido valido.");
        }
        
        return [
            'nome' => $userData['nome'],
            'email' => $userData['email'],
            'titulo'=> $titulo,
            'descricao' => $descricao,
            'pedido' => $pedido
        ];
    }
}

