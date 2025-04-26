<?php


include_once "Model.php";

class Task extends Model
{
    public int $id;
    public string $title;
    public bool $isDone;
    public string $startDateTime;
    public string $description;
    public string $deadline;
    public int $internalEventId;
    public string $creationDateTime;
    public string $editDateTime;
    public string $notes;
    public bool $isActive;
}
