<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ConfigurationOption;
use AppBundle\Form\ConfigurationOptionType;

/**
 * ConfigurationOption controller.
 *
 * @Route("/admin/configure")
 */
class ConfigurationOptionController extends Controller
{

    /**
     * Lists all ConfigurationOption entities.
     *
     * @Route("/", name="admin_configure")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:ConfigurationOption')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ConfigurationOption entity.
     *
     * @Route("/", name="admin_configure_create")
     * @Method("POST")
     * @Template("AppBundle:ConfigurationOption:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ConfigurationOption();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_configure_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ConfigurationOption entity.
     *
     * @param ConfigurationOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConfigurationOption $entity)
    {
        $form = $this->createForm(new ConfigurationOptionType(), $entity, array(
            'action' => $this->generateUrl('admin_configure_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-primary'),
        ));

        return $form;
    }

    /**
     * Displays a form to create a new ConfigurationOption entity.
     *
     * @Route("/new", name="admin_configure_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ConfigurationOption();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ConfigurationOption entity.
     *
     * @Route("/{id}", name="admin_configure_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ConfigurationOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfigurationOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ConfigurationOption entity.
     *
     * @Route("/{id}/edit", name="admin_configure_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ConfigurationOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfigurationOption entity.');
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
    * Creates a form to edit a ConfigurationOption entity.
    *
    * @param ConfigurationOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ConfigurationOption $entity)
    {
        $form = $this->createForm(new ConfigurationOptionType(true), $entity, array(
            'action' => $this->generateUrl('admin_configure_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Save Changes',
            'attr' => array('class' => 'btn btn-primary'),
        ));

        return $form;
    }
    /**
     * Edits an existing ConfigurationOption entity.
     *
     * @Route("/{id}", name="admin_configure_update")
     * @Method("PUT")
     * @Template("AppBundle:ConfigurationOption:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ConfigurationOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ConfigurationOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_configure_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ConfigurationOption entity.
     *
     * @Route("/{id}", name="admin_configure_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:ConfigurationOption')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ConfigurationOption entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_configure'));
    }

    /**
     * Creates a form to delete a ConfigurationOption entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_configure_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr' => array('class' => 'btn btn-warning'),
            ))
            ->getForm()
        ;
    }
}
