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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * The class retrieve a request and ask drupal to build the content
 *
 * See http://symfony.com/doc/current/book/internals.html#handling-requests
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 * @deprecated  originally, author use listener to handle Drupal before return controlling to Symfony
 *              my approach is using DrupalController as a normal controller to handle all Drupal request.
 *
 */
class DrupalRequestListenerDeprecated {

    protected $render;

    /**
     * DrupalRequestListener constructor.
     *
     * @param DrupalRender $render
     */
    public function __construct(DrupalRender $render) {
        $this->render = $render;
    }

    /**
     * @param RequestEvent $event
     *
     * @return mixed
     */
    public function onKernelRequest(RequestEvent $event) {
        return;

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return false;
        }

        $this->render->boot($event->getRequest());
//        return;
//
//        if ($response = $this->render->buildResponse($event->getRequest())) {
//            $event->setResponse($response);
//        }
    }
}
