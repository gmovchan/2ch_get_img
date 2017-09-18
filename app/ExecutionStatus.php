<?php

namespace Application;

/**
 * Отслеживает прогресс выполнения задачи. Синглтон.
 */
class ExecutionStatus
{

    private static $instance;
    private $planned = 0; // Запланировано частей
    private $done = 0; // Выполненно частей

    function __construct()
    {
        
    }

    public static function getInstance()
    {
        // проверяет, был ли уже создан объект и если нет, то создает его
        if (empty(self::$instance)) {
            // класс с закрытым конструктором может сам
            // себя создать
            self::$instance = new ExecutionStatus();
        }
        // возвращает ссылку на созданный объект
        return self::$instance;
    }

    public function setPlanned(int $planned)
    {
        $this->planned = $planned;
    }

    public function setDone(int $done)
    {
        $this->done = $done;
    }

    public function getDone()
    {
        return $this->done;
    }

    /**
     * Возвращает отношение количества выполненных частей к запланированным в виде дроби
     */
    public function getParts()
    {
        $parts = $this->done . "/" . $this->planned;
        $this->setSessionVariable('statusBar', $parts);
        return $parts;
    }

    /**
     * Возвращает процент выполненных частей
     */
    public function getPercentage()
    {
        $percentage = floor(($this->done / $this->planned) * 100);
        return $percentage;
    }

    /**
     * Сохраняет в переменной сессии факт завершения выполнения скрипта
     */
    public function setDownloadingComplete()
    {
        $this->setSessionVariable('downloadingComplete', true);
    }

    /**
     * Устанавливает значение переменной в сессии.
     * @param string $variableName
     * @param string $variableValue
     */
    public function setSessionVariable(string $variableName, string $variableValue)
    {
        session_start();
        $_SESSION[$variableName] = $variableValue;
        session_write_close();
    }

}
