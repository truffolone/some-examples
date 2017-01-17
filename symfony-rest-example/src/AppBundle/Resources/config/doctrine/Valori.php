<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Valori
 *
 * @ORM\Table(name="valori", indexes={@ORM\Index(name="IDX_D3F717ACEBF8FCEE", columns={"cardid"})})
 * @ORM\Entity
 */
class Valori
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="valori_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="regsellprice", type="float", precision=10, scale=0, nullable=true)
     */
    private $regsellprice;

    /**
     * @var float
     *
     * @ORM\Column(name="foilsellprice", type="float", precision=10, scale=0, nullable=true)
     */
    private $foilsellprice;

    /**
     * @var float
     *
     * @ORM\Column(name="regbuyprice", type="float", precision=10, scale=0, nullable=true)
     */
    private $regbuyprice;

    /**
     * @var float
     *
     * @ORM\Column(name="foilbuyprice", type="float", precision=10, scale=0, nullable=true)
     */
    private $foilbuyprice;

    /**
     * @var integer
     *
     * @ORM\Column(name="maxregbuyqt", type="integer", nullable=false)
     */
    private $maxregbuyqt = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="maxfoilbuyqt", type="integer", nullable=false)
     */
    private $maxfoilbuyqt = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert", type="datetimetz", nullable=true)
     */
    private $insert;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdate", type="datetimetz", nullable=true)
     */
    private $lastupdate;

    /**
     * @var \Carte
     *
     * @ORM\ManyToOne(targetEntity="Carte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardid", referencedColumnName="id")
     * })
     */
    private $cardid;


}

