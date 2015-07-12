<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Cohort;
use AppBundle\Form\CohortType;

/**
 * Cohort controller.
 *
 * @Route("/admin/cohort")
 */
class CohortController extends Controller
{

    /**
     * Lists all Cohort entities.
     *
     * @Route("/", name="admin_cohort")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Cohort')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Cohort entity.
     *
     * @Route("/", name="admin_cohort_create")
     * @Method("POST")
     * @Template("AppBundle:Cohort:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Cohort();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_cohort_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Cohort entity.
     *
     * @param Cohort $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cohort $entity)
    {
        $form = $this->createForm(new CohortType(), $entity, array(
            'action' => $this->generateUrl('admin_cohort_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr'  => array('class' => 'btn btn-primary'),
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Cohort entity.
     *
     * @Route("/add", name="admin_cohort_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Cohort();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Cohort entity.
     *
     * @Route("/{id}", name="admin_cohort_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Cohort')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cohort entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cohort entity.
     *
     * @Route("/{id}/edit", name="admin_cohort_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Cohort')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cohort entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Cohort entity.
    *
    * @param Cohort $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cohort $entity)
    {
        $form = $this->createForm(new CohortType(true), $entity, array(
            'action' => $this->generateUrl('admin_cohort_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Save Changes',
            'attr'  => array('class' => 'btn btn-primary'),
        ));

        return $form;
    }
    /**
     * Edits an existing Cohort entity.
     *
     * @Route("/{id}", name="admin_cohort_update")
     * @Method("PUT")
     * @Template("AppBundle:Cohort:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Cohort')->find($id);
        $wasArchived = $entity->getIsArchived();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cohort entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $isArchived = $entity->getIsArchived();
            if ($wasArchived != $isArchived) {
                $this->updateStudentsInCohort($entity, $isArchived);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('admin_cohort_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Cohort entity.
     *
     * @Route("/{id}", name="admin_cohort_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Cohort')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cohort entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_cohort'));
    }

    /**
     * Creates a form to delete a Cohort entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_cohort_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr'  => array('class' => 'btn btn-warning'),
            ))
            ->getForm()
        ;
    }

    /**
     * Update all of the students in this cohort
     * to match the status of the cohort.
     */
    private function updateStudentsInCohort($cohort, $isArchived)
    {
        $setEnabled = true;
        if ($isArchived) {
            $setEnabled = false;
        }

        foreach ($cohort->getStudents() as $student) {
            $student->setEnabled($setEnabled);
            $this->get('fos_user.user_manager')->updateUser($student);
        }
    }
}
