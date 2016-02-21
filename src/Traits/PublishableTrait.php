<?php

namespace Jeremytubbs\Igor\Traits;

use Jeremytubbs\Igor\Repositories\EloquentContentableRespository as Publishable;

trait PublishableTrait
{
    public static function bootPublishableTrait()
    {
        $self = new self;
        $table = $self->getTable();
        $class = get_class($self);

        static::created(function($post) use ($table, $class) {
            Publishable::create($post->id, $table, $class);
        });

        static::updated(function($post) use ($table, $class) {
            Publishable::update($post->id, $table, $class);
        });

        static::deleted(function($post) use ($table, $class) {
            Publishable::delete($post, $table, $class);
        });
    }
}
