<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\DemoBundle\Listener;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 *  @DI\Service()
 */
class ResponseListener
{
    private $container;

    /**
     * @DI\InjectParams({"container" = @DI\Inject("service_container")})
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @DI\Observe("kernel.response")
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (
            $this->container->getParameter('claroline.demo_bundle.display_counter')
            && $this->isMasterHttpRequest($event)
        ) {
            $this->appendDemoCounter($event->getResponse());
        }
    }

    private function isMasterHttpRequest(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest()
            && !$event->getRequest()->isXmlHttpRequest()
            && !in_array($event->getRequest()->attributes->get('_route'), $this->getExcludedRoutes())
            && 'GET' === $event->getRequest()->getMethod()
            && 200 === $event->getResponse()->getStatusCode()
            && !$event->getResponse() instanceof StreamedResponse
        ) {
            return true;
        }
    }

    private function appendDemoCounter(Response $response)
    {
        $counterJs = $this->container->get('templating.helper.assets')->getUrl('bundles/clarolinedemo/js/counter.js');
        $counterCss = $this->container->get('templating.helper.assets')->getUrl('bundles/clarolinedemo/css/counter.css');
        $scriptTag =
            '<script type="text/javascript" src="' . $counterJs . '"></script>' .
            '<link rel="stylesheet" type="text/css" href="' . $counterCss . '"></script>';

        //add the javascript and the css at the end of the head
        if ($pos = strpos($response->getContent(), '</head>')) {
            $response->setContent(substr_replace($response->getContent(), $scriptTag, $pos, 0));
        }

        //add the warning in the topbar before the "please-wait" div
        if ($pos = strpos($response->getContent(), '<div class="please-wait">')) {
            $rootDir = $this->container->getParameter('kernel.root_dir');
            $ds = DIRECTORY_SEPARATOR;
            $demoLastUpdateFile = $rootDir . $ds . 'config' . $ds . 'last_demo.txt';
            $interval = $this->container->getParameter('claroline.demo_bundle.period');
            $nextUpdate = (int) file_get_contents($demoLastUpdateFile) + $interval;
            $date = new \DateTime();
            $now = $date->getTimeStamp();
            $remaining = $nextUpdate - $now;
            $hours = floor($remaining / (3600));
            $minutes = floor(($remaining - ($hours * 3600)) / 60);
            $seconds = $remaining - ($hours * 3600) - ($minutes * 60);

            $message = $this->container->get('translator')->trans(
                'time_remaining',
                array('%hours%' => $hours, '%minutes%' => $minutes, '%seconds%' => $seconds),
                'platform'
            );

            $htmlElement = $this->container->get('templating')
                ->render(
                    'ClarolineDemoBundle::demoCounter.html.twig',
                    array('nextUpdate' => $nextUpdate, 'message' => $message)
                );

            $response->setContent(substr_replace($response->getContent(), $htmlElement, --$pos, 0));
        }
    }

    private function getExcludedRoutes()
    {
        return array(
            'bazinga_exposetranslation_js',
            'login_check',
            'login',
        );
    }
} 