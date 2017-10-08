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
                return "Pendente";
            case self::STATUS_PROCESSANDO:
                return "Processando";
            case self::STATUS_ENTREGUE:
                return "Entregue";
            case self::STATUS_ATRASADO:
                return "Atrasado";
        }
    }
}

