<?php

namespace AppBundle\Entity;

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



    /**
     * Set regsellprice
     *
     * @param float $regsellprice
     *
     * @return Valori
     */
    public function setRegsellprice($regsellprice)
    {
        $this->regsellprice = $regsellprice;

        return $this;
    }

    /**
     * Get regsellprice
     *
     * @return float
     */
    public function getRegsellprice()
    {
        return $this->regsellprice;
    }

    /**
     * Set foilsellprice
     *
     * @param float $foilsellprice
     *
     * @return Valori
     */
    public function setFoilsellprice($foilsellprice)
    {
        $this->foilsellprice = $foilsellprice;

        return $this;
    }

    /**
     * Get foilsellprice
     *
     * @return float
     */
    public function getFoilsellprice()
    {
        return $this->foilsellprice;
    }

    /**
     * Set regbuyprice
     *
     * @param float $regbuyprice
     *
     * @return Valori
     */
    public function setRegbuyprice($regbuyprice)
    {
        $this->regbuyprice = $regbuyprice;

        return $this;
    }

    /**
     * Get regbuyprice
     *
     * @return float
     */
    public function getRegbuyprice()
    {
        return $this->regbuyprice;
    }

    /**
     * Set foilbuyprice
     *
     * @param float $foilbuyprice
     *
     * @return Valori
     */
    public function setFoilbuyprice($foilbuyprice)
    {
        $this->foilbuyprice = $foilbuyprice;

        return $this;
    }

    /**
     * Get foilbuyprice
     *
     * @return float
     */
    public function getFoilbuyprice()
    {
        return $this->foilbuyprice;
    }

    /**
     * Set maxregbuyqt
     *
     * @param integer $maxregbuyqt
     *
     * @return Valori
     */
    public function setMaxregbuyqt($maxregbuyqt)
    {
        $this->maxregbuyqt = $maxregbuyqt;

        return $this;
    }

    /**
     * Get maxregbuyqt
     *
     * @return integer
     */
    public function getMaxregbuyqt()
    {
        return $this->maxregbuyqt;
    }

    /**
     * Set maxfoilbuyqt
     *
     * @param integer $maxfoilbuyqt
     *
     * @return Valori
     */
    public function setMaxfoilbuyqt($maxfoilbuyqt)
    {
        $this->maxfoilbuyqt = $maxfoilbuyqt;

        return $this;
    }

    /**
     * Get maxfoilbuyqt
     *
     * @return integer
     */
    public function getMaxfoilbuyqt()
    {
        return $this->maxfoilbuyqt;
    }

    /**
     * Set insert
     *
     * @param \DateTime $insert
     *
     * @return Valori
     */
    public function setInsert($insert)
    {
        $this->insert = $insert;

        return $this;
    }

    /**
     * Get insert
     *
     * @return \DateTime
     */
    public function getInsert()
    {
        return $this->insert;
    }

    /**
     * Set lastupdate
     *
     * @param \DateTime $lastupdate
     *
     * @return Valori
     */
    public function setLastupdate($lastupdate)
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * Get lastupdate
     *
     * @return \DateTime
     */
    public function getLastupdate()
    {
        return $this->lastupdate;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cardid
     *
     * @param \AppBundle\Entity\Carte $cardid
     *
     * @return Valori
     */
    public function setCardid(\AppBundle\Entity\Carte $cardid = null)
    {
        $this->cardid = $cardid;

        return $this;
    }

    /**
     * Get cardid
     *
     * @return \AppBundle\Entity\Carte
     */
    public function getCardid()
    {
        return $this->cardid;
    }
}
