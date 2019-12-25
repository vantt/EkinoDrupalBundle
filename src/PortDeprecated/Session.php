<?php

namespace Ekino\Bundle\DrupalBundle\Port;


use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class Session extends SymfonySession implements \ArrayAccess {

    /**
     * {@inheritdoc}
     */
    public function start() {
        global $user;
var_dump(__METHOD__);
        parent::start();

        $_SESSION = $this;
        $user     = $this->drupal_anonymous_user();
    }

    private function drupal_anonymous_user() {
        $user           = new \stdClass;
        $user->uid      = 0;
        $user->cache    = 0;
        $user->roles    = [];
        $user->roles[1] = 'anonymous user';

        return $user;
    }

    /**
     * Whether a offset exists
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

}