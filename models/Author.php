<?php

namespace Models;

use Core\AbstractModel;
use Core\DB;
use Helpers\ImageHelper;

/**
 * Модель Автор
 * Соответсвует записи в таблице authors
 */
class Author extends AbstractModel 
{
    /**
     * Максимальная длина ФИО
     */
    const FULL_NAME_MAX_LENGTH = 50;
    
    /**
     * Максимальный размер файла изображения
     */
    const IMAGE_MAX_WEIGHT = 2*1024*1024;
    
    /**
     * Ширина превьюшки
     */
    const THUMBS_WIDTH = 200;
    
    /**
     * Директория хранения изображений
     */
    const IMAGE_DIR = '/public/images/authors/';
    
    /**
     * ФИО автора
     * @var string 
     */
    private string $fullName;
    
    /**
     * Имя файла изображения
     * @var string 
     */
    private string $imgFile = '';
    
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
        
        if(empty($this->fullName)){
            $this->addValidateError('Поле "ФИО" обязательно для заполнения');            
        }
        
        if(mb_strlen($this->fullName) > self::FULL_NAME_MAX_LENGTH){
            $this->addValidateError('Максимальная длина "ФИО" 50 символов');            
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
     * Возвращает существует ли автор с таким ID
     * @param int $authorId id автора публикации
     * @return bool существует ли автор с таким ID
     */
    public static function isAuthorExist(int $authorId): bool {
        
        $sth = DB::getInstance()->prepare('SELECT count(*) FROM `authors` WHERE `id` = :id');
        $sth->execute(['id' =>  $authorId]);
        
        return $sth->fetchColumn();
    }
    
    /**
     * Установка значения ФИО
     * @param string $value значение
     * @return Author
     */
    public function setFullName(string $value): Author {
        $this->fullName = $value;
        return $this;
    }
    
    /**
     * Установка значения Названия файла изображения
     * @param string $value значение
     * @return Author
     */
    public function setImgFile(string $value): Author {
        $this->imgFile = $value;
        return $this;
    }
    
    /**
     * Установка значения файла изображения
     * @param string $value значение элемент массива $_FILES
     * @return Author
     */
    public function setFileImgFile(?array $value): Author {
        $this->fileImgFile = $value;
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
        $sth = $db->prepare('INSERT INTO `authors` 
                                SET `full_name` = :full_name, 
                                    `img_file` = :img_file');
        $sth->execute([
            'full_name' => $this->fullName,
            'img_file'  => $this->imgFile,            
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
        $sth = $db->prepare('UPDATE `authors` 
                                SET `img_file` = :img_file
                                WHERE id = :id');
        $sth->execute([
            'img_file'  => $this->imgFile,
            'id'        => $this->id,
        ]);

        return true;
    }
    
    /**
     * Возвращает всех авторов
     * @return array всех авторов
     */
    public function getAllAuthors(): array {
        
        $data = [];
        
        $sth = DB::getInstance()->query('SELECT * FROM `authors`');
        while($row = $sth->fetch()){
            $data[] = $row;
        }
        
        return $data;
    }

}
