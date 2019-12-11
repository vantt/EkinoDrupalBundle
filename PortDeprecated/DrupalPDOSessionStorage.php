<?php

namespace Ekino\Bundle\DrupalBundle\Port;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;


use Ekino\Bundle\DrupalBundle\Drupal\Drupal;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 * @author Florent Denis <fdenis@ekino.com>
 */
class DrupalSessionStorage extends NativeSessionStorage
{
    /**
     * {@inheritdoc}
     */
    public function save() {
        $session = $_SESSION;

        foreach ($this->bags as $bag) {
            if (empty($_SESSION[$key = $bag->getStorageKey()])) {
                unset($_SESSION[$key]);
            }
        }
        if ([$key = $this->metadataBag->getStorageKey()] === array_keys($_SESSION)) {
            unset($_SESSION[$key]);
        }

        // Register error handler to add information about the current save handler
        $previousHandler = set_error_handler(function ($type, $msg, $file, $line) use (&$previousHandler) {
            if (E_WARNING === $type && 0 === strpos($msg, 'session_write_close():')) {
                $handler = $this->saveHandler instanceof SessionHandlerProxy ? $this->saveHandler->getHandler() : $this->saveHandler;
                $msg = sprintf('session_write_close(): Failed to write session data with "%s" handler', \get_class($handler));
            }

            return $previousHandler ? $previousHandler($type, $msg, $file, $line) : false;
        });

        try {
            session_write_close();
        } finally {
            restore_error_handler();
            $_SESSION = $session;
        }

        $this->closed = true;
        $this->started = false;
    }

    /**
     * Load the session with attributes.
     *
     * After starting the session, PHP retrieves the session from whatever handlers
     * are set to (either PHP's internal, or a custom save handler set with session_set_save_handler()).
     * PHP takes the return value from the read() handler, unserializes it
     * and populates $_SESSION with the result automatically.
     */
    protected function loadSession(array &$session = null) {
        if (null === $session) {
            $session = &$_SESSION;
        }

        $bags = array_merge($this->bags, [$this->metadataBag]);

        foreach ($bags as $bag) {
            $key = $bag->getStorageKey();
            $session[$key] = isset($session[$key]) ? $session[$key] : [];
            $bag->initialize($session[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }


}
