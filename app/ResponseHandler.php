<?php

namespace Application;

/*
 * Обработчик результатов выполнения методов классов 
 */

class ResponseHandler
{

    private static $instance;
    private $errors = array();
    private $success = array();

    private function __construct()
    {
        
    }

    public static function getInstance()
    {
        // проверяет, был ли уже создан объект и если нет, то создает его
        if (empty(self::$instance)) {
            // класс с закрытым конструктором может сам
            // себя создать
            self::$instance = new ResponseHandler();
        }
        // возвращает ссылку на созданный объект
        return self::$instance;
    }

    public function addError(string $errorString)
    {
        $this->errors[] = $errorString;
    }

    public function getErrorsArray()
    {
        return $this->errors;
    }

    public function addSuccess(string $errorString)
    {
        $this->success[] = $errorString;
    }

    public function getSuccessArray()
    {
        return $this->success;
    }

}

?>
