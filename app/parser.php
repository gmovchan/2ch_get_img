<?php

namespace Application;

class Parser2ch
{

    private $host2ch = "2ch.hk";

    function __construct()
    {
        
    }

    /*
     * Проверяет является ли строка ссылкой на тред
     * Подозреваю что с помощью регулярного выражения было бы гораздо короче
     */

    public function validateLink($link)
    {
        if (empty($link)) {
            return false;
        }

        $urlArray = parse_url($link);

        if (!isset($urlArray["host"])) {
            return false;
        }

        if ($urlArray["host"] !== $this->host2ch) {
            return false;
        }

        if (!isset($urlArray["path"])) {
            return false;
        }

        $pathArray = explode('/', $urlArray["path"]);

        if (!isset($pathArray[3]) || empty($pathArray[3])) {
            return false;
        }

        if ($pathArray[2] !== "res") {
            return false;
        }

        $threadLink = explode('.', $pathArray[3]);

        if (count($threadLink) != 2) {
            return false;
        }

        if (!is_numeric($threadLink[0]) || $threadLink[1] !== "html") {
            return false;
        }

        return true;
    }

    public function parseThread($link)
    {
        if ($this->validateLink($link) === FALSE) {
            echo "Не является ссылкой на тред: \"$link\".";
        } else {
            echo 'Ссылка правильная.';
            $code = $this->getHtmlCodeUrl($link);
            
            if (is_null($code)) {
                return false;
            }
            
            echo '<hr>';
            echo htmlspecialchars($code);
        }
    }

    /*
     * Скачивает html-код треда для последующего разбора
     */

    private function getHtmlCodeUrl($url)
    {
        $curl = curl_init(); // Инициализирую CURL
        curl_setopt($curl, CURLOPT_HEADER, 0); // Отключаю в выводе header-ы
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Возвратить данные а не показать в браузере
        curl_setopt($curl, CURLOPT_URL, $url); // Указываю URL
        $code = curl_exec($curl); // Получаю данные
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl); // Закрываю CURL сессию

        if ($httpCode !== 200) {
            echo "<br>";
            echo "Файл не найден.";
            return null;
        }

        echo "<br>";
        echo "Файл найден.";
        return $code;
    }

}
