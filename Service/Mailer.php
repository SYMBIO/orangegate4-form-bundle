<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 13.11.15
 * Time: 18:10
 */

namespace Symbio\OrangeGate\FormBundle\Service;

use Symbio\OrangeGate\FormBundle\Entity\Form;

class Mailer
{
    /** @var \Swift_Mailer $mailer */
    private $mailer;

    /** @var \Symfony\Bundle\TwigBundle\TwigEngine $templating */
    private $templating;

    /** @var string|array $from */
    private $from;

    private $templates = [
        'dataForm' => 'SymbioOrangeGateFormBundle:Email:form.txt.twig',
    ];

    /**
     * @param \Swift_Mailer $mailer
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $templating
     */
    public function __construct($mailer, $templating, $from) {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->from = $from;
    }

    /**
     * @param array $formData
     * @param Form $formEntity
     * @return int Number of emails sent
     */
    public function sendFormDataEmail($formData, $formEntity)
    {
        /** @var \Swift_Message $message */
        $message = $this->mailer->createMessage();

        if (is_array($this->from)) {
            $message->setFrom($this->from[0], $this->from[1]);
        } else {
            $message->setFrom($this->from);
        }

        $message
            ->setTo($formEntity->getRecipientsArray())
            // todo translate
            ->setSubject('Nová data z formuláře ' . $formEntity->getName())
            ->setBody(
                $this->templating->render(
                    $this->getTemplate('dataForm'),
                    [
                        'formEntity' => $formEntity,
                        'formData'   => $formData,
                    ]
                ),
                'text/plain'
            )
        ;

        return $this->mailer->send($message);
    }

    /**
     * Get template by name
     * @param string $name
     * @return mixed
     */
    public function getTemplate($name)
    {
        if (array_key_exists($name, $this->templates)) {
            return $this->templates[$name];
        }

        throw new \InvalidArgumentException('Unknown name: ' . $name);
    }

    /**
     * Set template for given key
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setTemplate($name, $value)
    {
        $this->templates[$name] = $value;
        return $this;
    }
}