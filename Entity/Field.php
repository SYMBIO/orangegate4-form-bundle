<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 27.10.15
 * Time: 16:15
 */

namespace Symbio\OrangeGate\FormBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symbio\OrangeGate\FormBundle\Exception\InvalidArgumentException;
use Symbio\OrangeGate\FormBundle\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="form__field")
 */
class Field
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
     * @ORM\Column(type="text", length=32, nullable=false)
     */
    protected $type;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $required;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $priority;

    /**
     * @var null|string
     * @ORM\Column(type="text", length=32, nullable=true, name="validation_type")
     */
    protected $validationType;

    /**
     * @var null|string
     * @ORM\Column(type="text", length=255, nullable=true, name="validation_settings")
     */
    protected $validationSettings;

    /**
     * @Gedmo\Translatable
     * @var null|string
     * @ORM\Column(type="text", length=255, nullable=true, name="validation_message")
     */
    protected $validationMessage;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="fields")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=false)
     */
    protected $form;

    /**
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="field", cascade={"persist"})
     * @ORM\OrderBy({"priority"="ASC", "id"="ASC"})
     */
    protected $choices;

    /**
     * @ORM\OneToMany(targetEntity="FieldTranslation", mappedBy="object", indexBy="locale", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $translations;


    /**
     * Field constructor.
     * @param int $id
     * @param $label
     * @param $type
     * @param bool $required
     * @param string $validationType
     * @param string $validationSettings
     * @param int $priority
     */
    public function __construct(
        $id = null,
        $label = null,
        $type = null,
        $required = null,
        $priority = null,
        $validationType = null,
        $validationMessage = null,
        $validationSettings = null
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->priority = $priority;
        $this->validationType = $validationType;
        $this->validationSettings = $validationSettings;
        $this->validationMessage = $validationMessage;

        $this->translations = new ArrayCollection();
        $this->choices = new ArrayCollection();
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
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
     * @return
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param mixed $choices
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setChoices($choices)
    {
        $callback = function ($a, $b) {
            return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
        };

        if (is_array($choices)) {
            usort($choices, $callback);
            $this->choices = new ArrayCollection($choices);
        }
        else if ($choices instanceof ArrayCollection) {
            $this->choices =
            $iterator = $choices->getIterator();
            $iterator->uasort($callback);
            $this->choices = new ArrayCollection(iterator_to_array($iterator));
        }
        else if (null === $choices) {
            $this->choices = null;
        }
        else {
            throw new InvalidArgumentException('$field must be ArrayCollection or null');
        }
        return $this;
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

    /**
     * @return null|string
     */
    public function getValidationType()
    {
        return $this->validationType;
    }

    /**
     * @param null|string $validationType
     * @return $this
     */
    public function setValidationType($validationType)
    {
        $this->validationType = $validationType;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getValidationSettings()
    {
        return $this->validationSettings;
    }

    /**
     * @param null|string $validationSettings
     * @return $this
     */
    public function setValidationSettings($validationSettings)
    {
        $this->validationSettings = $validationSettings;
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
     * @param null|string $validationMessage
     * @return $this
     */
    public function setValidationMessage($validationMessage)
    {
        $this->validationMessage = $validationMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param mixed $translations
     * @return $this
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
        return $this;
    }

    public function addTranslation(AbstractTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations->set($translation->getLocale(), $translation);
        }
        return $this;
    }

    public function removeTranslation(AbstractTranslation $translation)
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }
        return $this;
    }

    public function getTranslation($locale)
    {
        if (isset($this->translations[$locale])) {
            return $this->translations[$locale];
        }

        return null;
    }

    /**
     * Specifies whether field has choices
     * @return bool
     */
    public function isChoice()
    {
        return in_array($this->getType(), ['select', 'radio', 'checkboxes']);
    }

    /**
     * todo priority order!
     * @return array
     */
    public function getChoicesArray()
    {
        $ret = [];
        foreach ($this->getChoices() as $choice) {
            $ret[$choice->getValue()] = $choice->getLabel();
        }

        return $ret;
    }

    /**
     * Returns form field type for formbuilder
     * @return string
     */
    public function getFormFieldType()
    {
        if ($this->isChoice()) {
            return 'choice';
        } else {
            return $this->getType();
        }
    }

    /**
     * Returns params array for formbuilder
     * @return array
     */
    public function getFormFieldParams()
    {
        $params = [
            'label' => $this->getLabel(),
            'required' => $this->isRequired(),
        ];

        if ($this->isChoice()) {
            $params['choices'] = $this->getChoicesArray();

            switch ($this->getType()) {
                case 'radio':
                    $params['expanded'] = true;
                    break;

                case 'checkboxes':
                    $params['expanded'] = true;
                    $params['multiple'] = true;
            }
        }

        return $params;
    }

    public function __toString()
    {
        return $this->getLabel() . '(' . $this->getType() . ')';
    }
}