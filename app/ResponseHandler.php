<?php

namespace Application;

/*
 * Обработчик результатов выполнения методов классов. Синглтон. 
 */

class ResponseHandler
{

    private static $instance;
    private $errors = array();
    private $success = array();
    private $executionStatus;

    private function __construct()
    {
        $this->executionStatus = ExecutionStatus::getInstance();
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
        $this->executionStatus->setSessionVariable('statusText', $errorString);
    }

    public function getErrorsArray()
    {
        return $this->errors;
    }

    public function addSuccess(string $successString)
    {
        $this->success[] = $successString;
        $this->executionStatus->setSessionVariable('statusText', $successString);
    }

    public function getSuccessArray()
    {
        return $this->success;
    }

}

?>
