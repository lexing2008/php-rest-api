<?php

namespace Helpers;

use Helpers\DirectoryHelper;

/**
 * Класс для работы с изображениями
 */
class ImageHelper {
    /**
     * Разделитель в списке форматов
     */
    const MESSAGE_FORMATS_GLUE = ',';
    
    /**
     * Точка
     */
    const DOT = '.';
    
    /**
     * Максимальное значение умноженной высоты на ширину изображения
     */
    const MAX_IMAGE_RESOLUTION = 10000000;
    
    /**
     * Изображение в формате JPG
     */
    const TYPE_IMG_JPEG = 2;

    /** 
     * Изменяет размер изображения $fin 
     * вписывая его в рамку с размерами $neww и $newh
     * и сохраняяя в $fout с качеством $quality
     * 
     * @param type $fout выходной файл
     * @param type $fin входной файл
     * @param type $neww ширина рамки, в котору требуется вписать исходное изображдение
     * @param type $newh высота рамки, в котору требуется вписать исходное изображдение
     * @param type $quality качество сжатия [1..100]
     * @return boolean
     */
    public static function scale($fout, $fin, $neww, $newh, $quality) {
        // сохраняем в себя
        if ($fout === NULL)
            $fout = $fin;

        if (!file_exists($fin))
            return false;

        $info = getimagesize($fin); //  получение информации о изображении 
        $ext = $info[2]; // тип изображения 

        $im = self::imageCreate($ext, $fin);

        imagealphablending($im, true);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 255, 255, 255, 0);

        if (!$im)
            return false;

        $k1 = $neww / imagesx($im);
        $k2 = $newh / imagesy($im);
        $k = $k1 > $k2 ? $k2 : $k1;

        $w = intval(imagesx($im) * $k);
        $h = intval(imagesy($im) * $k);

        $im1 = imagecreatetruecolor($w, $h);

        imagealphablending($im1, false);
        imagesavealpha($im1, true);
        $transparent = imagecolorallocatealpha($im1, 255, 255, 255, 127);

        imagecopyresampled($im1, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));

        self::imageSave($im1, $fout, $ext, $quality);

        imagedestroy($im);
        imagedestroy($im1);
        return true;
    }

    /**
     * Выбирает способ открытия файла в зависимости от типа изображения
     * @param string $ext расширение файла
     * @param string $fin путь к файлу
     * @return image дескриптор изображения
     */
    protected static function imageCreate($ext, $fin) {
        switch ($ext) {
            case self::TYPE_IMG_JPEG: 
                $im = imagecreatefromjpeg($fin);
                break;
            default:  /* если ничего не подошло */
                $im = null;
                
        }
        return $im;
    }

    /**
     * Выбор способа закрытия файла в зависимости от типа изображения 
     * @param image $im1 дескриптор изображения
     * @param string $fout путь к выходному файлу
     * @param string $ext расширение файла
     * @param int $quality качество сжатия
     */
    protected static function imageSave($im, $fout, $ext, $quality) {
        switch ($ext) {
            case self::TYPE_IMG_JPEG: // JPG
                imagejpeg($im, $fout, $quality);
                break;
        }
    }
    
    /**
     * Загрузка файла изображения на сервер
     * @param array $file элемент массива $_FILES
     * @param string $dir директория хранения изображений
     * @param string $fileName Имя файла без расширения
     * @param int $thumbsWidth ширина квадрата превьюшки, в который будет вписано изображение
     * @param int $quality качество сжатия 0-100
     * @return string имя сохраненного файла без пути
     */
    public function uploadImgFile(array $file, string $dir, string $fileName, int $thumbsWidth, int $quality = 80): string {
        $path_parts = pathinfo($file['name']);
        // устраняем проблему с регистром букв расширения файла
        $ext = strtolower($path_parts['extension']);

        $path = $_SERVER['DOCUMENT_ROOT'] . $dir . $fileName . self::DOT . $ext;
        // копируем фотографию, если есть, на сервер
        copy($file['tmp_name'], $path);

        // делаем превьюшку изображения
        $pathThumbs = $_SERVER['DOCUMENT_ROOT'] . $dir . $thumbsWidth . DirectoryHelper::SEPORATOR . $fileName . self::DOT . $ext;
        self::scale($pathThumbs, $path, $thumbsWidth, $thumbsWidth, $quality);
        
        return $fileName . self::DOT . $ext;
    }
    

    /**
     * Загрузка файла фотографии на сервер
     */
    /**
     * Валидация загруженного
     * @param array $file Элемент массива $_FILES
     * @param int $maxWeight максимальный вес изображения
     * @param array $allowedExt массив разрешенных расширений
     * @param array $allowedMimeTypes разрешенные mime types
     * @return string сообщение об проблеме или пустая строка, если валидация пройдена
     */
    public function validateImgFile(array $file, int $maxWeight, array $allowedExt = ['jpg'], array $allowedMimeTypes = ['image/jpeg']): string {

        if(empty($file['tmp_name'])){
            return 'Изображение не загружно';
        }
        
        if( $file['size'] > $maxWeight ){
            return 'Изображение должно быть размером до ' . $maxWeight . 'байт';
        } 
        
        $path_parts = pathinfo($file['name']);
        // устраняем проблему с регистром букв расширения файла
        $ext = strtolower($path_parts['extension']);
        if(!in_array($ext, $allowedExt) || !$this->checkMimeType($file['tmp_name'], $allowedMimeTypes)){
            return 'Вы загрузили файл неподдерживаемого формата. Допустимые форматы файлов: ' . implode(self::MESSAGE_FORMATS_GLUE, $allowedExt);
        } else {
            // проверяем разрешение изображения
            $info = getimagesize( $file['tmp_name'] );
            if( $info[0]*$info[1] > self::MAX_IMAGE_RESOLUTION ){
                return 'Прикрепленное изображение имеет слишком большое разрешение. Наш сервер не может его обработать. Пожалуйста, прикрепите другое изображение.';
            }
        }
        
        return '';
    }
    
    /**
     * Проверка MIME TYPE файла
     * @return bool MIME type разрешен
     */
    public function checkMimeType(string $fileName, array $allowedMimeTypes): bool {
        
        // проверка заголовка файла
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileName );
        finfo_close($finfo);
        
        return in_array($mime, $allowedMimeTypes);
    }
}
