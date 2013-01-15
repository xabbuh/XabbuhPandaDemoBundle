<?php

namespace Xabbuh\PandaDemoBundle\Twig;

use Symfony\Component\HttpFoundation\Session\Session;
use Xabbuh\PandaBundle\Cloud\CloudManager;

/**
 *
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudExtension extends \Twig_Extension
{
    /**
     * @var \Xabbuh\PandaBundle\Cloud\CloudManager
     */
    private $cloudManager;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;


    public function __construct(CloudManager $cloudManager, Session $session)
    {
        $this->cloudManager = $cloudManager;
        $this->session = $session;
    }

    public function getGlobals()
    {
        return array("clouds" => $this->cloudManager->getClouds(),
            "selected_cloud" => $this->getSelectedCloud());
    }

    public function getName()
    {
        return "panda_demo_extension";
    }

    public function getSelectedCloud()
    {
        if ($this->session->has("cloud")) {
            return $this->session->get("cloud");
        } else {
            return "default";
        }
    }
}
