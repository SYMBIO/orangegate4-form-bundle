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

    /**
     * @param \Swift_Mailer $mailer
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $templating
     */
    public function __construct($mailer, $templating, $from) {
        $this->mailer = $mailer;
        $this->templating = $templating;
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
                // todo this should be changeable
                $this->templating->render(
                    'SymbioOrangeGateFormBundle:Email:form.txt.twig',
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

}