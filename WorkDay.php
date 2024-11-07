<?php

require 'DB.php';

class WorkDay
{
    const REQUIRED_HOUR_DURATION = 8;
    public $pdo;
    public function __construct()
    {
        $db = new DB();
        $this->pdo = $db->pdo;
    }
    public function store(string $name, string $arrived_at, string $leaved_at)
    {
        // parametrdan arrived_at ni olib date object yasaymiz
        $this->arrived_at = new DateTime($arrived_at);
        $this->leaved_at = new DateTime($leaved_at);
        $this->Workrequied();

        $query = "INSERT INTO work_time (name,arrived_at,leaved_at, required_of) 
                        VALUES (:name, :arrived_at, :leaved_at, :required_of)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindValue(':arrived_at', $this->arrived_at->format('Y-m-d H:i'));
        $stmt->bindValue(':leaved_at', $this->leaved_at->format('Y-m-d H:i'));
        $stmt->bindParam(':required_of', $this->total);
        $stmt->execute();
        header('Location: index.php');
        return;
    }
    public function Workrequied()
    {
        $diff = $this->arrived_at->diff($this->leaved_at);
        $hour = $diff->h;
        $minute = $diff->i;
        $second = $diff->s;

        $this->total = ((self::REQUIRED_HOUR_DURATION * 3600) - (($hour * 3600) + ($minute * 60)));
    }
    public function getWorDayList()
    {
        $query = "SELECT * FROM work_time";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll();
    }
}
