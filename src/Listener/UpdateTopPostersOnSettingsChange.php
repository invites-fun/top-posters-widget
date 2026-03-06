<?php

/*
 * This file is part of afrux/top-posters-widget.
 *
 * Copyright (c) 2021 Sami Mazouz.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Afrux\TopPosters\Listener;

use Afrux\TopPosters\Job\UpdateTopPostersJob;
use Flarum\Settings\Event\Saved;
use Illuminate\Contracts\Queue\Queue;

class UpdateTopPostersOnSettingsChange
{
    /**
     * @var Queue
     */
    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(Saved $event)
    {
        if (isset($event->settings['afrux-top-posters-widget.excludeGroups'])) {
            $this->queue->push(new UpdateTopPostersJob());
        }
    }
}
