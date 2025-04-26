<?php

abstract class Page
{
    public const ACTION = "Action";
    public const AD_NEW = "Ad_new";
    public const EDIT = "Edit";
    public const CREATE_VIEW = "Create_view";
    public const EDIT_VIEW = "Edit_view";
    public const DELETE = "Delete";
    private string $title;
    private string $tableName;

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getTableName(): string
    {
        return $this->tableName;
    }
    public function __construct(string $title, string $tableName)
    {
        $this->title = $title;
        $this->tableName = $tableName;
    }

    /// metody abstrakcyjne 

    protected abstract function enterModelDataFromForm() : void;
    protected abstract function generateViewAll() : string;
    protected abstract function generateViewCreate() : string;
    protected abstract function generateViewEdit() : string;
    protected abstract function addNew() : void;
    protected abstract function edit() : void;

    protected function delete() : void {
        $id = $_POST['id'] ?? null;
        $database = $this->openConnection();
        $zapytanie = $database->prepare("UPDATE " . $this->getTableName() . " SET isActive = 0 WHERE id = :id");
        $zapytanie->bindValue(":id", $id, PDO::PARAM_INT);
        $zapytanie->execute();

    }



    protected function generateHead(): string{
        return '<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Internal Events and Task Create
    </title>
    
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link
        href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp"
        rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Internal Events and Task - Create</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <form method="POST">
                    <button class="btn btn-primary" name = "' . self::ACTION . '" value="' . self::CREATE_VIEW . '"> Create new </button>
                    <button class="btn btn-primary">All</button>
                </form>
            </div>
        </div>
    </div>
    <hr>';
    }

    protected function genereateFooter() : string{
        return '<script src="js/bootstrap.min.js"></script>
</body>';
    }

    protected function openConnection() : PDO
    {
        return new PDO("mysql:host=localhost;dbname=phpadvanced", "root");

    }

    public function init() : void
    {
        echo $this->generateHead();
        $action = $_POST[self::ACTION] ?? null;
        switch ($action) {
            case self::AD_NEW:
                echo $this->addNew();
                break;

            case self::EDIT:
                echo $this->edit();
                break;

            case self::CREATE_VIEW:
                echo $this->generateViewCreate();
                break;

            case self::EDIT_VIEW:
                echo $this-> generateViewEdit();
                break;

            case self::DELETE:
                $this->delete();
                header("Location: index.php");
                break;

            default:
                echo $this->generateViewAll();
                break;
        }
        echo $this->genereateFooter();
    }



}

