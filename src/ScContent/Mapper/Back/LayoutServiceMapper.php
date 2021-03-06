<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Mapper\Installation\LayoutMapper,
    ScContent\Options\ModuleOptions,
    ScContent\Entity\Back\Regions,
    ScContent\Entity\Widget,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutServiceMapper extends LayoutMapper
{
    /**
     * @var \ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     * @param \ScContent\Options\ModuleOptions  $options
     */
    public function __construct(
        AdapterInterface $adapter,
        ModuleOptions $options
    ) {
        $this->setAdapter($adapter);
        $this->moduleOptions = $options;
    }

    /**
     * @param  string $themeName
     * @return \ScContent\Entity\Back\Regions
     */
    public function findRegions($themeName)
    {
        $moduleOptions = $this->moduleOptions;
        $theme = $moduleOptions->getThemeByName($themeName);

        $list = new Regions($theme);
        $widgets = $moduleOptions->getWidgets();
        if (! is_array($widgets)) {
            return $list;
        }

        if (! isset($theme['frontend']['regions'])
            || ! is_array($theme['frontend']['regions'])
         ) {
            return $list;
        }
        $regions = $theme['frontend']['regions'];
        $availableRegions = array_keys($regions);
        if (! in_array('none', $availableRegions)) {
            $availableRegions[] = 'none';
        }
        $availableWidgets = array_keys($widgets);

        $select = $this->getSql()
            ->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->where([
                'theme'  => $themeName,
                'region' => $availableRegions,
                'name'   => $availableWidgets,
            ])
            ->order(['region ASC', 'position ASC']);

        $results = $this->execute($select);

        $hydrator = $this->getHydrator();
        $itemPrototype = new Widget();
        foreach ($results as $result) {
            $item = clone ($itemPrototype);
            $hydrator->hydrate($result, $item);
            $list->addItem($item);
        }
        return $list;
    }

    /**
     * @param  integer $id
     * @return void
     */
    public function deleteItem($id)
    {
        $this->beginTransaction();

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::WidgetsTableAlias))
            ->where([
                'widget' => $id,
            ]);

        $this->execute($delete);

        $select = $this->getSql()->select()
            ->columns(['theme', 'region', 'position'])
            ->from($this->getTable(self::LayoutTableAlias))
            ->where([
                'id' => $id
            ]);

        $result = $this->execute($select)->current();

        if (empty($result)) {
            $this->commit();
            return;
        }

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::LayoutTableAlias))
            ->where([
                'id' => $id,
                ]);

        $this->execute($delete);

        $update = $this->getSql()->update()
            ->table($this->getTable(self::LayoutTableAlias))
            ->set([
                'position' => new Expression('position - 1')
            ])
            ->where([
                'theme    = ?' => $result['theme'],
                'region   = ?' => $result['region'],
                'position > ?' => $result['position']
            ]);

        $this->execute($update);

        $this->commit();
    }
}
