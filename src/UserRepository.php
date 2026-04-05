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
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as Cache;

class UserRepository
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(Cache $cache, SettingsRepositoryInterface $settings)
    {
        $this->cache = $cache;
        $this->settings = $settings;
    }

    public function getTopPosters(): array
    {
        $timezone = $this->settings->get('afrux-top-posters-widget.timezone', 'UTC');
        $now = Carbon::now($timezone);
        $currentMonthKey = $now->format('Y-m');
        $cacheKey = "afrux-top-posters-widget.top_poster_counts.{$currentMonthKey}";

        return $this->cache->rememberForever($cacheKey, function () use ($currentMonthKey) {
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
