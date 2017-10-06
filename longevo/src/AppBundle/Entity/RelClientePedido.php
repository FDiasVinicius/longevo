<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelClientePedido
 *
 * @ORM\Table(name="rel_cliente_pedido", indexes={@ORM\Index(name="IDX_C2CA71A62A813255", columns={"id_cliente"}), @ORM\Index(name="IDX_C2CA71A6E2DBA323", columns={"id_pedido"})})
 * @ORM\Entity
 */
class RelClientePedido
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="rel_cliente_pedido_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Pedido
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pedido", referencedColumnName="id")
     * })
     */
    private $idPedido;

    /**
     * @var \AppBundle\Entity\Cliente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cliente", referencedColumnName="id")
     * })
     */
    private $idCliente;


}

