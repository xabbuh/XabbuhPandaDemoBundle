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

    /**
     * @return \Xabbuh\PandaBundle\Cloud\Cloud
     */
    private function getCloud()
    {
        $key = $this->container->get("xabbuh_panda_demo.twig.cloud_extension")->getSelectedCloud();
        return $this->container->get("xabbuh_panda.cloud_manager")->getCloud($key);
    }

    public function plainVideosAction()
    {
        $apiHost = "api-eu.pandastream.com";
        $path = "/videos.json";
        $accessKey = "927e30d33a55eb373a99";
        $secretKey = "13fa33b516893c1dcf6f";
        $cloudId = "dd0997b5952a0d00d3da993b927171b5";
        date_default_timezone_set("UTC");
        $params = array(
            "access_key" => $accessKey,
            "timestamp" => date("c"),
            "cloud_id" => $cloudId
        );

        $canonicalQueryString = str_replace(
            array("+", "%5B", "%5D"),
            array("%20", "[", "]"),
            http_build_query($params)
        );
        $stringToSign = "GET\napi-eu.pandastream.com\n/videos.json\n" . $canonicalQueryString;
        $params["signature"] = base64_encode(hash_hmac("sha256", $stringToSign, $secretKey, true));

        $ch = curl_init("api-eu.pandastream.com/v2/videos.json?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch) > 0) {
            // error handling
        }
        return $this->render(
            "XabbuhPandaDemoBundle:Demo:videos_plain.html.twig",
            array("text" => $response)
        );
    }
}
