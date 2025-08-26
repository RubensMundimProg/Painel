<?php
/**
 * Created by PhpStorm.
 * User: bruno.rosa
 * Date: 28/10/16
 * Time: 12:23
 */

namespace Dashboard\Service;

class Twitter
{

    public function start()
    {
        // The OAuth credentials you received when registering your app at Twitter.
        define("TWITTER_CONSUMER_KEY", "8nZqYXVBCpKskrZnlQB0caVT4");
        define("TWITTER_CONSUMER_SECRET", "2DXyRzgCuoZ4AAH0P2xE6ZWLR0NwYwUlxcToqPVMNfXH86TRAd");

        // The OAuth data for the twitter account
        define("OAUTH_TOKEN", "2805292805-pezle02XUlxHSPCOzh7fCV5wI8aINBBvK6WTsFs");
        define("OAUTH_SECRET", "FkkDSIQ7gNcqIM23FHNnz5R26Y0g6JA1xAZfve57wgLak");

        $txt = file_get_contents('./data/twitter/tags.txt');
        $words = explode(';',$txt);

        ksort($words);

        // Start streaming
        $sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
        $sc->setTrack($words);
        $sc->consume();
    }

} 