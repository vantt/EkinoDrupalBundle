<?php
namespace Ekino\Bundle\DrupalBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Ekino\Bundle\DrupalBundle\Entity\DrupalUser;
use stdClass;
use Symfony\Component\Security\Core\Security;

class StackUser {

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    private static $isLoaded = false;

    public function __construct(Security $security, EntityManagerInterface $em) {
        $this->security = $security;
        $this->em       = $em;
    }

    final public function restoreUser(bool $forceAnonymous = false): void {
        global $user;

        if ($forceAnonymous) {
            $user = $this->createAnonymousUser();
        }
        else {
            $tokenUser = $this->security->getUser();

            if (null !== $tokenUser) {
                $repo       = $this->em->getRepository(DrupalUser::class);
                $userFromDb = $repo->find($tokenUser->uid);

                if (null !== $userFromDb) {
                    $user = $userFromDb->toDrupalUser();
                }
            }
            else {
                $user = $this->createAnonymousUser();
            }
        }

        self::$isLoaded = true;
    }

    final public function &getCurrentUser(): stdClass {
        global $user;

        if (!self::$isLoaded) {
            $this->restoreUser();
        }

        return $user;
    }

    private function createAnonymousUser(): stdClass {
        $user           = new stdClass();
        $user->uid      = 0;
        $user->roles    = [];
        $user->roles[1] = 'anonymous user'; // DRUPAL_ANONYMOUS_RID
        $user->cache    = 0;

        return $user;
    }

}