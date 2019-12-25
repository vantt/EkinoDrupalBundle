<?php

namespace Ekino\Bundle\DrupalBundle\Drupal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DrupalController extends AbstractController {

    /**
     * @var DrupalRender
     */
    protected $drupalRender;

    /**
     *
     * @param DrupalRender $render
     */
    public function __construct(DrupalRender $render) {
        $this->drupalRender = $render;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response {
        return $this->drupalRender->render($request);
    }
}