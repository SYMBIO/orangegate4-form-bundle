<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 12.11.15
 * Time: 15:05
 */

namespace Symbio\OrangeGate\FormBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symbio\OrangeGate\FormBundle\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="form__choice")
 */
class Choice
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
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $label;

    /**
     * @ORM\Column(type="text", length=255, nullable=false)
     */
    protected $value;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $priority;

    /**
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="choices")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", nullable=false)
     */
    protected $field;

    /**
     * @ORM\OneToMany(targetEntity="ChoiceTranslation", mappedBy="object", indexBy="locale", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $translations;


    /**
     * Choice constructor.
     * @param int $id
     * @param $label
     * @param $value
     * @param int $priority
     * @param $field
     */
    public function __construct($label = null, $value = null, $priority = null, $field = null, $id = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->priority = $priority;
        $this->field = $field;

        $this->translations = new ArrayCollection();
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return ChoiceTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param ChoiceTranslation[] $translations
     * @return $this
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * @param AbstractTranslation $translation
     * @return $this
     */
    public function addTranslation(AbstractTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations->set($translation->getLocale(), $translation);
        }
        return $this;
    }

    /**
     * @param AbstractTranslation $translation
     * @return $this
     */
    public function removeTranslation(AbstractTranslation $translation)
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }
        return $this;
    }

    /**
     * @param $locale
     * @return null|AbstractTranslation
     */
    public function getTranslation($locale)
    {
        if (isset($this->translations[$locale])) {
            return $this->translations[$locale];
        }

        return null;
    }
}