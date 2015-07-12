<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Controller\BaseController;

/**
 * User controller.
 */
class UserController extends BaseController
{

    /**
     * Lists all User entities.
     *
     * @Route("/admin/user", name="admin_user")
     * @Route("/admin/user/in{disabled}", name="admin_user_disabled")
     * @Route("/users", name="student_view_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($disabled = null)
    {
        $em = $this->getDoctrine()->getManager();
        $enabled = ($disabled != "active");

        $routeUsed = $this->getRequest()->get('_route');
        $studentView = ($routeUsed == "student_view_users");

        $entities = $em->getRepository('AppBundle:User')->findBy(
            array('enabled' => $enabled), array('roles' => 'ASC', 'lastName' => 'asc'
        ));

        return array(
            'entities' => $entities,
            'enabled' => $enabled,
            'studentView' => $studentView,
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/admin/user", name="admin_user_create")
     * @Route("/admin/user/create-{admin}", name="admin_user_create_admin")
     * @Method("POST")
     * @Template("AppBundle:User:new.html.twig")
     */
    public function createAction(Request $request, $admin = null)
    {
        $createAdmin = ($admin == "admin");
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPassword($this->generatePassword(25));
            $entity->setEnabled(true);

            if ($createAdmin) {
                $entity->addRole('ROLE_ADMIN');
            }

            $userManager = $this->get('fos_user.user_manager');

            $userManager->updatePassword($entity);
            $userManager->updateUser($entity);

            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity, $createAdmin = false)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $createAdmin ? $this->generateUrl('admin_user_create_admin', array('admin' => 'admin')) : $this->generateUrl('admin_user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create Account',
            'attr' => array('class' => 'btn btn-primary'),
        ));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/admin/user/add", name="admin_user_new")
     * @Route("/admin/user/add-{admin}", name="admin_user_new_admin")
     * @Method("GET")
     * @Template()
     */
    public function newAction($admin = null)
    {
        $createAdmin = ($admin == "admin");
        $entity = new User();
        $form   = $this->createCreateForm($entity, $createAdmin);

        return array(
            'entity' => $entity,
            'createAdmin' => $createAdmin,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/admin/user/{id}", name="admin_user_show")
     * @Route("/profile", name="profile")
     * @Route("/profile/", name="profile_slash")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id = null)
    {
        $isProfile = false; # Are we looking at our own profile?
        if (is_null($id)) {
            $isProfile = true;
            $user = $this->get('security.context')->getToken()->getUser();
            if ($user) {
                $id = $user->getId();
            } else {
                throw $this->createNotFoundException('Unable to find User entity.');
            }            
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'isProfile'   => $isProfile,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * @Route("/update-profile", name="edit_profile")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id = null)
    {        
        $isProfile = false; # Are we looking at our own profile?
        if (is_null($id)) {
            $isProfile = true;
            $user = $this->get('security.context')->getToken()->getUser();
            if ($user) {
                $id = $user->getId();
            } else {
                throw $this->createNotFoundException('Unable to find profile.');
            }            
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'isProfile'   => $isProfile,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType($this->isAdmin()), $entity, array(
            'action' => $this->generateUrl('admin_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Save Changes',
            'attr' => array('class' => 'btn btn-primary'),
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/admin/user/{id}", name="admin_user_update")
     * @Method("PUT")
     * @Template("AppBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            if ($this->getReferringPath() == "/update-profile") {
                return $this->redirect($this->generateUrl('profile'));
            } else {
                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $id)));
            }
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/admin/user/{id}", name="admin_user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr'  => array('class' => 'btn btn-warning'),
            ))
            ->getForm()
        ;
    }

    /**
     * Generates a random password of $length.
     */
    private function generatePassword($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        return substr(str_shuffle($chars),0,$length);
    }

}
