<?php

include_once "Page.php";
include_once "Task.php"; // model Task

class TaskPage extends Page
{
    private Task $model;

    public function __construct()
    {
        parent::__construct("Tasks", "tasks"); // Tytuł i nazwa tabeli
    }

    protected function generateViewAll(): string
    {
        $pdo = $this->openConnection();
        $stmt = $pdo->query('
            SELECT t.*, e.Title AS EventTitle 
            FROM ' . $this->getTableName() . ' t
            INNER JOIN InternalEvents e ON t.InternalEventId = e.Id
            WHERE t.IsActive = 1
        ');

        $output = '<div class="container"><div class="row gy-3">';
        foreach ($stmt as $row) {
            $output .= '
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">' . $row['Title'] . '</h5>
                        <p><strong>Event:</strong> ' . $row['EventTitle'] . '</p>
                        <p><strong>Deadline:</strong> ' . $row['Deadline'] . '</p>
                        <p>' . $row['Description'] . '</p>
                    </div>
                    <div class="card-footer">
                        <form method="POST">
                            <input name="Id" type="hidden" value="' . $row['Id'] . '">
                            <button class="btn btn-primary" name="' . self::ACTION . '" value="' . self::EDIT_VIEW . '">Edit</button>
                            <button class="btn btn-danger" name="' . self::ACTION . '" value="' . self::DELETE . '">Delete</button>
                        </form>
                    </div>
                </div>
            </div>';
        }
        $output .= '</div></div>';
        $stmt->closeCursor();
        return $output;
    }

    protected function enterModelDataFromForm(): void
    {
        $this->model = new Task();

        $this->model->title = $_POST['Title'] ?? '';
        $this->model->isDone = isset($_POST['IsDone']);
        $this->model->startDateTime = $_POST['StartDateTime'] ?? '';
        $this->model->description = $_POST['Description'] ?? '';
        $this->model->deadline = $_POST['Deadline'] ?? '';
        $this->model->internalEventId = $_POST['InternalEventId'] ?? 0;
        $this->model->notes = $_POST['Notes'] ?? '';
        $this->model->isActive = isset($_POST['IsActive']);
    }

    protected function generateViewCreate(): string
    {
        $eventsOptions = $this->generateInternalEventsOptions();

        return '
        <form method="POST">
            <div class="container">
                <div class="row gy-3">

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Title</label>
                            <input name="Title" class="form-control" type="text">
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Deadline</label>
                            <input name="Deadline" class="form-control" type="datetime-local">
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Start Date</label>
                            <input name="StartDateTime" class="form-control" type="datetime-local">
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Internal Event</label>
                            <select class="form-select" name="InternalEventId">
                                ' . $eventsOptions . '
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label">Description</label>
                        <textarea name="Description" class="form-control"></textarea>
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label">Notes</label>
                        <textarea name="Notes" class="form-control"></textarea>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="IsDone">
                            <label class="form-check-label">Task Completed</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="IsActive" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button class="btn btn-primary" name="' . self::ACTION . '" value="' . self::AD_NEW . '">Create Task</button>
                    </div>

                </div>
            </div>
        </form>';
    }

    protected function generateViewEdit(): string
    {
        $pdo = $this->openConnection();
        $stmt = $pdo->prepare('SELECT * FROM ' . $this->getTableName() . ' WHERE Id = :id');
        $stmt->execute([':id' => $_POST['Id']]);
        $model = $stmt->fetch();



        $eventsOptions = $this->generateInternalEventsOptions($model['InternalEventId']);

        return '
        <form method="POST">
            <input type="hidden" name="Id" value="' . $model['Id'] . '">
            <div class="container">
                <div class="row gy-3">

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Title</label>
                            <input name="Title" class="form-control" type="text" value="' . $model['Title'] . '">
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Deadline</label>
                            <input name="Deadline" class="form-control" type="datetime-local" value="' . date('Y-m-d\TH:i', strtotime($model['Deadline'])) . '">
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Start Date</label>
                            <input name="StartDateTime" class="form-control" type="datetime-local" value="' . date('Y-m-d\TH:i', strtotime($model['StartDateTime'])) . '">
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="input-group">
                            <label class="input-group-text">Internal Event</label>
                            <select class="form-select" name="InternalEventId">
                                ' . $eventsOptions . '
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label">Description</label>
                        <textarea name="Description" class="form-control">' . $model['Description'] . '</textarea>
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label">Notes</label>
                        <textarea name="Notes" class="form-control">' . $model['Notes'] . '</textarea>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="IsDone" ' . ($model['IsDone'] ? 'checked' : '') . '>
                            <label class="form-check-label">Task Completed</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="IsActive" ' . ($model['IsActive'] ? 'checked' : '') . '>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button class="btn btn-warning" name="' . self::ACTION . '" value="' . self::EDIT . '">Save Changes</button>
                    </div>

                </div>
            </div>
        </form>';
    }

    protected function addNew(): void
    {
        $this->enterModelDataFromForm();


            $pdo = $this->openConnection();
            $stmt = $pdo->prepare('
                INSERT INTO ' . $this->getTableName() . ' 
                (Title, IsDone, StartDateTime, Description, Deadline, InternalEventId, CreationDateTime, EditDateTime, Notes, IsActive)
                VALUES 
                (:Title, :IsDone, :StartDateTime, :Description, :Deadline, :InternalEventId, :CreationDateTime, :EditDateTime, :Notes, :IsActive)
            ');

            $stmt->execute([
                ':Title' => $this->model->title,
                ':IsDone' => $this->model->isDone ? 1 : 0,
                ':StartDateTime' => $this->model->startDateTime,
                ':Description' => $this->model->description,
                ':Deadline' => $this->model->deadline,
                ':InternalEventId' => $this->model->internalEventId,
                ':CreationDateTime' => date('Y-m-d H:i:s'),
                ':EditDateTime' => date('Y-m-d H:i:s'),
                ':Notes' => $this->model->notes,
                ':IsActive' => $this->model->isActive ? 1 : 0,
            ]);

            header('Location: index.php?page=tasks');
            exit();


    }

    protected function edit(): void
    {
        $this->enterModelDataFromForm();

        try {
            $pdo = $this->openConnection();
            $stmt = $pdo->prepare('
                UPDATE ' . $this->getTableName() . '
                SET 
                    Title = :Title,
                    IsDone = :IsDone,
                    StartDateTime = :StartDateTime,
                    Description = :Description,
                    Deadline = :Deadline,
                    InternalEventId = :InternalEventId,
                    EditDateTime = :EditDateTime,
                    Notes = :Notes,
                    IsActive = :IsActive
                WHERE Id = :Id
            ');

            $stmt->execute([
                ':Title' => $this->model->title,
                ':IsDone' => $this->model->isDone ? 1 : 0,
                ':StartDateTime' => $this->model->startDateTime,
                ':Description' => $this->model->description,
                ':Deadline' => $this->model->deadline,
                ':InternalEventId' => $this->model->internalEventId,
                ':EditDateTime' => date('Y-m-d H:i:s'),
                ':Notes' => $this->model->notes,
                ':IsActive' => $this->model->isActive ? 1 : 0,
                ':Id' => $_POST['Id'],
            ]);

            header('Location: index.php?page=tasks');
            exit();

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }

    protected function delete(): void
    {
        $pdo = $this->openConnection();
        $stmt = $pdo->prepare('DELETE FROM ' . $this->getTableName() . ' WHERE Id = :Id');
        $stmt->execute([':Id' => $_POST['Id']]);
        header('Location: index.php?page=tasks');
        exit();
    }

    private function generateInternalEventsOptions($selectedId = null): string
    {
        $pdo = $this->openConnection();
        $stmt = $pdo->query('SELECT Id, Title FROM InternalEvents WHERE IsActive = 1 ORDER BY Title');
        $options = '';

        foreach ($stmt as $row) {
            $selected = ($row['Id'] == $selectedId) ? 'selected' : '';
            $options .= '<option value="' . htmlspecialchars($row['Id']) . '" ' . $selected . '>' . htmlspecialchars($row['Title']) . '</option>';
        }

        return $options;
    }


}
