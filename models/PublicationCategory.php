<?php

namespace Models;

use Core\AbstractModel;
use Core\DB;
use Models\Publication;
use Models\Category;

/**
 * Модель КатегорияПубликации
 * Соответсвует записи в таблице publications_categories
 */
class PublicationCategory extends AbstractModel 
{
    /**
     * ID публикации
     * @var string 
     */
    private string $publicationId ;
    
    /**
     * ID категории
     * @var string 
     */
    private string $categoryId;
    
    
    /**
     * Производит валидацию параметров
     * @return bool true - валидация пройдена, false - есть ошибки валидации
     */
    public function validate(): bool
    {
        // очищаем ошибки валидации
        $this->clearValidateErrors();
        
        if(empty($this->publicationId)){
            $this->addValidateError('Поле "publicationId" обязательно для заполнения');
        } elseif(!Publication::isPublicationExist($this->publicationId)){
            $this->addValidateError('Такая публикация не существует');
        }
        
        if(empty($this->categoryId)){
            $this->addValidateError('Поле "categoryId" обязательно для заполнения');            
        } elseif(!Category::isCategoryExist($this->categoryId)){
            $this->addValidateError('Такая категория не существует');
        }
        
        return empty($this->getValidateErrors());
    }
    
    /**
     * Установка ID публикации
     * @param string $value значение
     * @return PublicationCategory
     */
    public function setPublicationId(int $value): self {
        $this->publicationId = $value;
        return $this;
    }

    /**
     * Установка ID категории
     * @param string $value значение
     * @return PublicationCategory
     */
    public function setCategoryId(int $value): self {
        $this->categoryId = $value;
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
        $sth = $db->prepare('INSERT INTO `publications_categories` 
                                SET `publication_id` = :publication_id, 
                                    `category_id` = :category_id');
        $sth->execute([
            'publication_id' => $this->publicationId,
            'category_id'  => $this->categoryId,
        ]);

        return true;
    }
}
