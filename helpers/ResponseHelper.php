<?php
namespace Helpers;

/**
 * Хэлпер ответов сервера
 */
class ResponseHelper
{
    /**
     * Ответ JSON об успешном выполнении операции
     * @param array $data данные передаваемые в ответе JSON
     */
    public function jsonOk(array $data = []){
        
        $response = [
            'status'      => 'ok',
            'message'   => 'Operation completed successfully.',
        ];
        
        if(!empty($data)){
            $response['data'] = $data;
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE); // JSON_UNESCAPED_UNICODE устраняет проблему с кодировкой кириллицы
    }
    
    /**
     * Ответ JSON с ошибкой
     * @param array $messages сообщения об ошибке
     */
    public function jsonError(array $messages){
        
        echo json_encode([
            'status'      => 'error',
            'messages'   => $messages,
        ], JSON_UNESCAPED_UNICODE); // JSON_UNESCAPED_UNICODE устраняет проблему с кодировкой кириллицы
    }
}
