<?php

namespace Core;

use Core\App;

/**
 * Абстрактный класс контроллера
 */
abstract class AbstractController 
{
    /** 
     * Подключает необходимый View
     * @param string $viewTplFile имя файла View
     * @param array $site передаваемые во View данные
     */
    public function render(string $viewTplFile, array $site = []){
        // получаем короткое имя класса, без namespace в нижнем регистре
        $controllerShortName = (new \ReflectionClass($this))->getShortName();
        $controllerShortName = strtolower(str_replace(App::CONTROLLER_NAME_POSTFIX, '', $controllerShortName));
       
        // подключаем необходимый вид 
        $viewTplFile = "views/$controllerShortName/$viewTplFile";
        if(file_exists($viewTplFile) ){
            include_once $viewTplFile;
        } else {
            throw new \Exception("Вид $viewTplFile недоступен");
        }
    }
    
    /**
     * Перемещает пользователя на главную страницу сайта
     */
    public function gotoHome(){
        header('Location: /');
        die();
    }
}
