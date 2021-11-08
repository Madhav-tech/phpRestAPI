<?php
class Db
{
    //const HOST = "database-1.c88xiivsd0im.ap-south-1.rds.amazonaws.com";
    const HOST = "localhost";
    const USER = "root";
    const PWD = ""; //const HOST =
    const DB = "taskdb";
    private $connection;
    function __construct()
    {
        $this->connection = mysqli_connect(Db::HOST, Db::USER, Db::PWD, Db::DB);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
