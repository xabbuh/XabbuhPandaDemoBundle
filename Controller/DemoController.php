<?php

/*
* This file is part of the XabbuhPandaDemoBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaDemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DemoController extends Controller
{
    /**
     * @var \Xabbuh\PandaBundle\Cloud\CloudManager
     */
    private $cloudManager;


    public function indexAction($name)
    {
        return $this->render('XabbuhPandaDemoBundle:Default:index.html.twig', array('name' => $name));
    }

    public function videosAction()
    {
        $cloud = $this->getCloud()->getCloudData();
        $videos = $this->getCloud()->getVideos();
        return $this->render(
            "XabbuhPandaDemoBundle:Demo:videos.html.twig",
            array("cloud" => $cloud, "videos" => $videos)
        );
    }
    
    public function videoEncodingsAction($videoid)
    {
        $cloud = $this->getCloud()->getCloudData();
        $encodings = $this->getCloud()->getEncodingsForVideo($videoid);
        return $this->render(
            "XabbuhPandaDemoBundle:Demo:video_encodings.html.twig",
            array("cloud" => $cloud, "encodings" => $encodings)
        );
    }

    public function deleteVideoAction($videoid)
    {
        $this->getCloud()->deleteVideo($videoid);
        $this->container->get("session")->setFlash(
            "success",
            "Video successfully deleted"
        );
        $router = $this->container->get("router");
        return $this->redirect($router->generate("xabbuh_panda_demo_videos"));
    }

    public function changeCloudAction()
    {
        $request = $this->getRequest();
        $this->container->get("session")->set("cloud", $request->query->get("cloud"));
        $router = $this->container->get("router");
        return $this->redirect($router->generate("xabbuh_panda_demo_videos"));
    }

    public function cloudAction()
    {
        $cloud = $this->getCloud()->getCloudData();
        return $this->render(
            "XabbuhPandaDemoBundle:Demo:cloud.html.twig",
            array("cloud" => $cloud)
        );
    }
    
    public function profileAction($profileid)
    {
        $profile = $this->getCloud()->getProfile($profileid);
        return $this->render(
            "XabbuhPandaDemoBundle:Demo:profile.html.twig",
            array("profile" => $profile)
        );
    }

    /**
     * @return \Xabbuh\PandaBundle\Cloud\Cloud
     */
    private function getCloud()
    {
        $key = $this->container->get("xabbuh_panda_demo.twig.cloud_extension")->getSelectedCloud();
        return $this->container->get("xabbuh_panda.cloud_manager")->getCloud($key);
    }
}
