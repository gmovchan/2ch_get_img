<?php

namespace Application;

class ThreadParser
{

    function __construct()
    {
        
    }

    public function parseThread(ThreadDownloader $downloader, $url)
    {
        $code = $downloader->getThreadHtmlCode($url);
        if (is_null($code)) {
            return null;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // скрывает сообщения об ошибках HTML
        $dom->loadHTML($code);
                
        $nodes = $dom->getElementsByTagName("div");
        $sourceImageLinks = array();

        foreach ($nodes as $element) {
            $classy = $element->getAttribute("class");

            if (strpos($classy, "image") !== false) {
                $tagsA = $element->getElementsByTagName("a");

                foreach ($tagsA as $tagA) {
                    $sourceImageLinks[] = $tagA->getAttribute("href");
                }
            }
        }
        
        $downloader->downloadImages($sourceImageLinks);
        
        return "Файлы скачаны.";
    }

}
