<?php
namespace Helpers;

use Models\Category;

/**
 * Хэлпер CategoriesHierarchyHelper
 * позволяет работать с иерархическим списком категорий
 */
class CategoriesHierarchyHelper extends HierarchyHelper
{
    /**
     * Имя файла кэша
     */
    const CACHE_FILE = '/cache/categories.dat';
    
    /**
     * Получение элементов иерархического списка из кэша
     * @return array элементы иерархического списка
     */
    public function getItemsFromCache(): array 
    { 
        $items = [];
        // путь к файлу кэша
        $path = $this->getCachePath();
        // проверяем существование файла
        if(file_exists($path)){
            // получаем элементы иерархического списка
            $items = json_decode(file_get_contents($path), true);
        }
        
        return $items; 
    }
    
    /**
     * Получение элементов иерархического списка из таблицы
     * @return array массив элементов иерархического списка
     */
    public function getItemsFromTable(): array
    {
        return (new Category)->getAllCategories();
    }
    
    /**
     * Сохранение элементов иерархического списка в кэш
     */
    public function saveItemsToCache()
    {
        // преобразуем массив элементов иерархического списка в json
        $content = json_encode($this->items);
        // записываем кэш в файл
        file_put_contents($this->getCachePath(), $content);
    }
    
    /**
     * Удаляем файл кэша
     */
    public function deleteCache(){
        unlink($this->getCachePath());
    }
}