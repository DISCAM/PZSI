<?php
require ("InternalEventPage.php");
require ("TaskPage.php");

//(new InternalEventPage()) -> init();

//(new TaskPage()) -> init();

echo '
<div class="container mt-3">
    <a href="index.php?page=events" class="btn btn-primary">Wydarzenia</a>
    <a href="index.php?page=tasks" class="btn btn-secondary">Zadania</a>
</div>
<hr>
';

// Routing
$page = $_GET['page'] ?? 'events';

switch ($page) {
    case 'tasks':
        $pageObject = new TaskPage();
        break;
    case 'events':
    default:
        $pageObject = new InternalEventPage();
        break;
}

$pageObject->init();