<?php

namespace Models;

use Core\AbstractModel;
use Core\DB;
use Helpers\CategoriesHierarchyHelper;
use Helpers\ImageHelper;
use Models\PublicationCategory;

/**
 * Модель Публикация
 * Соответсвует записи в таблице publications
 */
class Publication extends AbstractModel 
{
    /**
     * Максимальная длина заголовка
     */
    const TITLE_MAX_LENGTH = 15;
    
    /**
     * Максимальный размер файла изображения
     */
    const IMAGE_MAX_WEIGHT = 3*1024*1024;
    
    /**
     * Ширина превьюшки
     */
    const THUMBS_WIDTH = 200;
    
    /**
     * Директория хранения изображений
     */
    const IMAGE_DIR = '/public/images/publications/';
    
    /**
     * Заголовок публикации
     * @var string 
     */
    private string $title;
    
    /**
     * Текст публикации
     * @var string 
     */
    private string $text;
    
    /**
     * Имя файла изображения
     * @var string 
     */
    private string $imgFile = '';
    
    /**
     * ID автора публикации
     * @var int
     */
    private int $authorId = 0;
    
    /**
     * Файл элемет массива $_FILES
     * @var array 
     */
    private ?array $fileImgFile = [];
    
    /**
     * Производит валидацию параметров
     * @return bool true - валидация пройдена, false - есть ошибки валидации
     */
    public function validate(): bool
    {
        // очищаем ошибки валидации
        $this->clearValidateErrors();
        
        if(empty($this->title)){
            $this->addValidateError('Поле "Заголовок" обязательно для заполнения');            
        }
        
        if(mb_strlen($this->title) > self::TITLE_MAX_LENGTH){
            $this->addValidateError('Максимальная длина "Заголовка" 15 символов');            
        }
        
        if(empty($this->text)){
            $this->addValidateError('Поле "Текст" обязательно для заполнения');
        }
        
        if(empty($this->authorId)){
            $this->addValidateError('Поле "ID автора" обязательно для заполнения');
        } else {
            // проверяем существование автора с таким ID
            if(!Author::isAuthorExist($this->authorId)){
                $this->addValidateError('Автор с таким ID не существует');
            }
        }
        
        if(!empty($this->fileImgFile['tmp_name'])){
            $message = (new ImageHelper)->validateImgFile($this->fileImgFile, self::IMAGE_MAX_WEIGHT);
            if(!empty($message)){
                $this->addValidateError($message);
            }
        }

        return empty($this->getValidateErrors());
    }
    
    /**
     * Установка названия публикации
     * @param string $title название публикации
     * @return Publication
     */
    public function setTitle(string $title): Publication {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Установка текста
     * @param string $text текст
     * @return Publication
     */
    public function setText(string $text): Publication {
        $this->text = $text;
        return $this;
    }

    /**
     * Установка имени изображения
     * @param string $value значение
     * @return Publication
     */
    public function setImgFile(string $value): Publication {
        $this->imgFile = $value;
        return $this;
    }
    
    /**
     * Установка ID автора
     * @param string $value значение
     * @return Publication
     */
    public function setAuthorId(string $value): Publication {
        $this->authorId = $value;
        return $this;
    }
    
    
    /**
     * Установка значения файла изображения
     * @param string $value значение элемент массива $_FILES
     * @return Publication
     */
    public function setFileImgFile(?array $value): Publication {
        $this->fileImgFile = $value;
        return $this;
    }
    
    /**
     * Вставка записи в таблицу
     * 
     * @return bool удалось ли вставить запись
     */
    public function insert(): bool {
        
        if($this->getId() || !$this->validate())
            return false;
        
        $db = DB::getInstance();
        $sth = $db->prepare('INSERT INTO `publications` 
                                SET `title` = :title, 
                                    `text` = :text,
                                    `author_id` = :author_id,
                                    `img_file` = :img_file');
        $sth->execute([
            'title' => $this->title,
            'text' => $this->text,
            'author_id' => $this->authorId,
            'img_file' => $this->imgFile,            
        ]);
        
        // получаем id записи
        $this->id = $db->lastInsertId();

        return $this->id > 0;
    }
    
    /**
     * Загрузка изображения на сервер
     */
    public function uploadImgFile(){
        if(!empty($this->fileImgFile['tmp_name'])){
            $fileName = (new ImageHelper)->uploadImgFile($this->fileImgFile, self::IMAGE_DIR, $this->id, self::THUMBS_WIDTH);
            $this->setImgFile($fileName);
            // обновляем запись в таблице
            $this->updateImgFileInTable();
        }
    }
    
    /**
     * Обновляет поле img_file в таблице
     * @return bool удалось обновить поле img_file в таблице
     */
    public function updateImgFileInTable(): bool {
        
        if(empty($this->id)){
            return false;
        }
        
        $db = DB::getInstance();
        $sth = $db->prepare('UPDATE `publications` 
                                SET `img_file` = :img_file
                                WHERE id = :id');
        $sth->execute([
            'img_file'  => $this->imgFile,
            'id'        => $this->id,
        ]);

        return true;
    }
    
    /**
     * Возвращает существует ли публикация с таким ID
     * @param int $id id публикации
     * @return bool существует ли публикация с таким ID
     */
    public static function isPublicationExist(int $id): bool {
        
        $sth = DB::getInstance()->prepare('SELECT count(*) FROM `publications` WHERE `id` = :id');
        $sth->execute(['id' =>  $id]);
        
        return $sth->fetchColumn();
    }
    
    /**
     * Возвращает публикацию по ID
     * @param int $id
     * @return array
     */
    public function getPublicationById(int $id): array {
        
        $sth = DB::getInstance()->prepare('SELECT * FROM `publications` WHERE `id` = :id');
        $sth->execute(['id' =>  $id]);
        
        return $sth->fetch();
    }
    
    /**
     * Возвращает все публикации
     * @return array все публикации
     */
    public function getAllPublications(): array {
        
        $data = [];
        
        $sth = DB::getInstance()->query('SELECT * FROM `publications`');
        while($row = $sth->fetch()){
            $data[] = $row;
        }
        
        return $data;
    }

    /**
     * Возвращает публикации по ID категории
     * @param int $categoryId ID категории
     * @return array массив данных публикаций
     */
    public function getPublicationsByCategoryId(int $categoryId): array {
        
        $data = [];
        
        // получаем потомков категории
        $categories = new CategoriesHierarchyHelper;
        $childrens = $categories->getChildrens($categoryId);
        $categoriesIds = array_column($childrens, 'id');
        $categoriesIds[] = $categoryId;
        
        // формируем sql запрос IN 
        $sqlWhere = '?' . str_repeat(',?', count($categoriesIds)-1);

        $sth = DB::getInstance()->prepare('SELECT DISTINCT p.*
                                            FROM publications_categories as cat
                                            JOIN publications as p 
                                            ON cat.publication_id = p.id
                                            WHERE 
                                            cat.category_id IN (' . $sqlWhere . ')');
        $sth->execute($categoriesIds);

        while($row = $sth->fetch()){
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Возвращает публикации по ID автора
     * @param int $authorId ID автора
     * @return array массив данных публикаций
     */
    public function getPublicationsByAuthorId(int $authorId): array {
        
        $data = [];
        
        $sth = DB::getInstance()->prepare('SELECT * FROM `publications` WHERE `author_id` = ?');
        $sth->execute([$authorId]);

        while($row = $sth->fetch()){
            $data[] = $row;
        }

        return $data;
    }
    
    /**
     * Поиск публикаций по названию и по ФИО автора
     * @param array $words массив слов по которым осуществляется поиск
     * @return array
     */
    public function searchByTitleAndAuthor(array $words): array {
        
        $data = [];
        
        if(empty($words))
            return $data;

        // формируем sql запрос
        $sqlWherePublications = 'p.title LIKE CONCAT("%", ?, "%")' . str_repeat(' OR p.title LIKE CONCAT("%", ?, "%")', count($words)-1);
        $sqlWhereAuthors = 'a.full_name LIKE CONCAT("%", ?, "%")' . str_repeat(' OR a.full_name LIKE CONCAT("%", ?, "%")', count($words)-1);
        
        $sth = DB::getInstance()->prepare('SELECT p.* 
                                            FROM publications as p
                                            JOIN authors as a ON a.id = p.author_id
                                            WHERE (' . $sqlWherePublications . ') OR (' . $sqlWhereAuthors . ')');
        $sth->execute(array_merge($words, $words));
        
        while($row = $sth->fetch()){
            $data[] = $row;
        }

        return $data;        
    }
    
    /**
     * Добавляем категории к публикации
     * @param array $categories массив ID категорий
     */
    public function addCategories(array $categoriesIds){
        
        foreach ($categoriesIds as $categoryId){
            $model = new PublicationCategory;
            $model->setCategoryId($categoryId)
                  ->setPublicationId($this->id)
                  ->insert();
        }
    }
}
