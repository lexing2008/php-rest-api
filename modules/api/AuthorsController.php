<?php

namespace Modules\Api;

use Core\AbstractController;
use Models\Author;
use Helpers\ResponseHelper;

/**
 * Контроллер Author
 * позволяем работать с Авторами
 * @author Алексей Согоян
 */
class AuthorsController extends AbstractController 
{
    /**
     * Cоздать автора
     */
    public function pageCreate(){
        $fullName   = (string)$_POST['fullName'];
        
        $author = new Author;
        $author->setFullName($fullName)
               ->setFileImgFile($_FILES['imgFile']);
        
        if($author->insert()){
            $author->uploadImgFile();
            (new ResponseHelper)->jsonOk(['authorId' => $author->getId()]);
        } else {
            (new ResponseHelper)->jsonError($author->getValidateErrors());
        }
    }
    
    /**
     * Вывести всех авторов
     * URL: /api/authors/GetAllAuthors
     */
    public function pageGetAllAuthors(){
        
        // получаем данные всех публикаций
        $data = (new Author)->getAllAuthors();

        (new ResponseHelper)->jsonOk($data);
    }
}
