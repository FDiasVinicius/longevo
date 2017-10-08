<?php
namespace AppBundle\Helper;

class Pedido
{
    /*
     * Status de pedido pendente
     * @var int
     */
    const STATUS_PENDENTE = 1;
    /*
     * Status de pedido processando
     * @var int
     */
    const STATUS_PROCESSANDO = 2;
    /*
     * Status de pedido entregue
     * @var int
     */
    const STATUS_ENTREGUE = 3;
    /*
     * Status de pedido atrasado
     * @var int
     */
    const STATUS_ATRASADO = 4;
    
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
            case self::STATUS_PROCESSANDO:
                return "<span class=\"label label-primary\">Processando</span>";
            case self::STATUS_ENTREGUE:
                return "<span class=\"label label-success\">Entregue</span>";
            case self::STATUS_ATRASADO:
                return "<span class=\"label label-danger\">Atrasado</span>";
        }
    }
}

