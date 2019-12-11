<?php
namespace Ekino\Bundle\DrupalBundle\Port;

use ArrayObject;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionStorage extends NativeSessionStorage {

    /**
     * {@inheritdoc}
     */
    public function save() {
        if ($_SESSION instanceof SessionInterface) {
            $_SESSION = new ArrayObject($_SESSION);
        }

        parent::save();
    }

    
}