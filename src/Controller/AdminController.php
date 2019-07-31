<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;
use App\Form\EditUserType;

/**
 * Admin controller.
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * Lists all Users
     * @Rest\Get("/users")
     *
     * @return Response
     */
    public function getUsersAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        return $this->handleView($this->view($users));
    }

    /**
     * Delete user
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/user/delete/{id}")
     */
    public function deleteUser(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($request->get('id'));

        if ($user)
        {
            $em->remove($user);
            $em->flush();
        }

        return $this->handleView($this->view());
    }

    /**
     * Activate user
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Put("/user/activate/{id}")
     */
    public function activateUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($request->get('id'));

        $user->setEnabled(!$user->isEnabled());

        $em->persist($user);
        $em->flush();

        return $this->handleView($this->view());
    }

    /**
     * Edit user
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Put("/user/edit/{id}")
     */
    public function editUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($request->get('id'));

        $form = $this->createForm(EditUserType::class, $user);
        $data = json_decode($request->getContent(), true);

        if ($em->getRepository(User::class)->findByEmail($data['email']) && $data['email'] != $user->getEmail())
        {
            return $this->handleView($this->view(['message' => 'Email already taken'], Response::HTTP_NOT_FOUND));
        }

        if ($em->getRepository(User::class)->findByUsername($data['username']) && $data['username'] != $user->getUsername())
        {
            return $this->handleView($this->view(['message' => 'Username already taken'], Response::HTTP_NOT_FOUND));
        }

        $form->submit($data);

        if ($form->isValid()) {
            $em->merge($user);
            $em->flush();

            return $this->handleView($this->view(['status' => 'ok', 'user' => $user], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }
}