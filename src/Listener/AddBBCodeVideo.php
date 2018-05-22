<?php

namespace cnuer\ShortVideo\Listener;

use Flarum\Event\ConfigureFormatter;

class AddBBCodeVideo
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureFormatter::class, [$this, 'video']);
    }

    /**
     * @param ConfigureFormatter $event
     */
    public function video(ConfigureFormatter $event)
    {
        $event->configurator->BBCodes->addCustom(
            '[VIDEO poster={URL;optional} src={URL;useContent} height={NUMBER;optional} width={NUMBER;optional}]{TEXT}[/VIDEO]',
            '<video controls preload="metadata" poster="{@poster}" src="{@src}" height="{@height}" width="{@width}">{TEXT}</video>'
        );
    }
}
