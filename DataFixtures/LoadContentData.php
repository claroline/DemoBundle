<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\DemoBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Claroline\CoreBundle\Entity\Content;
use Claroline\CoreBundle\Entity\Home\Type;
use Claroline\CoreBundle\Entity\Home\Content2Type;

class LoadContentData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $contents = array(
            array('ClarolineConnect©', 'text1', 'home',      'content-6'),
            array('',                  'text2', 'home',      'content-6'),
            array('',                  'text3', 'home',      'content-5'),
            array('Demo',              'text4', 'home',      'content-7'),
            array('',                  'text7', 'home',      'content-6'),
            array('',                  'text8', 'home',      'content-6'),
            array('Youtube',           'text3', 'opengraph', 'content-12'),
            array('Vimeo',             'text5', 'opengraph', 'content-12'),
            array('Wikipedia',         'text6', 'opengraph', 'content-12')
        );

        $textDir = __DIR__. '/files/homepage';
        $locales = array('en', 'fr', 'es');

        foreach ($contents as $data) {
            $title = $data[0];
            $textName = $data[1];
            $type  = $manager->getRepository('ClarolineCoreBundle:Home\Type')->findOneBy(array('name' => $data[2]));
            $size  = $data[3];
            $ds = DIRECTORY_SEPARATOR;
            $content = new Content();
            $content->setTitle($title);

            foreach ($locales as $locale) {
                $content->setContent(file_get_contents($textDir . $ds . $textName . '.' . $locale . '.html', 'r'));
                $content->setTranslatableLocale($locale);
                $manager->persist($content);
                $manager->flush();
            }

            $first = $manager->getRepository('ClarolineCoreBundle:Home\Content2Type')->findOneBy(
                array('back' => null, 'type' => $type)
            );

            $contentType = new Content2Type($first);
            $contentType->setContent($content);
            $contentType->setType($type);
            $contentType->setSize($size);
            $manager->persist($contentType);
            $manager->flush();
        }
    }
}
