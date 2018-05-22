<?php
namespace cnuer\ShortVideo;

use cnuer\ShortVideo\Listener;
use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {
    $events->subscribe(Listener\AddBBCodeVideo::class);
    $events->subscribe(Listener\FormatShortVideoUrl::class);
};
