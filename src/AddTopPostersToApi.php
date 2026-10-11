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

use Flarum\Api\Serializer\ForumSerializer;

class AddTopPostersToApi
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ForumSerializer $serializer)
    {
        $data = $serializer->getActor()->can('viewForum') ? $this->repository->getTopPosters() : [];

        return [
            'afrux-top-posters-widget.topPosterCounts' => $data,
        ];
    }
}
