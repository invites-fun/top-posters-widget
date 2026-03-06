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
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as Cache;

class TopPostersCalculator
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(SettingsRepositoryInterface $settings, Cache $cache)
    {
        $this->settings = $settings;
        $this->cache = $cache;
    }

    public function calculate()
    {
        $excludeGroups = array_map('intval', json_decode($this->settings->get('afrux-top-posters-widget.excludeGroups', '[]'), true));
        $excludePrivate = (bool) $this->settings->get('afrux-top-posters-widget.excludePrivatePosts', true);

        $startOfMonth = Carbon::now()->startOfMonth();
        $currentMonthKey = Carbon::now()->format('Y-m');

        $counts = CommentPost::query()
            ->selectRaw('user_id, count(id) as count')
            ->where('created_at', '>=', $startOfMonth)
            ->whereNull('hidden_at')
            ->when($excludePrivate, function ($query) {
                if ($query->getConnection()->getSchemaBuilder()->hasTable('recipients')) {
                    $query->whereNotIn('discussion_id', function ($subQuery) {
                        $subQuery->select('discussion_id')
                            ->from('recipients')
                            ->whereNull('removed_at');
                    });
                }
            })
            ->when(!empty($excludeGroups), function ($query) use ($excludeGroups) {
                $query->whereNotIn('user_id', function ($subQuery) use ($excludeGroups) {
                    $subQuery->select('user_id')
                        ->from('group_user')
                        ->whereIn('group_id', $excludeGroups);
                });
            })
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->mapWithKeys(function (CommentPost $post) {
                return [$post->user_id => (int) $post->count];
            })
            ->toArray();

        // Upsert fresh Top 5 to top_posters_history table preserving existing IDs
        $topUserIds = array_keys($counts);
        foreach ($counts as $userId => $postCount) {
            TopPosterHistory::query()->updateOrCreate(
                ['user_id' => $userId, 'date_month' => $currentMonthKey],
                ['post_count' => $postCount]
            );
        }

        // Clean up any users who were in the top 5 earlier this month but have now fallen out
        TopPosterHistory::query()
            ->where('date_month', $currentMonthKey)
            ->whereNotIn('user_id', $topUserIds)
            ->delete();

        // Clear the widget frontend cache so it fetches fresh data
        $this->cache->forget('afrux-top-posters-widget.top_poster_counts');
    }
}
