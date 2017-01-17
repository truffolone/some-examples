<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Carte
 *
 * @ORM\Table(name="carte")
 * @ORM\Entity
 */
class Carte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="carte_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="text", nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="esp", type="text", nullable=false)
     */
    private $esp;

    /**
     * @var string
     *
     * @ORM\Column(name="rarity", type="text", nullable=false)
     */
    private $rarity = 'c';



    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Carte
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set esp
     *
     * @param string $esp
     *
     * @return Carte
     */
    public function setEsp($esp)
    {
        $this->esp = $esp;

        return $this;
    }

    /**
     * Get esp
     *
     * @return string
     */
    public function getEsp()
    {
        return $this->esp;
    }

    /**
     * Set rarity
     *
     * @param string $rarity
     *
     * @return Carte
     */
    public function setRarity($rarity)
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Get rarity
     *
     * @return string
     */
    public function getRarity()
    {
        return $this->rarity;
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
}
