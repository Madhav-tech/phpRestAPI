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
        try {


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

            if (mysqli_affected_rows($connection) === 0) {
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
        } catch (Throwable $th) {
            $response = new Response();
            $response->set_httpStatusCode(500);
            $response->set_success(true);
            $response->set_messages("Delete failed");
            $response->send();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === "PATCH") {
        $response = new Response();
        $response->set_httpStatusCode(200);
        $response->set_success(true);
        $response->set_messages("Patch");
        $response->send();
    } else {
        $response = new Response();
        $response->set_httpStatusCode(405);
        $response->set_success(false);
        $response->set_messages("Request Method not allowed");
        $response->send();
    }
} elseif (isset($_GET["completed"])) {
    $completed = $_GET["completed"];

    if ($completed !== "Y" && $completed !== "N") {
        $response = new Response();
        $response->set_httpStatusCode(400);
        $response->set_success(false);
        $response->set_messages("Request Method not allowed");
        $response->send();
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $query = "Select * from tbltasks WHERE completed = '{$completed}'";
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

            $allTasks = [];
            while ($row = mysqli_fetch_assoc($queryResult)) {
                $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);
                array_push($allTasks, $task->getTaskAsArray());
            }

            $returnData = [];
            $returnData['rowCount'] = mysqli_num_rows($queryResult);
            $returnData['data'] = $allTasks;

            $response = new Response();
            $response->set_httpStatusCode(200);
            $response->set_success(true);
            $response->set_toCache(true);
            $response->set_data($returnData);
            $response->send();
        } else {
            $response = new Response();
            $response->set_httpStatusCode(405);
            $response->set_success(false);
            $response->set_messages("Only GET method allowed");
            $response->send();
            exit;
        }
    }
} elseif (empty($_GET)) {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $query = "Select * from tbltasks";
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

        $allTasks = [];
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['completed']);
            array_push($allTasks, $task->getTaskAsArray());
        }

        $returnData = [];
        $returnData['rowCount'] = mysqli_num_rows($queryResult);
        $returnData['data'] = $allTasks;

        $response = new Response();
        $response->set_httpStatusCode(200);
        $response->set_success(true);
        $response->set_toCache(true);
        $response->set_data($returnData);
        $response->send();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $rowPostData = file_get_contents('php://input');
        if (!$jsonData = json_decode($rowPostData)) {
            $response = new Response();
            $response->set_httpStatusCode(400);
            $response->set_success(false);
            $response->set_messages("Request body is not valid");
            $response->send();
            exit;
        }
        if (!isset($jsonData->title) || !isset($jsonData->completed)) {
            $response = new Response();
            $response->set_httpStatusCode(400);
            $response->set_success(false);
            (!isset($jsonData->title) ? $response->set_messages("Task title required") : false);
            (!isset($jsonData->completed) ? $response->set_messages("completed field required") : false);
            $response->send();
            exit;
        }


        $title = $jsonData->title;
        $description =  (isset($jsonData->description) ? $jsonData->description : null);
        $date = (isset($jsonData->deadline) ? $jsonData->deadline : null);
        $completed = $jsonData->completed;

        $deadline = date($date);


        $query = "INSERT INTO tbltasks(title,description,deadline,completed) value('$title','$description','$deadline','$completed')";
        $queryResult = mysqli_query($connection, $query);

        if (!$queryResult) {
            $response = new Response();
            $response->set_httpStatusCode(500);
            $response->set_success(false);
            $response->set_messages(mysqli_error($connection));
            $response->send();
            exit;
        }
        if (mysqli_affected_rows($connection) === 0) {
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
        $response->set_data("Task Created" . $deadline);
        $response->send();
    } else {
        $response = new Response();
        $response->set_httpStatusCode(405);
        $response->set_success(false);
        $response->set_messages("Only GET method allowed");
        $response->send();
        exit;
    }
}
