<?php

/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 13.11.15
 * Time: 11:03
 */

namespace Symbio\OrangeGate\FormBundle\Controller;

use Symbio\OrangeGate\FormBundle\Entity\SubmittedData;
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
        $return = [
            'success' => false,
            'form'    => null,
            'message' => null,
        ];

        // test for http request
        try {
            $formModel = $this->loadFormModel($formId);
        } catch (NotFoundHttpException $e) {
            if ($request->isXmlHttpRequest()) {
                $return['message'] = $e->getMessage();
                return new JsonResponse($return, 404);
            } else {
                throw $e;
            }
        }
        $formFactory = $this->get('orangegate.form.factory');

        $form = $formFactory->createForm($formModel);
        $form->handleRequest($request);


        if ($form->isValid()) {

            $data = $formFactory->getFormData($formModel, $form);

            // save form to db
            try {
                $dataObj = new SubmittedData(null, $request->getClientIp(), $request->headers->get('User-Agent'), $data, $formModel);

                $em = $this->getDoctrine()->getManager();
                $em->persist($dataObj);
                $em->flush();
            } catch (\Exception $e) {
                $this->get('logger')->critical(sprintf(
                    'Cannot save data for orangegate form with id: %d. Exception message: %s',
                    $formModel->getId(),
                    $e->getMessage()
                ));

                $return['message'] = 'Error while saving form data: ' . $e->getMessage();
                return new JsonResponse($return, 500);
            }

            // send form via email
            // if it fails, just log it an proceed
            if ($formModel->isEmailable()) {
                try {
                    $this->get('orangegate.form.mailer')->sendFormDataEmail($data, $formModel);
                } catch (\Exception $e) {
                    $this->get('logger')->error(sprintf(
                        'Cannot send email with data for orangegate form with id: %d. Exception message: %s',
                        $formModel->getId(),
                        $e->getMessage()
                    ));
                }
            }

            $return['success'] = true;
            $form = $formFactory->createForm($formModel);
        }

        // render form
        $return['form'] = $this->renderView('SymbioOrangeGateFormBundle:Block:_form.html.twig', [
            'form' => $form->createView(),
            'formId' => $formModel->getId(),
        ]);

        return new JsonResponse($return);
    }


    /**
     * Load form with given ID or fail (404)
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