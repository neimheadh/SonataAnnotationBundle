<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * Extension adding to test suites createSession() function.
 */
trait SessionHelperTrait
{

    /**
     * Create mock session for given client.
     *
     * @param KernelBrowser $client Test client.
     *
     * @return Session
     */
    private function createSession(KernelBrowser $client): Session
    {
        $container = $client->getContainer();
        $sessionSavePath = $container->getParameter('session.save_path');
        $sessionStorage = new MockFileSessionStorage($sessionSavePath);

        $session = new Session($sessionStorage);
        $session->start();
        $session->save();

        $sessionCookie = new Cookie(
          $session->getName(),
          $session->getId(),
          null,
          null,
          'localhost',
        );
        $client->getCookieJar()->set($sessionCookie);

        return $session;
    }
}