<?php

namespace Ekino\Bundle\DrupalBundle\Security;

use Ekino\Bundle\DrupalBundle\Entity\SessionUserOriginal;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DrupalUserProviderDeprecated implements UserProviderInterface {

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username) {
        global $user;

        if ($user->uid > 0) {
            $obj = SessionUserOriginal::createFromDrupalUser($user);
            if ($obj->getUsername() == $username) {
                return $obj;
            }
        }

        throw new UsernameNotFoundException(sprintf('Could not match current signed-in Drupal User "%s"', $username));
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     */
    public function refreshUser(UserInterface $user) {
        dump($user); exit;
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class) {
        dump($class); exit;
        return (SessionUserOriginal::class === $class);
    }

}