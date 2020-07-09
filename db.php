<?php
class database
{
    private $db;
    private $ini;

    public function __construct()
    {
        $this->ini = include('configs/config.php');

        $DB_DSN = "{$this->ini['db']['dbtype']}:dbname={$this->ini['db']['dbname']};
        host={$this->ini['db']['host']};
        port={$this->ini['db']['port']};
        charset=UTF8;";
        $DB_USER = $this->ini['db']['username'];
        $DB_PASSWORD = $this->ini['db']['password'];
        $this->db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
    public function dbReadMultiple($query)
    {
        $data = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        return($data);
    }
    public function dbReadSingle($query)
    {
        $data = $this->db->query($query)->fetch(PDO::FETCH_COLUMN);
        return($data);
    }
    public function getTableFields($tableName){
        $q = $this->db->prepare("DESCRIBE $tableName");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);
        for ($i = 0; $i < count($tableFields); $i++) //Exclude Fields From Config
            unset($tableFields[in_array($tableFields[$i], $this->ini['excludedColumns']) ? $i : -1]);
        return($tableFields);
    }
    public function getTableFieldsWT($tableName){
        $q = $this->db->prepare("DESCRIBE $tableName");
        $q->execute();
        $tableFields = [];
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $tableField)
            $tableFields[$tableField['Field']] = $tableField['Type'];
        foreach ($tableFields as $tableField => $c) //Exclude Fields From Config
            unset($tableFields[in_array($tableField, $this->ini['excludedColumns']) ? $tableField : -1]);
        return($tableFields);
    }
    public function getTables(){
        $q = $this->db->prepare("SHOW TABLES");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_COLUMN);
        return ($tables);
    }
}
?>