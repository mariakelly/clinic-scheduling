<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base controller.
 */
class BaseController extends Controller
{
    /**
     * Is the current user an admin?
     */
    public function isAdmin()
    {
        return $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
    }

    /**
     * Get the referring path of the request.
     */
    public function getReferringPath()
    {
        $request = $this->getRequest();
        $referer = $request->headers->get('referer');
        $baseUrl = $request->getBaseUrl();
        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));

        return $lastPath;
    }
}