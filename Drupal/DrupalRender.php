<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Drupal;

use Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface;

use Ekino\Bundle\DrupalBundle\Security\StackUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * The class retrieve a request and ask drupal to build the content
 *
 * See http://symfony.com/doc/current/book/internals.html#handling-requests
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalRender {
    /**
     * @var DrupalInterface
     */
    protected $drupal;

    /**
     * @var DeliveryStrategyInterface
     */
    protected $strategy;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var StackUser
     */
    protected $stackUser;


    protected $isBoot = false;

    protected $redirect = '';

    /**
     * Constructor
     *
     * @param DrupalInterface           $drupal   A Drupal instance
     * @param DeliveryStrategyInterface $strategy A delivery strategy instance
     * @param SessionInterface          $session
     * @param StackUser                 $stackUser
     */
    public function __construct(DrupalInterface $drupal, DeliveryStrategyInterface $strategy, SessionInterface $session, StackUser $stackUser) {
        $this->drupal    = $drupal;
        $this->strategy  = $strategy;
        $this->session   = $session;
        $this->stackUser = $stackUser;
    }

    public function render(Request $request, bool $justLogin=false): ?Response {
        $this->restoreGlobalUser($justLogin);
        return $this->buildResponse($request);
    }

    private function boot(Request $request) {
        if (!$this->isBoot) {
            $this->drupal->defineState($request);
            $this->isBoot = true;
        }
    }

    /**
     * @param Request $request
     *
     * @return Response | null
     */
    private function buildResponse(Request $request): ?Response {
        $this->boot($request);

        $this->strategy->buildResponse($this->drupal);

        if ($this->drupal->hasResponse()) {
            $response = $this->drupal->getResponse();

            if ($this->isRedirect()) {
                return new RedirectResponse($this->getRedirect(), 302, $response->headers->all());
            }

            return $response;

        }

        throw new RouteNotFoundException();
    }

    private function restoreGlobalUser(bool $justLogin) {
        $forceAnonymous = ($justLogin === true);
        $this->stackUser->restoreUser($forceAnonymous);
    }

    public function is404() {
        return $this->drupal->is404();
    }

    private function isRedirect(): bool {
        return $this->drupal->isRedirect();
    }

    /**
     * @return string
     */
    private function getRedirect(): string {
        return $this->drupal->getRedirect();
    }
}

