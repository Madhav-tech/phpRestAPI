<?php

require_once("db.php");
require_once("../model/Response.php");
require_once("../model/Task.php");
header('Content-type:application/json;charset=utf-8');

$dbconfig = new Db();
$connection = $dbconfig->getConnection();

if (!$connection) {
    $response = new Response();
    $response->set_httpStatusCode(500);
    $response->set_success(false);
    $response->set_messages("Database Connection failed");
    $response->send();
    exit;
}

if (isset($_GET["taskid"])) {
    $taskid = $_GET["taskid"];
    if (empty($taskid) || !is_numeric($taskid)) {
        $response = new Response();
        $response->set_httpStatusCode(400);
        $response->set_success(false);
        $response->set_messages("Task Id required and must be numeric");
        $response->send();
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $query = "Select * from tbltasks WHERE id = {$taskid}";
        $queryResult = mysqli_query($connection, $query);

        if (!$queryResult) {
            $response = new Response();
            $response->set_httpStatusCode(500);
            $response->set_success(false);
            $response->set_messages("Database Connection failed");
            $response->send();
            exit;
        }

        if (mysqli_num_rows($queryResult) === 0) {
            $response = new Response();
            $response->set_httpStatusCode(404);
            $response->set_success(false);
            $response->set_messages("Task Not Found");
            $response->send();
            exit;
        }
        $row = mysqli_fetch_assoc($queryResult);
        $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);

        $returnData = [];
        $returnData['rowCount'] = mysqli_num_rows($queryResult);
        $returnData['data'] = $task->getTaskAsArray();

        $response = new Response();
        $response->set_httpStatusCode(200);
        $response->set_success(true);
        $response->set_toCache(true);
        $response->set_data($returnData);
        $response->send();
    } elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {

        $query = "DELETE from tbltasks WHERE id = {$taskid}";
        $queryResult = mysqli_query($connection, $query);

        if (!$queryResult) {
            $response = new Response();
            $response->set_httpStatusCode(500);
            $response->set_success(false);
            $response->set_messages("Database Connection failed");
            $response->send();
            exit;
        }

        if (mysqli_affected_rows($connection)===0) {
            $response = new Response();
            $response->set_httpStatusCode(404);
            $response->set_success(false);
            $response->set_messages("Task Not Found");
            $response->send();
            exit;
        }


        $response = new Response();
        $response->set_httpStatusCode(200);
        $response->set_success(true);
        $response->set_toCache(true);
        $response->set_data(["Task Deleted"]);
        $response->send();
    } elseif ($_SERVER['REQUEST_METHOD'] === "PATCH") {
        $response = new Response();
        $response->set_httpStatusCode(200);
        $response->set_success(true);
        $response->set_messages("Patch");
        $response->send();
    } else {
        $response = new Response();
        $response->set_httpStatusCode(200);
        $response->set_success(true);
        $response->set_messages("Request Method not allowed");
        $response->send();
    }
}
