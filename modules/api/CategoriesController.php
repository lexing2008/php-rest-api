<?php

namespace Modules\Api;

use Core\AbstractController;
use Models\Category;
use Helpers\{ResponseHelper, HierarchyHelper, CategoriesHierarchyHelper};

/**
 * Контроллер Category
 * позволяем работать с Категориями
 * @author Алексей Согоян
 */
class CategoriesController extends AbstractController 
{
    /**
     * Cоздать категорию
     */
    public function pageCreate(){
        $name     = (string)$_POST['name'];
        $parentId = (int)$_POST['parentId'];
        
        $category = new Category;
        $category->setName($name)
                 ->setParentId($parentId);
        
        if($category->insert()){
            // удаляем кэш
            (new CategoriesHierarchyHelper(false))->deleteCache();
            
            (new ResponseHelper)->jsonOk(['categoryId' => $category->getId()]);
        } else {
            (new ResponseHelper)->jsonError($category->getValidateErrors());
        }
    }
    
    public function pageGet(){
        // получаем потомков категории
        $categories = new CategoriesHierarchyHelper;
        $childrens = $categories->getChildrens(HierarchyHelper::MAIN_PARENT_ID);
        (new ResponseHelper)->jsonOk($childrens);
    }
}
