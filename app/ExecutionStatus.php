<?php

namespace Application;

/**
 * Отслеживает прогресс выполнения задачи 
 */
class ExecutionStatus
{

    private $planned = 0; // Запланировано частей
    private $done = 0; // Выполненно частей

    function __construct(int $planned)
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
        return $this->done . "/" . $this->planned;
    }

    /**
     * Возвращает процент выполненных частей
     */
    public function getPercentage()
    {
        return floor(($this->done / $this->planned) * 100);
    }

}
