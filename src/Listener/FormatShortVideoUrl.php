<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace cnuer\ShortVideo\Listener;

use Flarum\Event\PostWasPosted;
use Illuminate\Contracts\Events\Dispatcher;
use GuzzleHttp\Client as HttpClient;

class FormatShortVideoUrl
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(PostWasPosted::class, [$this, 'formatKuaishouVideo']);
        $events->listen(PostWasPosted::class, [$this, 'formatDouyinVideo']);
    }

    /**
     * @param ConfigureFormatter $event
     */
    public function formatKuaishouVideo(PostWasPosted $event)
    {
        $client = new HttpClient();
        if(preg_match_all('#(https?://www\.kuaishou\.com/photo/.+?)\s#is',$event->post->content,$m)){
            foreach($m[1] as &$url){
                $res = $client->request('GET', $url);
                if(200 == $res->getStatusCode()){
                    $content = $res->getBody()->getContents();
                    if(preg_match('/<video src="(.+?)".+?poster="(.+?)" height="(\d+)" width="(\d+)/is',$content,$m2)){
                        $event->post->content = str_replace($url,'[VIDEO poster="'.$m2[2].'" src="'.$m2[1].'" height="'.$m2[3].'" width="'.$m2[4].'"][/VIDEO]',$event->post->content);
                    }
                }
            }
        }
        if(preg_match_all('#(https?://www\.gifshow\.com/i/photo/.+?)\s#is',$event->post->content,$m)){
            foreach($m[1] as &$url){
                $res = $client->request('GET', $url);
                if(200 == $res->getStatusCode()){
                    $content = $res->getBody()->getContents();
                    if(preg_match('/<video src="(.+?)".+?poster="(.+?)" height="(\d+)" width="(\d+)"/is',$content,$m2)){
                        $event->post->content = str_replace($url,'[VIDEO poster="'.$m2[2].'" src="'.$m2[1].'" height="'.$m2[3].'" width="'.$m2[4].'"][/VIDEO]',$event->post->content);
                    }
                }
            }
        }
    }
    
    public function formatDouyinVideo(PostWasPosted $event){
        if(preg_match_all('#(https?://www\.iesdouyin\.com/share/video/.+?)\s#is',$event->post->content,$m)){
            $client = new HttpClient();
            foreach($m[1] as &$url){
                $res = $client->request('GET', $url);
                if(200 == $res->getStatusCode()){
                    $content = $res->getBody()->getContents();
                    if(preg_match('/var data = (.+?"}\]);/is',$content,$m2)){
                        $object = json_decode($m2[1]);
                        if($object){
                            $event->post->content = str_replace($url,'[VIDEO poster="'.$object[0]->video->cover->url_list[0].'" src="'.$object[0]->video->play_addr->url_list[0].'" height="'.$object[0]->video->height.'" width="'.$object[0]->video->width.'"][/VIDEO]',$event->post->content);
                        }
                    }
                }
            }
        }
    }
}
