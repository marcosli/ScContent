<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractList;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FilesList extends AbstractList
{
    /**
     * @param  File $item
     * @return void
     */
    public function addItem(File $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return File[]
     */
    public function getArrayCopy()
    {
        return $this->items;
    }

    /**
     * @param  array $data
     * @return void
     */
    public function exchangeArray($data)
    {
        return;
    }
}
