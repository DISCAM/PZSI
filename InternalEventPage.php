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
        $this->model->id = $_POST['Id'];
        $this->model->title = $_POST['Title'];
        $this->model->notes = $_POST['Notes'];
        $this->model->isActive = $_POST['IsActive'] == 'on';
        $this->model->link = $_POST['Link'];
        $this->model->isPublic = $_POST['IsPublic'] == 'on';
        $this->model->isCancelled = $_POST['IsCancelled'] == 'on';
        $this->model->publishDateTime = $_POST['PublishDateTime'];
        $this->model->eventDateTime = $_POST['EventDateTime'];
        $this->model->shortDescription = $_POST['ShortDescription'];
        $this->model->contentHTML = $_POST['ContentHTML'];
        $this->model->metaDescription = $_POST['MetaDescription'];
        $this->model->metaTags = $_POST['MetaTags'];
    }


    protected function generateViewCreate() : string{
        return ' <div class="container">
        <div class="row gy-3">
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title h5">Cosmo Libra meeting</p>
                        <p><strong>Customer interview.</strong></p>
                        Meet customer at "Sushi bar".
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title h5">Company party</p>
                        <p><strong>Orginize party for workers</strong></p>
                        To be completed...
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary">Edit</button>
                        <button class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    }








}