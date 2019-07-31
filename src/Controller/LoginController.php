<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserLoginType;

/**
 * Login controller.
 * @Route("/api", name="api_")
 */
class LoginController extends AbstractFOSRestController
{

    /**
     * Activate and set Password User.
     * @Rest\Put("/activate/{token}")
     *
     * @return Response
     */
    public function activateAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneByConfirmationToken($request->get('token'));

        $data = json_decode($request->getContent(), true);
        $data['password'];

        if ($user)
        {
            if ($user->getPassword() === 'NO_PASSWORD')
            {
                if ($data['password'])
                {
                    $user->setEnabled(1);

                    $password = $passwordEncoder->encodePassword($user, $data['password']);
                    $user->setPassword($password);

                }

                return $this->handleView($this->view(['message' => 'Password is empty'], Response::HTTP_NOT_FOUND));
            }

            $user->setEnabled(1);

            $em->persist($user);
            $em->flush();

            return $this->handleView($this->view(['message' => $data['password']], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view(['message' => 'Cannot find user'], Response::HTTP_NOT_FOUND));
    }

    /**
     * Create User.
     * @Rest\Post("/register")
     *
     * @return Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($em->getRepository(User::class)->findByEmail($data['email']))
        {
            return $this->handleView($this->view(['message' => 'Email already taken'], Response::HTTP_NOT_FOUND));
        }

        if ($em->getRepository(User::class)->findByUsername($data['username']))
        {
            return $this->handleView($this->view(['message' => 'Username already taken'], Response::HTTP_NOT_FOUND));
        }

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());

            $view = 'emails/activate.html.twig';

            if (!$user->getPassword()) {
                $password = 'NO_PASSWORD';
                $view = 'emails/setpassword.html.twig';
            }

            $user->setPassword($password);

            $user->addRole("ROLE_ADMIN");

            $token = bin2hex(random_bytes(10));

            $user->setConfirmationToken($token);

            $em->persist($user);
            $em->flush();

            //Mail
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('info@yousign.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        $view,
                        ['token' => $token]
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);

            return $this->handleView($this->view(['status' => 'We sent you an email to activate your account !', 'user' => $user], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Login User.
     * @Rest\Post("/login")
     *
     * @return Response
     */
    public function loginAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $form = $this->createForm(UserLoginType::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        $password = $user->getPassword();

        $user = $em->getRepository(User::class)->findOneByEmail($user->getEmail());

        // Check if the user exists
        if(!$user){
            return $this->handleView($this->view(['message' => 'This account does not exist'], Response::HTTP_NOT_FOUND));
        }

        if(!$encoder->isPasswordValid($user, $password)) {
            return $this->handleView($this->view(['message' => 'Password not valid !'], Response::HTTP_NOT_FOUND));
        }


        if(!$user->isEnabled()){
            return $this->handleView($this->view(['message' => 'Your account is disabled, maybe check your email before !'], Response::HTTP_NOT_FOUND));
        }

        //Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->get('session')->set('_security_main', serialize($token));

        // Fire the login event manually
        $dispatcher = new EventDispatcher();
        $event = new InteractiveLoginEvent($request, $token);

        $dispatcher->dispatch("security.interactive_login", $event);

        return new Response(
            'Welcome '. $user->getUsername(),
            Response::HTTP_OK,
            array('Content-type' => 'application/json')
        );
    }
}