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

namespace CuyZ\Notiz\Domain\Event\Scheduler;

use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Event triggered when a task from the scheduler was executed.
 */
class SchedulerTaskWasExecutedEvent extends SchedulerTaskEvent
{
    /**
     * @label Event/Scheduler:task.execution_done.marker.result
     * @marker
     *
     * @var bool
     */
    protected $result;

    /**
     * @param AbstractTask $task
     * @param bool $result
     */
    public function run(AbstractTask $task, bool $result)
    {
        $this->checkTaskFilter($task);
        $this->fillTaskData($task);

        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getExampleProperties(): array
    {
        return parent::getExampleProperties() +
            [
                'result' => true,
            ];
    }
}
