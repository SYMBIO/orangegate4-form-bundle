<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 27.10.15
 * Time: 16:16
 */

namespace Symbio\OrangeGate\FormBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symbio\OrangeGate\FormBundle\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="form__recipient")
 */
class Recipient
{
    use TimestampableEntity;

    /**
     * @var integer $id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="fields")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    protected $form;

    /**
     * Recipient constructor.
     * @param $name
     * @param $email
     * @param $form
     * @param int $id
     */
    public function __construct($name = null, $email = null, $form = null, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->form = $form;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    public function __toString()
    {
        return $this->getName() . '(' . $this->getEmail() . ')';
    }
}