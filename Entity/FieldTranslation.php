<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 02.11.15
 * Time: 13:38
 */

namespace Symbio\OrangeGate\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class FieldTranslation
 * @package Symbio\OrangeGate\FormBundle\Entity
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="form__field__translation", uniqueConstraints={
 *    @ORM\UniqueConstraint(name="form_field_translation_unique_idx", columns={"locale", "object_id"})
 * })
 */
class FieldTranslation extends AbstractTranslation
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $locale
     *
     * @ORM\Column(type="string", length=8)
     */
    protected $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $object;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @var null|string
     * @ORM\Column(type="text", length=255, nullable=true, name="validation_message")
     */
    protected $validationMessage;

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
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     * @return $this
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getValidationMessage()
    {
        return $this->validationMessage;
    }

    /**
     * @param null|string $validation_message
     * @return $this
     */
    public function setValidationMessage($validation_message)
    {
        $this->validationMessage = $validation_message;
        return $this;
    }


}