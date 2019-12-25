<?php

namespace Ekino\Bundle\DrupalBundle\Security;

use Ekino\Bundle\DrupalBundle\Drupal\DrupalRender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DrupalAuthenticator extends AbstractGuardAuthenticator {
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var DrupalRender
     */
    private $drupalRender;

    private $authType;

    private $session;

    public function __construct(Security $security, EncoderFactoryInterface $encoderFactory, SessionInterface $session, DrupalRender $drupalRender) {
        $this->encoderFactory = $encoderFactory;
        $this->security       = $security;
        $this->drupalRender   = $drupalRender;
        $this->session        = $session;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request) {
        if ($this->security->getUser()) {
            return false;
        }

        if ($request->request->has('form_id') && $request->request->has('name') && $request->request->has('pass')) {
            $this->authType = 'login';
            return true;
        }

        if ($this->session->has('internalAuth-result')) {
            $this->authType = 'internalAuth';
            return true;
        }

        return false;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array {
        // this if will detect the submit from the login-form
        if ('login' === $this->authType) {
            return [
              'username' => $request->request->get('name'),
              'password' => $request->request->get('pass'),
            ];
        }

        // this if will detect the redirect from the oauth-login-flow result.
        // code/html/sites/all/modules/mio/membership/membership.oauth.inc
        if ('internalAuth' === $this->authType) {
            $credentials = [
              'username' => (string)$this->session->get('internalAuth-username'),
              'password' => (string)$this->session->get('internalAuth-result')
            ];

            $this->session->remove('internalAuth-username');
            $this->session->remove('internalAuth-result');

            return $credentials;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $isValid = false;

        if ('login' === $this->authType) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $isValid = ($user->getUsername() === $credentials['username']) && $encoder->isPasswordValid($user->getPassword(), $credentials['password'], null);
        }
        // code/html/sites/all/modules/mio/membership/membership.oauth.inc
        elseif ('internalAuth' === $this->authType) {
            $isValid = ($user->getUsername() === $credentials['username']) && ('1' === $credentials['password']);
        }

        return $isValid;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        if ('login' === $this->authType) {
            // this rendering will skip the DrupalController and render the rest of Drupal-login-flow
            // this authenticator will execute the Drupal-Flow same as the DrupalController do
            return $this->drupalRender->render($request, $justLogin = true);
        }

        // code/html/sites/all/modules/mio/membership/membership.oauth.inc
        if ('internalAuth' === $this->authType) {
            $redirect = $this->session->get('internalAuth-redirect');
            $this->session->remove('internalAuth-redirect');
            $content = " 
            <html><body><script>
            window.opener.parent.location.href = \"$redirect\";
            window.close();
            </script></body></html>";
            return new Response($content);
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        if ('login' === $this->authType) {
            $data = [
              'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
            ];

            return new JsonResponse($data, Response::HTTP_FORBIDDEN);
        }

        if ('internalAuth' === $this->authType) {
            $content = '<html><body><script>
            window.opener.parent.location.reload();
            window.close();
            </script></body></html>';
            return new Response($content);
        }
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null) {

        $data = [
            // you might translate this message
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     *
     * RememberMe cookie will be set if *all* of the following are met:
     *    A) This method returns true (supportsRememberMe)
     *    B) The remember_me key under your firewall is configured
     *    C) The "remember me" functionality is activated. This is usually done by
     *         having a _remember_me checkbox in your form, but
     *         can be configured by the "always_remember_me" and "remember_me_parameter"
     *         parameters under the "remember_me" firewall key
     *    D) The onAuthenticationSuccess method returns a Response object
     */
    public function supportsRememberMe() {
        return true;
    }
}
