<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelPedidoChamado
 *
 * @ORM\Table(name="rel_pedido_chamado", indexes={@ORM\Index(name="IDX_E05E0522E2DBA323", columns={"id_pedido"}), @ORM\Index(name="IDX_E05E0522E59FAF1F", columns={"id_chamado"})})
 * @ORM\Entity
 */
class RelPedidoChamado
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="rel_pedido_chamado_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Chamado
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Chamado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_chamado", referencedColumnName="id")
     * })
     */
    private $idChamado;

    /**
     * @var \AppBundle\Entity\Pedido
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pedido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pedido", referencedColumnName="id")
     * })
     */
    private $idPedido;


}

