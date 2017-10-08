<?php 
// src/AppBundle/EventListener/ExceptionListener.php
namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;

class ExceptionListener
{

    private $_twig = null;
    
    public function __construct(Environment $twig)
    {
        $this->_twig = $twig;   
    }
    
    public function onKernelException(GetResponseForExceptionEvent $event, $kernel)
    { 
        $exception = $event->getException();
        
        $rendered = $this->_twig->render('info.html.twig', [
            "msg" => $exception->getMessage(),
            "classe" => "warning",
            "back" => true
        ]);

        $response = new Response();
        $response->setContent($rendered);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}