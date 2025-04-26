<?php

include_once "Page.php";
include_once "internalEvent.php";
class InternalEventPage extends Page
{
    private InternalEvent $model;
    public function __construct()
    {
        parent::__construct("Internal Event", "internalevents");

    }

    protected function generateViewAll(): string
    {
        $dataBase = $this -> openConnection();
        $zapytanie = $dataBase->query("SELECT * FROM " . $this->getTableName() . " WHERE isActive = 1");

        $output = '  <div class="container">
                    <div class="row gy-3">';
        foreach ($zapytanie as $row) {
            $output .= '<div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title h5"> ' . ($row['Title']) . ' </p>
                        <p><strong>' . ($row['ContentHTML']) . '</strong></p>
                        ' . ($row['ShortDescription']) . '
                    </div>
                    <div class="card-footer">
                    <form method="POST">
                    <input name="id" type="hidden" value="' . ($row['Id']) . '">
                    
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-danger" name="' . self::ACTION . '" value="' . self::DELETE . '">Delete</button>
                     </form>
                    </div>
                </div>
            </div>';
        }

        $output .= '</div> </div>';
        $zapytanie -> closeCursor();
        return $output;
    }

    protected function enterModelDataFromForm() : void
    {
        $this->model = new InternalEvent();

        $this->model->title = $_POST['Title'] ?? '';
        $this->model->link = $_POST['Link'] ?? '';
        $this->model->isPublic = ($_POST['IsPublic'] ?? '') === 'on';
        $this->model->isCancelled = ($_POST['IsCancelled'] ?? '') === 'on';
        $this->model->eventDateTime = $_POST['EventDateTime'] ?? '';
        $this->model->publishDateTime = $_POST['PublishDateTime'] ?? '';
        $this->model->shortDescription = $_POST['ShortDescription'] ?? '';
        $this->model->contentHTML = $_POST['ContentHTML'] ?? '';
        $this->model->metaDescription = $_POST['MetaDescription'] ?? '';
        $this->model->metaTags = $_POST['MetaTags'] ?? '';
        $this->model->notes = $_POST['Notes'] ?? '';

        // Pola automatyczne
        $this->model->isActive = true; // Domyślnie zakładamy, że nowy rekord jest aktywny

    }

    protected function addNew(): void
    {
        // Wypełniamy model danymi z formularza
        $this->enterModelDataFromForm();

        try {
            $pdo = self::openConnection();

            $stmt = $pdo->prepare('
            INSERT INTO ' . $this->getTableName() . ' 
            (Title, Link, IsPublic, IsCancelled, EventDateTime, PublishDateTime, ShortDescription, ContentHTML, MetaDescription, MetaTags, CreationDateTime, EditDateTime, Notes, IsActive)
            VALUES 
            (:Title, :Link, :IsPublic, :IsCancelled, :EventDateTime, :PublishDateTime, :ShortDescription, :ContentHTML, :MetaDescription, :MetaTags, :CreationDateTime, :EditDateTime, :Notes, :IsActive)
        ');

            $stmt->execute([
                ':Title' => $this->model->title,
                ':Link' => $this->model->link,
                ':IsPublic' => $this->model->isPublic ? 1 : 0,
                ':IsCancelled' => $this->model->isCancelled ? 1 : 0,
                ':EventDateTime' => $this->model->eventDateTime,
                ':PublishDateTime' => $this->model->publishDateTime,
                ':ShortDescription' => $this->model->shortDescription,
                ':ContentHTML' => $this->model->contentHTML,
                ':MetaDescription' => $this->model->metaDescription,
                ':MetaTags' => $this->model->metaTags,
                ':CreationDateTime' => date('Y-m-d H:i:s'), // teraz
                ':EditDateTime' => date('Y-m-d H:i:s'), // teraz
                ':Notes' => $this->model->notes,
                ':IsActive' => $this->model->isActive ? 1 : 0,
            ]);

            // Po dodaniu przekieruj na stronę główną albo wyświetl wszystkie modele
            header('Location: index.php');
            exit();

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
    protected function generateViewCreate() : string{
        return '   
   <form method="POST">
   <div class="container">
        <div class="row gy-3">
            <div class="col-md-12 col-lg-6 col-xxl-4">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="material-icons-round align-middle">label</i>
                        Title
                    </label>
                    <input class="form-control validate" name="Title" type="text" >
                </div>
            </div>
            <div class="col-md-12 col-lg-6 col-xxl-4">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="material-icons-round align-middle">link</i>
                        Link
                    </label>
                    <input class="form-control validate" name="Link" type="text" >
                </div>
            </div>
            <div class="col-md-12 col-lg-6 col-xxl-4">
                <div class="row">
                    <div class="col-auto">
                        <label class="form-check-label">
                            Public
                            <i class="material-icons-round align-middle" >public</i>
                        </label>
                    </div>
                    <div class="form-switch form-check col-auto">
                        <input class="form-check-input validate" name="IsPublic" type="checkbox">
                        <label class="form-check-label">
                            <i class="material-icons-round align-middle">block</i>
                            Private
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6 col-xxl-4">
                <div class="row">
                    <div class="col-auto">
                        <label class="form-check-label">
                            Cancelled
                            <i class="material-icons-round align-middle">cancel</i>
                        </label>
                    </div>
                    <div class="form-switch form-check col-auto">
                        <input class="form-check-input validate" name="IsCancelled" type="checkbox">
                        <label class="form-check-label">
                            <i class="material-icons-round align-middle">public</i>
                            Active
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6 col-xxl-4">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="material-icons-round palette-accent-text-color align-middle">event</i>
                        Event date
                    </label>
                    <input class="form-control validate" name="eventDateTime" type="date">
                </div>
            </div>
            <div class="col-md-12 col-lg-6 col-xxl-4">
                <div class="input-group">
                    <label class="input-group-text">
                        <i class="material-icons-round palette-accent-text-color align-middle">today</i>
                        Publish date
                    </label>
                    <input class="form-control validate" name="publishDateTime" type="date">
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label">
                    <i class="material-icons-round palette-accent-text-color align-middle">description</i>
                    Short description
                </label>
                <textarea class="form-control validate" name="ShortDescription"></textarea>
            </div>
            <div class="col-sm-12">
                <label class="form-label">
                    <i class="material-icons-round palette-accent-text-color align-middle">newspaper</i>
                    Content
                </label>
                <textarea class="form-control validate" name="ContentHTML"></textarea>
            </div>
            <div class="col-sm-12">
                <label class="form-label">
                    <i class="material-icons-round palette-accent-text-color align-middle">feed</i>
                    Meta description
                </label>
                <textarea class="form-control validate" name="metaDescription"></textarea>
            </div>
            <div class="col-sm-12">
                <label class="form-label">
                    <i class="material-icons-round palette-accent-text-color align-middle">subtitles</i>
                    Meta tags
                </label>
                <textarea class="form-control validate" name="MetaTags"></textarea>
            </div>
            <div class="col-sm-12">
                <label class="form-label">
                    <i class="material-icons-round palette-accent-text-color align-middle">notes</i>
                    Notes
                </label>
                <textarea class="form-control validate" name="Notes"></textarea>
            </div>
            <div class="col-sm-12">
                <button class="btn btn-primary"   name="' . self::ACTION . '" value="' . self::AD_NEW . '"> Create </button>
            </div>
        </div>
    </div>
    </form>';
    }








}