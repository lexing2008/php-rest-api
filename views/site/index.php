<?php
use Config\Config;

$config = Config::getInstance();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title> Главная страница</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="public/css/styles.css" type="text/css">
</head>

<body>
    <div class="wr">
        <?php
        // include 'header.php';?>

        <h1>
            Добро пожаловать на главную страницу сайта
        </h1>
        <p>
            Нажмите на ФИО автора для того, чтобы подгрузить публикации автора<br>
            Нажмите на название категории для того, чтобы подгрузить публикации категории<br>
        </p>       
        <p>
            <h2>Создание автора</h2>
            <form enctype="multipart/form-data" method="post" action="/api/authors/create">
                ФИО: <input type="text" name="fullName" value="Имя Фамилия"><br>
                Изображение: <input type="file" name="imgFile"><br>
                <input type="submit" name="button_form" value="Применить">

            </form>
            
            <h2>Создание публикации</h2>
            <form enctype="multipart/form-data" method="post" action="/api/publications/create">
                Заголовок: <input type="text" name="title" value="Заголовок публикации"><br>
                Текст: <input type="text" name="text" value="Текст публикации"><br>
                AuthorId: <input type="text" name="authorId" value="1"><br>
                Изображение: <input type="file" name="imgFile"><br>
                Категории:<br>
                <?php
                $size = count($site['categories']);
                for($i=0; $i<$size; ++$i):
                    $a = $site['categories'][$i]; ?>
                <div class="category level_<?=$a['level']?>">
                    <label><input type="checkbox" name="categories[]" value="<?=$a['id']?>"> <?=$a['name']?></label>
                </div>
                <?php
                endfor;?>
                
                <input type="submit" name="button_form" value="Применить">
            </form>
            
            <h2>Создание категории</h2>
            <form enctype="multipart/form-data" method="post" action="/api/categories/create">
                Название категории: <input type="text" name="name" value="Название категории"><br>
                parentId: <input type="text" name="parentId" value="0"><br>
                <input type="submit" name="button_form" value="Применить">
            </form>
            
        </p>
        <div id="app">
            <div id="mleft">
                <h2>Блок Категории</h2>
                <button v-on:click="loadCategories">Подгрузить категории</button>
                <div v-for="category in categories" v-bind:class="'category level_' + category.level" >
                    <span class="pointer" v-on:click="loadPublicationByCategoryId(category.id)">{{category.name}}</span>
                </div>

                <h2>Блок Авторы</h2>
                <button v-on:click="loadAuthors">Подгрузить авторов</button>
                <div v-for="author in authors">
                    <div>
                        <span class="pointer" v-on:click="loadPublicationByAuthorId(author.id)">{{author.full_name}}</span> 
                        <img v-if="author.img_file" v-bind:src="'public/images/authors/200/' + author.img_file">
                    </div>
                </div>
            </div>
            
            <div id="mright">
                <h2>Блок Поиск Ajax</h2>
                Поиск по названию и ФИО автора <input v-model="query" v-on:keydown="searchPublications"> <button v-on:click="searchPublications">Найти</button><br>
                Поиск по ID публикации <input v-model="publicationId"> <button v-on:click="loadPublicationById">Найти</button>

                <h2>Блок Публикации</h2>
                <button v-on:click="loadPublications">Подгрузить все публикации</button>
                <div v-for="item in publications">
                    <h3>{{item.title}} (id:{{item.id}})</h3>
                    {{item.created_at}}
                    <br>
                    <img v-if="item.img_file" v-bind:src="'public/images/publications/200/' + item.img_file">
                    <br>
                    {{item.text}}

                </div>
            </div>
        </div>
        
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
const DOMAIN = '<?=$config->domain?>';

let appCategories = new Vue({
	el: '#app',
	data: {
            publicationId: '',
            categories: [
            ],
            authors: [
            ],
            publications: [
            ]
	},
	methods: {
            loadCategories: function() {
                axios
                  .get('http://'+DOMAIN+'/api/categories/get')
                  .then(response => (this.categories = response.data.data));
            },
            loadAuthors: function() {
                axios
                  .get('http://'+DOMAIN+'/api/authors/GetAllAuthors')
                  .then(response => (this.authors = response.data.data));
            },
            loadPublications: function() {
                axios
                  .get('http://'+DOMAIN+'/api/publications/GetAllPublications')
                  .then(response => (this.publications = response.data.data));
            },
            searchPublications: function() {
                axios
                  .get('http://'+DOMAIN+'/api/publications/search?query=' + this.query)
                  .then(response => (this.publications = response.data.data));
            },
            loadPublicationById: function() {
                this.publications = [];
                axios
                  .get('http://'+DOMAIN+'/api/publications/GetPublicationById?id=' + this.publicationId)
                  .then(response => (this.publications = [response.data.data]));
            },
            loadPublicationByAuthorId: function(authorId){
                this.publications = [];
                axios
                  .get('http://'+DOMAIN+'/api/publications/GetPublicationsByAuthorId?authorId=' + authorId)
                  .then(response => (this.publications = response.data.data));
            },
            loadPublicationByCategoryId: function(categoryId){
                this.publications = [];
                axios
                  .get('http://'+DOMAIN+'/api/publications/GetPublicationsByCategoryId?categoryId=' + categoryId)
                  .then(response => (this.publications = response.data.data));
            }
	},
});
</script>

</body>
</html>
