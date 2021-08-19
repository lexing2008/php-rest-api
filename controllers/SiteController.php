<?php

namespace Controllers;

use Core\AbstractController;
use Helpers\CategoriesHierarchyHelper;

/**
 * Контроллер Site
 *
 * @author Алексей Согоян
 */
class SiteController extends AbstractController 
{
    /**
     * Главная страница
     */
    public function pageIndex(){
        $site = [];
        
        // подгружаем категории
        $categories = new CategoriesHierarchyHelper;
        $site['categories'] = $categories->getChildrens();
        
        $this->render('index.php', $site);
    }
}
