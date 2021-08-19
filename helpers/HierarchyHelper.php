<?php
namespace Helpers;

/**
 * Хэлпер HierarchyHelper
 * позволяет работать с иерархическим списком
 */
abstract class HierarchyHelper 
{
    /**
     * ID главного родителя в иерархии
     */
    const MAIN_PARENT_ID = 0;
    
    /**
     * Имя файла кэша
     */
    const CACHE_FILE = '';
    
    /**
     * Категории
     * @var array 
     */
    public array $category = [];
    
    /**
     * Упорядоченные по дереву элементы
     * @var array
     */
    public $items = [];
    
    /**
     * Количество элементов в $items. Вычисляется как счетчик. используется как счетчик
     * @var int
     */
    protected int $currentCountItems = 0;
    
    /**
     * Получение элементов иерархического списка из таблицы
     */
    abstract public function getItemsFromTable(): array;
    
    /**
     * Получение элементов иерархического списка из кэша
     */
    abstract public function getItemsFromCache(): array; 
    
    /**
     * Сохранение элементов иерархического списка в кэш
     */
    abstract public function saveItemsToCache();
         
    /**
     * Конструктор класса
     * @param bool $autoLoad автоматическая загрука из кэша, если не получилось из кэша, то из БД при создании объекта
     */
    public function __construct(bool $autoLoad = true) 
    {
        // подгружаем всю информацию
        if ($autoLoad){
            // если не удалось подгрузить из кэша
            if(!$this->loadFromCache()){
                // подгружаем из таблицы
                $this->loadFromTable();
            }
        }
    }

    /**
     * Подгружает из кэша данные иерархии
     * @return bool получилось подгрузить из кэша данные иерархии
     */
    public function loadFromCache(): bool
    {
        $this->items = $this->getItemsFromCache();
        
        return !empty($this->items);
    }
        
    /**
     * Загрузка информации из БД и формирование правильной иерархической структуры
     */
    public function loadFromTable() 
    {
        // Получение элементов иерархического списка из таблицы
        $records = $this->getItemsFromTable();
        $rows = count($records);
        unset($this->category);
        unset($this->items);
        $this->category = [];
        for ($i = 0; $i < $rows; ++$i) {
            $this->category[$records[$i]['id']] = $records[$i];
        }

        // устанавливаем текущее количество в нуль
        $this->currentCountItems = 0;
        // приводим в структурированный вид иерархический список
        $this->nextItem( self::MAIN_PARENT_ID ); // (Родитель) pid = 0; (Уровень вложенности) level = 0; Начинаем отсчет уровня вложенности	
        // сохраняем в кэш
        $this->saveItemsToCache();
    }
    
    /**
     * Функция находит все элементы родителя
     * @param int $pid идентификатор родителя. Для самого верхнего это 0
     * @param int $level уровень вложенности
     */
    private function nextItem(int $pid, int $level = 0) 
    {
        // просматриваем весь массив
        foreach ($this->category as $key => $val) {
            // элемент пренадлежит родителю
            if ($val['parent_id'] == $pid) {
                // добавляем текущий элемент в наш массив упорядоченных элементов
                $this->items[$this->currentCountItems]          = $val;
                $this->items[$this->currentCountItems]['level'] = $level;
                // удаляем текущий элемент из массива
                unset($this->category[$key]);
                // увеличивае счетчик
                $this->currentCountItems++;
                // рекурсивно ищем потомков для данного
                $this->nextItem($key, $level + 1);
            }
        }
    }
    
    /** 
     * Возвращает массив потомков заданного элемента
     * @param int $parent  идентификатор родителя. Приведен к int.
     * @return array массив потомков
     */
    public function getChildrens(int $parent = 0): array 
    {
        if($parent == self::MAIN_PARENT_ID){
            return $this->items;
        }
        
        $arr        = [];
        $flagLevel  = 0;
        $size       = count($this->items); 
        for ($i = 0; $i < $size; ++$i) {
            if ($this->items[$i]['id'] == $parent) {
                $flagLevel = $this->items[$i]['level'];
                ++$i;
                while ($i < count($this->items) && $this->items[$i]['level'] > $flagLevel) {
                    $arr[] = $this->items[$i];
                    ++$i;
                }
                return $arr;
            }
        }
        return $arr;
    }
    
    /**
     * Возвращает путь к файлу кэша
     * @return string путь к файлу кэша
     */
    protected function getCachePath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . static::CACHE_FILE;
    }
}