<?php



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


}

