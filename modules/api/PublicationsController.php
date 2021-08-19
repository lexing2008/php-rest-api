<?php

namespace Modules\Api;

use Core\AbstractController;
use Models\Publication;
use Helpers\{ResponseHelper, StemmingHelper};

/**
 * Контроллер Publications
 *
 * @author Алексей Согоян
 */
class PublicationsController extends AbstractController 
{
    /**
     * Cоздавать публикацию
     * URL: /api/publications/create
     */
    public function pageCreate(){
        
        $title      = (string)$_POST['title'];
        $text       = (string)$_POST['text'];
        $authorId   = (int)$_POST['authorId'];
        $categories = (array)$_POST['categories'];
        
        $publication = new Publication;
        $publication->setTitle($title)
                    ->setText($text)
                    ->setAuthorId($authorId)
                    ->setFileImgFile($_FILES['imgFile']);
        
        if($publication->insert()){
            // загружаем изображение, если такое есть
            $publication->uploadImgFile();
            // добавляем категории
            $publication->addCategories($categories);
            
            (new ResponseHelper)->jsonOk(['publicationId' => $publication->getId()]);
        } else {
            (new ResponseHelper)->jsonError($publication->getValidateErrors());
        }
    }
    
    /**
     * Вывести публикацию по ID
     * URL: /api/publications/GetPublicationById
     */
    public function pageGetPublicationById(){
        
        $id   = (int)$_GET['id'];
        if(empty($id)){
            (new ResponseHelper)->jsonError(['Empty Id']);
            return;
        }
        
        // получаем данные публикации по id
        $data = (new Publication)->getPublicationById($id);
        if(empty($data)){
            (new ResponseHelper)->jsonError(['Publication not found']);
        } else {
            (new ResponseHelper)->jsonOk($data);
        }
    }
    
    /**
     * Вывести все публикации
     * URL: /api/publications/GetAllPublications
     */
    public function pageGetAllPublications(){
        
        // получаем данные всех публикаций
        $data = (new Publication)->getAllPublications();

        (new ResponseHelper)->jsonOk($data);
    }
    
    /**
     * Вывести все публикации по категории
     * URL: /api/publications/GetPublicationsByCategoryId
     */
    public function pageGetPublicationsByCategoryId(){
        
        $categoryId   = (int)$_GET['categoryId'];
        if(empty($categoryId)){
            (new ResponseHelper)->jsonError(['Empty categoryId']);
            return;
        }
        // получаем данные публикаций
        $data = (new Publication)->getPublicationsByCategoryId($categoryId);

        (new ResponseHelper)->jsonOk($data);
    }
    
    /**
     * Вывести все публикации по автору
     * URL: /api/publications/GetPublicationsByAuthorId
     */
    public function pageGetPublicationsByAuthorId(){
        
        $authorId   = (int)$_GET['authorId'];
        if(empty($authorId)){
            (new ResponseHelper)->jsonError(['Empty authorId']);
            return;
        }
        // получаем данные публикаций
        $data = (new Publication)->getPublicationsByAuthorId($authorId);

        (new ResponseHelper)->jsonOk($data);
    }
    
    /**
     * Поиск по названию публикации и автору
     * URL: /api/publications/search
     */
    public function pageSearch(){
        
        $query   = trim((string)$_GET['query']);
        if(empty($query)){
            (new ResponseHelper)->jsonError(['Empty query']);
            return;
        }

        // разбиваем строку на слова и удаляем окончания с помощью стемминга Портера
        $words = array_values((new StemmingHelper())->stem_string($_GET['query']));
        // получаем данные публикаций
        $data = (new Publication)->searchByTitleAndAuthor($words);

        (new ResponseHelper)->jsonOk($data);
    }
}
