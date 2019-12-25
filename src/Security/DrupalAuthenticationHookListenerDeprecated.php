<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Security;

use Ekino\Bundle\DrupalBundle\Entity\SessionUserOriginal;
use Mio\D7ServiceContainer\DrupalEvent;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * These methods are called by the drupal user hook
 *
 * see for more information about parameters http://api.drupal.org/api/drupal/modules--user--user.api.php/7
 */
class DrupalAuthenticationHookListenerDeprecated {

    protected $session;
    protected $request;
    protected $guardHandler;

    /**
     * @var array
     */
    protected $providerKeys;
    private   $authenticator;


    /**
     * @param DrupalAuthenticator       $authenticator
     * @param GuardAuthenticatorHandler $guardHandler
     * @param RequestStack              $request
     */
//    public function __construct(DrupalAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, RequestStack $request, SessionInterface $session) {
//
//        $this->authenticator = $authenticator;
//        $this->guardHandler  = $guardHandler;
//        $this->request       = $request->getCurrentRequest();
//        $this->session       = $session;
//    }

    /**
     * http://api.drupal.org/api/drupal/modules--user--user.api.php/function/hook_user_login/7
     *
     * @param DrupalEvent $event
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function onLogin(DrupalEvent $event) {
//        $user = $event->getData()[0];
//
//        return $this->guardHandler->authenticateUserAndHandleSuccess(
//          SessionUser::createFromDrupalUser($event->getData()[0]),          // the User object you just created
//          $this->request,
//          $this->authenticator, // authenticator whose onAuthenticationSuccess you want to use
//          'main'    // the name of your firewall in security.yaml
//        );
    }


    /**
     * @param DrupalEvent $event
     */
    public function onLogout(DrupalEvent $event) {
        //dump($event);
        //exit;
        //        foreach ($this->providerKeys as $providerKey) {
        //            $this->request->getSession()->set('_security_' . $providerKey, null);
        //        }
    }
}
