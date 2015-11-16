<?php

/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 13.11.15
 * Time: 11:03
 */

namespace Symbio\OrangeGate\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symbio\OrangeGate\FormBundle\Entity\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FormController extends Controller
{
    /**
     * @Route("/orangegate/form/submit/{formId}", name="symbio_orangegate_form_form_submit")
     * @param int $formId
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     * todo try to handle non-ajax requests too
     */
    public function submitAction($formId, Request $request)
    {
        // test for http request
        $formModel = $this->loadFormModel($formId);
        $formFactory = $this->get('orangegate.form.factory');

        $form = $formFactory->createForm($formModel);
        $form->handleRequest($request);

        $return = [
            'success' => false,
            'form'    => null,
        ];

        if ($form->isValid()) {
            $return['success'] = true;

            $data = $formFactory->getFormData($formModel, $formFactory);

            // todo save data to db

            if ($formModel->isEmailable()) {
                $this->get('orangegate.form.mailer')->sendFormDataEmail($data, $formModel);
            }

            $form = $formFactory->createForm($formModel);
        }

        $return['form'] = $this->renderView('SymbioOrangeGateFormBundle:Block:_form.html.twig', [
            'form' => $form->createView(),
            'formId' => $formModel->getId(),
        ]);

        return new JsonResponse($return);
    }


    /**
     * @param int $id
     * @return Form
     * @throws NotFoundHttpException
     */
    protected function loadFormModel($id)
    {
        $form = $this->getDoctrine()->getRepository('SymbioOrangeGateFormBundle:Form')->find($id);

        if (null === $form) {
            throw new NotFoundHttpException('Invalid form specified');
        }

        return $form;
    }
}