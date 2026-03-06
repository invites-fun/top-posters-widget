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

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class TopPosterHistory extends AbstractModel
{
    /**
     * @var string
     */
    protected $table = 'top_posters_history';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'date_month', 'post_count'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
