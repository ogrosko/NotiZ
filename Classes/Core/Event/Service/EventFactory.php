<?php
declare(strict_types=1);

/*
 * Copyright (C)
 * Nathan Boiron <nathan.boiron@gmail.com>
 * Romain Canon <romain.hydrocanon@gmail.com>
 *
 * This file is part of the TYPO3 NotiZ project.
 * It is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License, either
 * version 3 of the License, or any later version.
 *
 * For the full copyright and license information, see:
 * http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace CuyZ\Notiz\Core\Event\Service;

use CuyZ\Notiz\Core\Definition\Tree\EventGroup\Event\EventDefinition;
use CuyZ\Notiz\Core\Event\Event;
use CuyZ\Notiz\Core\Exception\ClassNotFoundException;
use CuyZ\Notiz\Core\Exception\InvalidClassException;
use CuyZ\Notiz\Core\Notification\Notification;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class EventFactory implements SingletonInterface
{
    /**
     * @var EventRegistry
     */
    protected $eventRegistry;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param EventRegistry $eventRegistry
     * @param ObjectManager $objectManager
     */
    public function __construct(EventRegistry $eventRegistry, ObjectManager $objectManager)
    {
        $this->eventRegistry = $eventRegistry;
        $this->objectManager = $objectManager;
    }

    /**
     * @param EventDefinition $eventDefinition
     * @param Notification $notification
     * @return Event
     *
     * @throws ClassNotFoundException
     * @throws InvalidClassException
     */
    public function create(EventDefinition $eventDefinition, Notification $notification): Event
    {
        $className = $eventDefinition->getClassName();

        if (!class_exists($className)) {
            throw ClassNotFoundException::eventClassNotFound($className);
        }

        if (!in_array(Event::class, class_implements($className))) {
            throw InvalidClassException::eventHasMissingInterface($className);
        }

        /** @var Event $event */
        $event = $this->objectManager->get($className, $eventDefinition, $notification);

        return $event;
    }
}
