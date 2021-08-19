<?php
namespace Config;

use Core\Trates\Singleton;

/**
 * Класс содержит конфигурационные настройки
 */
class Config {
    /**
     * Делаем класс Одиночку используя трейти Singleton
     */
    use Singleton;
    
    /**
     * Настройки соединения с БД
     * @var array 
     */
    public $db = [
        'host' => '',
        'name' => '',
        'user' => '',
        'password' => '',        
    ];
    
    /**
     * Домен сайта
     * @var string
     */
    public $domain = 'test2.by';
    
    /**
     * Имя сессии
     * @var string 
     */
    public $sessionName = 'TEST2_BY';
    
    /**
     * Время жизни сессии в секундах
     * @var int 
     */
    public $sessionLifetime = 1400;
    
    /**
     * Установка параметров из локального конфига
     */
    public function setParamsFromLocalConfig(){
        $vars = get_class_vars('Config\\LocalConfig');
        if(!empty($vars)){
            foreach ($vars as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}