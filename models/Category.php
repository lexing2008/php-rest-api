<?php

namespace Models;

use Core\AbstractModel;
use Core\DB;

/**
 * Модель Категория
 * Соответсвует записи в таблице categories
 */
class Category extends AbstractModel 
{
    /**
     * Максимальная длина названия категории
     */
    const NAME_MAX_LENGTH = 40;
    
    /**
     * Название категории
     * @var string 
     */
    private string $name;
    
    /**
     * ID родительской категории
     * @var string 
     */
    private int $parentId = 0;
    
    /**
     * Производит валидацию параметров
     * @return bool true - валидация пройдена, false - есть ошибки валидации
     */
    public function validate(): bool
    {
        // очищаем ошибки валидации
        $this->clearValidateErrors();
        
        if(empty($this->name)){
            $this->addValidateError('Поле "Название категории" обязательно для заполнения');            
        }
        
        if(mb_strlen($this->name) > self::NAME_MAX_LENGTH){
            $this->addValidateError('Максимальная длина "Названия категории" 40 символов');
        }

        return empty($this->validateErrors);
    }
    
    /**
     * Установка значения названия категории
     * @param string $value значение
     * @return Category
     */
    public function setName(string $value): Category {
        $this->name = $value;
        return $this;
    }

    /**
     * Установка значения ID родительской категории
     * @param string $value значение
     * @return Category
     */
    public function setParentId(string $value): Category {
        $this->parentId = $value;
        return $this;
    }
    
    /**
     * Вставка записи в таблицу
     * @return bool удалось ли вставить запись
     */
    public function insert(): bool {
        
        if($this->getId() || !$this->validate())
            return false;
        
        $db = DB::getInstance();
        $sth = $db->prepare('INSERT INTO `categories` 
                                SET `name` = :name, 
                                    `parent_id` = :parent_id');
        $sth->execute([
            'name' => $this->name,
            'parent_id' => $this->parentId,            
        ]);

        // получаем id записи
        $this->id = $db->lastInsertId();

        return $this->id > 0;
    }
    
    /**
     * Возвращает существует ли категория с таким ID
     * @param int $id id категории
     * @return bool существует ли категория с таким ID
     */
    public static function isCategoryExist(int $id): bool {
        
        $sth = DB::getInstance()->prepare('SELECT count(*) FROM `categories` WHERE `id` = :id');
        $sth->execute(['id' =>  $id]);
        
        return $sth->fetchColumn();
    }
    
    /**
     * Возвращает все категории
     * @return array массив данных категорий
     */
    public function getAllCategories(): array {
        
        $data = [];
        
        $sth = DB::getInstance()->query('SELECT * FROM `categories`');

        while($row = $sth->fetch()){
            $data[] = $row;
        }

        return $data;
    }
}
