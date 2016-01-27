<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 25.11.15
 * Time: 11:30
 */

namespace Symbio\OrangeGate\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symbio\OrangeGate\FormBundle\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SubmittedData
 * @package Symbio\OrangeGate\FormBundle\Entity
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="form__submitted_data")
 */
class SubmittedData
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
     * @ORM\Column(type="text", length=32, nullable=false, name="user_ip")
     */
    protected $userIp;

    /**
     * @ORM\Column(type="text", length=1024, nullable=false, name="user_agent")
     */
    protected $userAgent;

    /**
     * @ORM\Column(type="text", length=1024, nullable=false, name="form_data")
     */
    protected $formData;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="fields")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=false)
     */
    protected $form;

    /**
     * SubmittedData constructor.
     * @param int $id
     * @param $userIp
     * @param $userAgent
     * @param $formData
     * @param $form
     */
    public function __construct($id = null, $userIp = null, $userAgent = null, $formData = null, $form = null)
    {
        $this->id = $id;
        $this->userIp = $userIp;
        $this->userAgent = $userAgent;
        if (is_array($formData)) {
            $this->formData = json_encode($formData);
        } else {
            $this->formData = $formData;
        }
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
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @param mixed $userIp
     * @return $this
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param mixed $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @param mixed $formData
     * @return $this
     */
    public function setFormData($formData)
    {
        $this->formData = $formData;
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


}