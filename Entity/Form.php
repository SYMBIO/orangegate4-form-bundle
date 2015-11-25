<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 27.10.15
 * Time: 16:01
 */

namespace Symbio\OrangeGate\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symbio\OrangeGate\PageBundle\Entity\Site;
use Symbio\OrangeGate\FormBundle\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="form__form")
 */
class Form
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
    protected $name;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Symbio\OrangeGate\PageBundle\Entity\Site", cascade={"persist"})
     * @ORM\JoinColumn(name="site_id", nullable=false)
     */
    protected $site;

    /**
     * @Gedmo\Translatable
     * @var string
     * @ORM\Column(type="text", length=255, nullable=false, name="submit_label")
     */
    protected $submitLabel;

    /**
     * @var string
     * @ORM\Column(type="text", length=255, nullable=true, name="email_from")
     */
    protected $emailFrom;

    /**
     * @var Recipient
     *
     * @ORM\OneToMany(targetEntity="Recipient", mappedBy="form", cascade={"persist"})
     */
    protected $recipients;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="form", cascade={"persist"})
     */
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="FormTranslation", mappedBy="object", indexBy="locale", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $translations;

    /**
     * Form constructor.
     * @param int $id
     * @param $name
     * @param $description
     * @param Site $site
     */
    public function __construct($id = null, $name = null, $description = null, $site = null, $submitLabel = null, $emailFrom = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->site = $site;
        $this->submitLabel = $submitLabel;
        $this->emailFrom = $emailFrom;

        $this->recipients = new ArrayCollection();
        $this->fields = new ArrayCollection();
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmitLabel()
    {
        return $this->submitLabel;
    }

    /**
     * @param mixed $submitLabel
     * @return $this
     */
    public function setSubmitLabel($submitLabel)
    {
        $this->submitLabel = $submitLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailFrom()
    {
        return $this->emailFrom;
    }

    /**
     * @param string $emailFrom
     * @return $this
     */
    public function setEmailFrom($emailFrom)
    {
        $this->emailFrom = $emailFrom;
        return $this;
    }

    /**
     * @return Recipient[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     * @return $this
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
        return $this;
    }

    /**
     * Returns recipients as email => name pairs
     * @return array
     */
    public function getRecipientsArray()
    {
        $ret = [];

        foreach($this->getRecipients() as $recipient) {
            $ret[$recipient->getEmail()] = $recipient->getName();
        }

        return $ret;
    }

    /**
     * @return Field
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
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

    public function isEmailable()
    {
        return count($this->getRecipients()) > 0;
    }

    public function __toString()
    {
        return $this->getName();
    }
}