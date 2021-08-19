<?php
namespace Core;

use Core\DB;
use Config\{Config, LocalConfig};
use Core\Trates\Singleton;

/**
 * Класс приложения MVC
 */
class App
{
    /**
     * Делаем класс Одиночкой
     */
    use Singleton;
    
    /**
     * Разделитель элементов в URL 
     */
    const URL_DELIMITER = '/';
    
    /**
     * Контроллер по умолчанию
     */
    const DEFAULT_CONTROLLER = 'site';
    
    /**
     * Экшен по умолчанию
     */
    const DEFAULT_ACTION = 'pageIndex';
    
    /**
     * Префикс названия экшена
     */
    const ACTION_NAME_PREFIX = 'page';
    
    /**
     * Префикс домена для параметра сессии
     */
    const DOMAIN_PREFIX = '.';
    
    /**
     * Префикс имени контроллера
     */
    const CONTROLLER_NAME_PREFIX = 'Controllers\\';

    /**
     * Постфикс имени контроллера
     */
    const CONTROLLER_NAME_POSTFIX = 'Controller';
    
    /**
     * Количество элементов запроса для модуля
     */
    const MODULE_COUNT_REQUEST_ITEMS = 3;
    
    /**
     * Префикс имени модуля
     */
    const MODULE_NAME_PREFIX = 'Modules\\';
    
    /**
     * Разделитель в пространстве имен
     */
    const NAMESPACE_SEPORATOR = '\\';
    
    /**
     * Параметры запроса
     * @var array 
     */
    public array $requestParams = [];
    
    /**
     * Запуск приложения
     */
    public function run(){
        $config = Config::getInstance();
        // установка значений конфига из локального конфига
        $config->setParamsFromLocalConfig();
                
        // запускаем сессию
        $this->session_start();
        
        // подключаемся к БД
        $db = DB::getInstance();
        $db->connect();

        // роутинг запроса
        $this->routing();

        // закрываем соединение с БД
        $db->close();   
    }

    /**
     * Старт сессии
     */
    public function session_start(){
        $config = Config::getInstance();

        session_name( $config->sessionName );
        session_set_cookie_params($config->sessionLifetime , '/' , self::DOMAIN_PREFIX . $config->domain );
        session_start();        
    }
    
    /**
     * Роутинг. Запуск нужного экшена нужного контроллера
     */
    public function routing(){

        if( empty($_GET['url_request_string']) ){
            // устанавливаем конктроллер по умолчанию
            $controllerName = self::DEFAULT_CONTROLLER;
            $action = self::DEFAULT_ACTION;
        } else {
            // разбиваем строку запроса на элементы
            $urlItems = explode(self::URL_DELIMITER, $_GET['url_request_string']);
            if(count($urlItems) == self::MODULE_COUNT_REQUEST_ITEMS){
                // название модуля
                $moduleName = array_shift($urlItems);
            }
            // устанавливаем имя контроллера
            $controllerName = array_shift($urlItems);
            // устанавливаем имя экшена
            $urlActionItem  = array_shift($urlItems);
            if(empty($urlActionItem)){
                // экшен по умолчанию
                $action = self::DEFAULT_ACTION;
            } else {
                $action = self::ACTION_NAME_PREFIX . $urlActionItem;                
            }
            $this->requestParams = $urlItems;
        }
        
        // формируем полное имя контроллера
        if(isset($moduleName)){
            $controllerName = self::MODULE_NAME_PREFIX . $moduleName . self::NAMESPACE_SEPORATOR  . $controllerName . self::CONTROLLER_NAME_POSTFIX ;            
        } else {
            $controllerName = self::CONTROLLER_NAME_PREFIX . $controllerName . self::CONTROLLER_NAME_POSTFIX ;            
        }

        // проверяем наличие контроллера
        if( class_exists($controllerName) ){
            // создаем объект контроллера
            $controller = new $controllerName;
            // проверяем наличие экшена в контроллере
            if( method_exists($controller, $action) ) {
                $controller->$action();
            } else {
                throw new \Exception('Ошибка! Такого action не существует: ' . $methodName);
            }
        } else {
            throw new \Exception('Не удалось найти такой контроллер: ' . $controllerName);
        }
    }
}
