<?php

/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.11.15
 * Time: 10:32
 */
class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests extended logic on form recipients
     */
    public function testRecipients()
    {
        $form = new \Symbio\OrangeGate\FormBundle\Entity\Form(1, 'Test', 'test', 1);

        $this->assertEquals(false, $form->isEmailable());
        $this->assertEquals([], $form->getRecipientsArray());

        $form->setRecipients([
            new \Symbio\OrangeGate\FormBundle\Entity\Recipient('Nekdo Nekde', 'nekdo@nekde.cz', $form, 1),
            new \Symbio\OrangeGate\FormBundle\Entity\Recipient('Nekdo Nekde Jinde', 'nekdo@nekde.com', $form, 1),
        ]);

        $this->assertEquals(true, $form->isEmailable());
        $this->assertEquals(
            [
                'nekdo@nekde.cz' => 'Nekdo Nekde',
                'nekdo@nekde.com' => 'Nekdo Nekde Jinde',
            ],
            $form->getRecipientsArray()
        );
    }
}
