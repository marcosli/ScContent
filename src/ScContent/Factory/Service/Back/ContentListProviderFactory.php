<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Service\Back;

use ScContent\Service\Back\ContentListProvider,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListProviderFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ScContent\Service\Back\ContentListProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $optionsProvider = $serviceLocator->get(
            'ScService.Back.ContentListOptionsProvider'
        );
        $searchMapper = $serviceLocator->get('ScMapper.Back.ContentSearch');
        $listMapper = $serviceLocator->get('ScMapper.Back.ContentList');

        $service = new ContentListProvider();

        $service->setOptionsProvider($optionsProvider);
        $service->setMapper($searchMapper, 'search');
        $service->setMapper($listMapper, 'list');

        return $service;
    }
}
