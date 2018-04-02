<?php

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
        $events->listen(PostWasPosted::class, [$this, 'formatPost']);
    }

    /**
     * @param PostWasPosted $event
     */
    public function formatPost(PostWasPosted $event)
    {
        //PostWasPosted居然是在保存之后才会调用。。那就再保存一次。。
        $event->post->content = $this->formatKuaishouVideo($event->post->content);
        $event->post->content = $this->formatDouyinVideo($event->post->content);
        $event->post->save();
    }
    
    public function formatKuaishouVideo(&$text)
    {
        $client = new HttpClient();
        if(preg_match_all('#^(https?://www\.kuaishou\.com/photo/.+?)(?:[\s\b]|$)#ism',$text,$m)){
            foreach($m[1] as &$url){
                $res = $client->request('GET', $url);
                if(200 == $res->getStatusCode()){
                    $content = $res->getBody()->getContents();
                    if(preg_match('/<video src="(.+?)".+?poster="(.+?)" height="(\d+)" width="(\d+)/is',$content,$m2)){
                        $text = str_replace($url,'[VIDEO poster="'.$m2[2].'" src="'.$m2[1].'" height="'.$m2[3].'" width="'.$m2[4].'"]'.$url.'[/VIDEO]',$text);
                    }
                }
            }
        }
        if(preg_match_all('#^(https?://www\.gifshow\.com/i/photo/.+?)(?:[\s\b]|$)#ism',$text,$m)){
            foreach($m[1] as &$url){
                $res = $client->request('GET', $url);
                if(200 == $res->getStatusCode()){
                    $content = $res->getBody()->getContents();
                    if(preg_match('/<video src="(.+?)".+?poster="(.+?)" height="(\d+)" width="(\d+)"/is',$content,$m2)){
                        $text = str_replace($url,'[VIDEO poster="'.$m2[2].'" src="'.$m2[1].'" height="'.$m2[3].'" width="'.$m2[4].'"]'.$url.'[/VIDEO]',$text);
                    }
                }
            }
        }
        
        return $text;
    }
    
    public function formatDouyinVideo($text)
    {
        if(preg_match_all('#^(https?://www\.iesdouyin\.com/share/video/.+?)(?:[\s\b]|$)#ism',$text,$m)){
            $client = new HttpClient();
            foreach($m[1] as &$url){
                $res = $client->request('GET', $url);
                if(200 == $res->getStatusCode()){
                    $content = $res->getBody()->getContents();
                    if(preg_match('/var data = (.+?"}\]);/is',$content,$m2)){
                        $object = json_decode($m2[1]);
                        if($object){
                            $text = str_replace($url,'[VIDEO poster="'.$object[0]->video->cover->url_list[0].'" src="'.$object[0]->video->play_addr->url_list[0].'" height="'.$object[0]->video->height.'" width="'.$object[0]->video->width.'"]'.$url.'[/VIDEO]',$text);
                        }
                    }
                }
            }
        }
        
        return $text;
    }
}
