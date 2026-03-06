<?php

/*
 * This file is part of afrux/top-posters-widget.
 *
 * Copyright (c) 2021 Sami Mazouz.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Afrux\TopPosters\Console;

use Afrux\TopPosters\TopPostersCalculator;
use Illuminate\Console\Command;

class CalculateTopPostersCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'afrux:top-posters:calculate';

    /**
     * @var string
     */
    protected $description = 'Calculate top posters for the current month and save to database.';

    /**
     * @var TopPostersCalculator
     */
    protected $calculator;

    public function __construct(TopPostersCalculator $calculator)
    {
        parent::__construct();
        $this->calculator = $calculator;
    }

    public function handle()
    {
        $this->info('Calculating top posters...');
        $this->calculator->calculate();
        $this->info('Top posters calculated and saved to history table.');
    }
}
