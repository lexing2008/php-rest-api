<?php

namespace Core;

/**
 * Абстрактная модель
 */
abstract class AbstractModel 
{
    /**
     * ID записи
     * @var int 
     */
    protected int $id = 0;

    /**
     * Содержит массив сообщений об ошиках валидации
     * @var array 
     */
    private array $validateErrors = [];
    
    /**
    * Валидация данных модели
    */
    abstract public function validate(): bool;
    
    /**
     * Возвращает массив ошибок валидации
     * @return array массив ошибок валидации
     */
    public function getValidateErrors(): array {
        return $this->validateErrors;
    }
    
    /**
     * Очищает ошибки валидации
     */
    public function clearValidateErrors(){
        $this->validateErrors = [];
    }
    
    /**
     * Добавляет сообщение об ошибке валидации
     * @param string $errorMessage сообщение об ошибке валидации
     */
    public function addValidateError(string $errorMessage){
        $this->validateErrors[] = $errorMessage;
    }
    
    /**
     * Возвращает ID записи
     * @return int id записи
     */
    public function getId(): int {
        return $this->id;
    }
}
