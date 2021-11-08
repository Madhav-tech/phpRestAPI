<?php

class Task
{
    private $id;
    private $title;
    private $description;
    private $deadline;
    private $completed;

    public function __construct($id, $title, $description, $deadline, $completed)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setDeadline($deadline);
        $this->setCompleted($completed);
    }

    public function getId()
    {
        return $this->id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getDeadline()
    {
        return $this->deadline;
    }

    public function getCompleted()
    {
        return $this->completed;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }
    public function setCompleted($completed)
    {
        $this->completed = $completed;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getTaskAsArray()
    {
        $task = [];

        $task["id"] = $this->getId();
        $task["title"] = $this->getTitle();
        $task["description"] = $this->getDescription();
        $task["deadline"] = $this->getDeadline();
        $task["completed"] = $this->getCompleted();

        return $task;
    }
}

// $task = new Task(1, "Task 1", "Description 1", "10/12/2021", "Y");
// echo json_encode($task->getTaskAsArray());
