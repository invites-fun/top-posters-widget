<?php

/*
 * This file is part of afrux/top-posters-widget.
 *
 * Copyright (c) 2021 Sami Mazouz.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Afrux\TopPosters\Job;

use Afrux\TopPosters\TopPostersCalculator;
use Flarum\Queue\AbstractJob;

class UpdateTopPostersJob extends AbstractJob
{
    public function handle(TopPostersCalculator $calculator)
    {
        $calculator->calculate();
    }
}
