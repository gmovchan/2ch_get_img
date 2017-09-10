<?php

namespace Application;

class ThreadDownloader
{

    private $host2ch = "2ch.hk";
    private $scheme2ch = "https://";

    function __construct()
    {
        
    }

    /*
     * Проверяет является ли строка ссылкой на тред
     * Подозреваю что с помощью регулярного выражения было бы гораздо короче
     */

    public function validateLink($url)
    {
        if (empty($url)) {
            return false;
        }

        $urlArray = parse_url($url);

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
            echo "Файл не найден. Ошибка $httpCode";
            return null;
        }

        echo "<br>";
        echo "Файл найден.";
        return $code;
    }
    
    /*
     * Отадет ссылку на проверку и вызывает функцию скачивания html страницы
     */

    public function getThreadHtmlCode($url)
    {
        if ($this->validateLink($url) === FALSE) {
            echo "Не является ссылкой на тред: \"$url\".";
        } else {
            echo 'Ссылка правильная.';
            $code = $this->getHtmlCodeUrl($url);
            return $code;
        }
    }
    
    private function downloadFile(string $filePathOn2ch) 
    {
        // Получает ссылка для скачивания
        $url = $this->scheme2ch . $this->host2ch . $filePathOn2ch;
        
        // Получает мся файла с расширением
        $fileName = explode("/", $filePathOn2ch);
        $fileName = array_pop($fileName);  
        
        // Путь к папке для скаченных файлов
        $filePath = __DIR__ . "/../storage/$fileName";
        $destFile = fopen($filePath, "w");
        $curl = curl_init(); // Инициализирую CURL
        curl_setopt($curl, CURLOPT_HEADER, 0); // Отключаю в выводе header-ы
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Возвратить данные а не показать в браузере
        curl_setopt($curl, CURLOPT_URL, $url); // Указываю URL
        curl_setopt($curl, CURLOPT_FILE, $destFile); // устанавливаем место на сервере, куда будет скопирован удаленной файл
        curl_exec($curl); // Получаю данные
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl); // Закрываю CURL сессию
        fclose($destFile);

        if ($httpCode !== 200) {
            echo "<br>";
            echo "Файл не найден. Ошибка $httpCode";
            return null;
        }

        echo "<br>";
        echo "Файл скачан.";
    }
    
    public function downloadImages(array $imagesLinksArray)
    {
        foreach ($imagesLinksArray as $link) {
            $this->downloadFile($link);
        }
    }
}
