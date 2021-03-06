<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) 2019 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Listeners;

use Flarum\Api\Event\WillGetData;
use Flarum\Api\Serializer;
use Flarum\Event\GetApiRelationship;
use Illuminate\Contracts\Events\Dispatcher;

class AddRecipientsRelationships
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(GetApiRelationship::class, [$this, 'getApiRelationship']);
        $events->listen(WillGetData::class, [$this, 'includeRecipientsRelationship']);
    }

    /**
     * @param WillGetData $event
     */
    public function includeRecipientsRelationship(WillGetData $event)
    {
        if ($event->controller->serializer === Serializer\DiscussionSerializer::class) {
            $event->addInclude(['recipientUsers', 'oldRecipientUsers', 'recipientGroups', 'oldRecipientGroups']);
        }
    }

    /**
     * @param GetApiRelationship $event
     *
     * @return \Tobscure\JsonApi\Relationship
     */
    public function getApiRelationship(GetApiRelationship $event)
    {
        if ($event->isRelationship(Serializer\BasicDiscussionSerializer::class, 'recipientUsers')) {
            return $event->serializer->hasMany($event->model, Serializer\UserSerializer::class, 'recipientUsers');
        }
        if ($event->isRelationship(Serializer\BasicDiscussionSerializer::class, 'oldRecipientUsers')) {
            return $event->serializer->hasMany($event->model, Serializer\UserSerializer::class, 'oldRecipientUsers');
        }

        if ($event->isRelationship(Serializer\BasicDiscussionSerializer::class, 'recipientGroups')) {
            return $event->serializer->hasMany($event->model, Serializer\GroupSerializer::class, 'recipientGroups');
        }
        if ($event->isRelationship(Serializer\BasicDiscussionSerializer::class, 'oldRecipientGroups')) {
            return $event->serializer->hasMany($event->model, Serializer\GroupSerializer::class, 'oldRecipientGroups');
        }

        if ($event->isRelationship(Serializer\UserSerializer::class, 'privateDiscussions')) {
            return $event->serializer->hasMany($event->model, Serializer\DiscussionSerializer::class, 'privateDiscussions');
        }
    }
}
