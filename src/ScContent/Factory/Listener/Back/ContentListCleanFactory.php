<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Listener\Back;

use ScContent\Listener\Back\ContentListClean,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListCleanFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Listener\Back\ContentListClean
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $translator = $serviceLocator->get('translator');
        $optionsProvider = $serviceLocator->get(
            'ScService.Back.ContentListOptionsProvider'
        );
        $mapper = $serviceLocator->get('ScMapper.Back.ContentListClean');

        $listener = new ContentListClean();

        $listener->setTranslator($translator);
        $listener->setOptionsProvider($optionsProvider);
        $listener->setMapper($mapper);

        $events = $listener->getEventManager();
        $events->attach(
            'process.clean.pre',
            function($event) use ($serviceLocator) {
                $layoutListener = $serviceLocator->get(
                    'ScListener.Back.Layout'
                );
                $layoutListener->beforeCleaningTrash($event);

                $garbageListener = $serviceLocator->get(
                    'ScListener.Back.Garbage'
                );
                $garbageListener->beforeCleaningTrash($event);
            }
        );

        return $listener;
    }
}
