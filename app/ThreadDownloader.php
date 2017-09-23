<?php

namespace Application;

class ThreadDownloader
{

    private $host2ch = "2ch.hk";
    private $scheme2ch = "https://";
    private $responseHandler;
    private $executionStatus;

    function __construct()
    {
        $this->responseHandler = ResponseHandler::getInstance();
        $this->executionStatus = ExecutionStatus::getInstance();
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
            $this->responseHandler->addError("Файл не найден. Ошибка $httpCode");
            return null;
        }

        return $code;
    }

    /*
     * Отадет ссылку на проверку и вызывает функцию скачивания html страницы
     */

    public function getThreadHtmlCode($url)
    {
        if ($this->validateLink($url) === FALSE) {
            $this->responseHandler->addError("Не является ссылкой на тред: \"$url\".");
            return null;
        } else {
            $this->responseHandler->addSuccess('Ссылка правильная.');
            $code = $this->getHtmlCodeUrl($url);
            return $code;
        }
    }

    private function downloadFile(string $url, string $threadIdPath, string $fileName, string $filePath)
    {

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
            $this->responseHandler->addError("Файл не найден. Ошибка $httpCode");
            return false;
        }

        return true;
    }

    public function downloadImages(array $imagesLinksArray)
    {
        //var_dump($imagesLinksArray);
        
        $threadPaths = $this->parseFilePath($imagesLinksArray[0]);

        if (!file_exists($threadPaths["threadIdPath"])) {
            mkdir($threadPaths["threadIdPath"], 0700);
        }

        // отслеживает прогресс выполнения задачи
        //$executionStatus = new ExecutionStatus(count($imagesLinksArray));
        $this->executionStatus->setPlanned(count($imagesLinksArray));
        $status = 0;
        
        $this->responseHandler->addSuccess('Идет скачивание файлов на сервер...');
        
        foreach ($imagesLinksArray as $imageLink) {
            $imagePaths = $this->parseFilePath($imageLink);
            
            // Стикеры не будут скачиваться
            if (!is_null($imagePaths["imageLink"])) {
                $this->downloadFile($imagePaths["imageLink"], $imagePaths["threadIdPath"], $imagePaths["fileName"], $imagePaths["filePath"]);
            }
            
            $status++;

            // Записывает процесс выполнения в переменную сессии для статус-бара
            $this->executionStatus->setDone($status);
            $this->executionStatus->getParts();
        }

        // Получает количество успешео скачанных файлов
        $this->executionStatus->setDone($status);
        // Сообщает в переменную сессии что скрипт завершил выполнение
        //$this->executionStatus->setDownloadingComplete();
        $this->responseHandler->addSuccess($this->executionStatus->getParts());

        $this->archiveFiles($threadPaths["threadIdPath"], $threadPaths["threadId"]);
    }

    public function parseFilePath($path)
    {
        $fileNameExploded = explode("/", $path);
        $threadIdArrayIndex = count($fileNameExploded) - 2;
        $threadId = $fileNameExploded[$threadIdArrayIndex]; // id треда
        $threadIdPath = __DIR__ . "/../public/storage/threads/$threadId"; // Папка для скачанных файлов
        
        // Стикеры не будут скачиваться
        if ($fileNameExploded[2] !== "src") {
            $imageLink = null; 
        } else {
            $imageLink = $this->scheme2ch . $this->host2ch . $path; // Ссылка на сорцы картинки для скачивания
        }
        
        $fileName = array_pop($fileNameExploded); // Имя файла с расширением       
        $filePath = $threadIdPath . "/" . $fileName; // Путь к папке для скаченных файлов

        return array(
            "imageLink" => $imageLink,
            "threadIdPath" => $threadIdPath,
            "fileName" => $fileName,
            "filePath" => $filePath,
            "threadId" => $threadId
        );
    }

    public function archiveFiles(string $threadIdPath, string $zipName)
    {
        $this->responseHandler->addSuccess('Архивирование...');
        
        $zip = new \ZipArchive();
        $zipPath = __DIR__ . "/../public/storage/zip/$zipName.zip";

        if (file_exists($zipPath)) {
            $zip->open($zipPath, \ZipArchive::OVERWRITE);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            $this->responseHandler->addError("Не удалось создать архив.");
            return false;
        }

        $scandirResult = scandir($threadIdPath);
        $filePathsArray = array();

        foreach ($scandirResult as $filePath) {
            if (!is_dir($filePath)) {
                $filePathsArray[] = $threadIdPath . "/" . $filePath;
            }
        }
        
        foreach ($filePathsArray as $filePath) {
            $fileNameExploded = explode("/", $filePath);
            $fileName = array_pop($fileNameExploded);
            $zip->addFile($filePath, $fileName);
        }

        $zip->close();

        // Удаляет папку с картинками
        if ($this->removeDirectory($threadIdPath)) {
            $this->responseHandler->addSuccess('Неархивированная папка удалена.');
        } else {
            $this->responseHandler->addError("Не удалось удалить папку: \"$threadIdPath\"");
        }

        $this->responseHandler->addSuccess('Архив создан.');
        return true;
    }

    /**
     * Удаляет папку со всем её содержимым или файл
     */
    private function removeDirectory($dir)
    {
        $this->responseHandler->addSuccess('Очистка кэша...');
        
        if ($elements = glob($dir . "/*")) {

            foreach ($elements as $element) {
                if (is_dir($element)) {
                    $this->removeDirectory($element);
                } else {
                    unlink($element);
                }
            }

            return rmdir($dir);
        }
    }

}
