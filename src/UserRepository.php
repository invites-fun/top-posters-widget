<?php

/*
 * This file is part of afrux/top-posters-widget.
 *
 * Copyright (c) 2021 Sami Mazouz.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Afrux\TopPosters;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;

class UserRepository
{
    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function getTopPosters(): array
    {
        return $this->cache->rememberForever('afrux-top-posters-widget.top_poster_counts', function () {
            $currentMonthKey = Carbon::now()->format('Y-m');

            $records = TopPosterHistory::query()
                ->where('date_month', $currentMonthKey)
                ->orderBy('post_count', 'desc')
                ->limit(5)
                ->get();

            $counts = [];
            foreach ($records as $record) {
                $counts[$record->user_id] = (int) $record->post_count;
            }

            return $counts;
        }) ?: [];
    }
}
