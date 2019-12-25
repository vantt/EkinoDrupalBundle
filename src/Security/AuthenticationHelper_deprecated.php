<?php


namespace Ekino\Bundle\DrupalBundle\Security;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class AuthenticationHelperDepricated
 * @package Ekino\Bundle\DrupalBundle\Security
 * @deprecated
 */
class AuthenticationHelperDeprecated {
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * AuthenticationHelper constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher) {
        $this->tokenStorage    = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function login() {

        // Here, "public" is the name of the firewall in your security.yml
        $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());

        // For older versions of Symfony, use security.context here
        $this->get("security.token_storage")->setToken($token);

        // Fire the login event
        // Logging the user in above the way we do it doesn't do this automatically
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

    }
}