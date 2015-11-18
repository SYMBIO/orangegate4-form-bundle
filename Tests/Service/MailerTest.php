<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.11.15
 * Time: 16:18
 */

namespace Symbio\OrangeGate\FormBundle\Tests\Service;

use Symbio\OrangeGate\FormBundle\Service\Mailer;
use Symbio\OrangeGate\FormBundle\Entity\Form;
use Symbio\OrangeGate\FormBundle\Entity\Field;
use Symbio\OrangeGate\FormBundle\Entity\Choice;
use Symbio\OrangeGate\FormBundle\Entity\Recipient;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    private function getTemplatingStub()
    {
        return new \Twig_Environment(new \Twig_Loader_Filesystem(array(__DIR__ . '/../../Resources/views/Email')));
    }


    public function testSendFormDataMail()
    {
        // set up expectations
        $messageMock = $this->getMockBuilder('\Swift_Message')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        // expected send from
        $messageMock->expects($this->once())->method('setFrom')
            ->with('tester@test.eu', 'Tester Eu')
            ->willReturnSelf();

        // expected recipients
        $messageMock->expects($this->once())->method('setTo')->with([
                'recipient1@test.cz' => 'Tester 1',
                'recipient2@test.cz' => 'Tester 2',
            ])
            ->willReturnSelf();

        // expected mail subject
        $messageMock->expects($this->once())->method('setSubject')
            ->with('Nová data z formuláře Test form')
            ->willReturnSelf();

        // expected mail body
        $messageMock->expects($this->once())->method('setBody')
            ->with(file_get_contents(__DIR__ . '/mail_tpl.txt'))
            ->willReturnSelf();

        // expected mailer calls
        $mailerMock = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $mailerMock->expects($this->once())->method('createMessage')->willReturn($messageMock);
        $mailerMock->expects($this->once())->method('send')->with($messageMock)->willReturn(2);

        // create form model
        $fieldChoice = new Field(3, 'Field 3 choice Label', 'checkboxes');
        $fieldChoice->setChoices([
            new Choice('Choice 1', 1),
            new Choice('Choice 2', 2),
        ]);
        $formModel = new Form(1, 'Test form', 'Form description', 1);

        $formModel->setFields([
            new Field(1, 'Item 1 Label', 'textarea'),
            new Field(2, 'Item 2 Label', 'textarea'),
            $fieldChoice,
        ]);
        $formModel->setRecipients([
            new Recipient('Tester 1', 'recipient1@test.cz', $formModel),
            new Recipient('Tester 2', 'recipient2@test.cz', $formModel),
        ]);

        // create form data
        $formData = [
            'field_1' => ['label' => 'Item 1 Label', 'value' => 'data 1'],
            'field_2' => ['label' => 'Item 2 Label', 'value' => 2],
            'field_3' => ['label' => 'Field 3 choice Label', 'value' => ['Choice 1', 'Choice 2']],
        ];

        // create mailer
        $mailer = new Mailer($mailerMock, $this->getTemplatingStub(), ['tester@test.eu', 'Tester Eu']);
        // this is a bit hack, but makes live easier
        $mailer->setTemplate('dataForm', 'form.txt.twig');

        $this->assertEquals(2, $mailer->sendFormDataEmail($formData, $formModel));
    }
}
