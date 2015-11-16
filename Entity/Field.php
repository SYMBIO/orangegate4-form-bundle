<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 27.10.15
 * Time: 16:15
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
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="fields")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=false)
     */
    protected $form;

    /**
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="field", cascade={"persist"})
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
     * @param int $priority
     */
    public function __construct($id = null, $label = null, $type = null, $required = null, $priority = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->priority = $priority;

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
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
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


    public function getFormFieldType()
    {
        if ($this->isChoice()) {
            return 'choice';
        } else {
            return $this->getType();
        }
    }

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
}