<?php

namespace Core\Trates;

/**
 * Трейт реализует паттерн Одиночка (Singleton)
 */
trait Singleton {
    /**
     * Экземпляр класса
     * @var mixed 
     */
    private static $instance = null;

    /**
     * Защищаем от создания через new Singleton
     */
    private function __construct() { }
    
    /**
     * Защищаем от создания через клонирование
     */
    private function __clone() { }
    
    /**
     * Защищаем от создания через unserialize
     */
    private function __wakeup() { }

    /**
     * Получить экземляр класса
     * @return mixed
     */
    public static function getInstance(): self {
        return 
            self::$instance === null
                ? self::$instance = new static() // Если $instance равен 'null', то создаем объект new static()
                : self::$instance; // Иначе возвращаем существующий объект 
    }
}