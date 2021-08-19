<?php
namespace Core;

use Config\Config;
use Core\Trates\Singleton;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Класс работы с БД через PDO
 *
 * @author Алексей Согоян
 */
class DB {
    /**
     * Делаем класс Одиночкой
     */
    use Singleton;
    
    /**
     * Объект PDO
     * @var PDO 
     */
    private $pdo = NULL;
    
    /**
     * Соединение с базой данныз Mysql
     */
    public function connect(){
        $config = Config::getInstance();

        $dsn = 'mysql:host=' . $config->db['host'] .';dbname=' . $config->db['name'] .';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $this->pdo = new PDO($dsn, $config->db['user'], $config->db['password'], $options);            
        } catch (PDOException $ex) {
            die('Ошибка подключения к БД: ' . $ex->getMessage());
        }
    }
    
    /** 
     * Возврашает объект PDO
     * @return PDO объект PDO
     */
    public function pdo(){
        return $this->pdo;
    }
    

    /** 
     * Выполняет SQL запрос к БД
     * @param string $sql sql запрос
     * @return PDOStatement 
     */
    public function query(string $sql): PDOStatement {
        return $this->pdo->query($sql);
    }
    
    /**
     * Выполняет подготовленный SQL запрос к БД
     * @param string $sql sql запрос
     * @return PDOStatement 
     */
    public function prepare(string $sql): PDOStatement {
        return $this->pdo->prepare($sql);
    }
    
    /**
     * Возвращает ID последней вставленной записи
     * @return int ID последней вставленной записи
     */
    public function lastInsertId(): int {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Закрытие соединения с БД Mysql
     */
    public function close(){
        $this->pdo = NULL;
    }
}