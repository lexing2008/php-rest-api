<?php
namespace Config;

/**
 * Класс содержит локальные конфигурационные настройки,
 * которые замещают настройки Config файла.
 * Служит для развертывания сайта на нескольких площадках
 * Для DEV свой LocalConfig
 * Для PROD свой LocalConfig
 */
class LocalConfig {
   
    /**
     * Настройки соединения с БД
     * @var array 
     */
    public $db = [
        'host' => 'localhost',
        'name' => 'db_test',
        'user' => 'root',
        'password' => '',        
    ];
    
    /**
     * Домен сайта
     * @var string
     */
    public $domain = 'test2.by';
}