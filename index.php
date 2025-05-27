<?php
require 'includes/auth.php';
require 'db/connection.php';
require 'includes/functions.php';

// Handle INSERT/UPDATE/DELETE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];

    if (isset($_POST['create_project'])) {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, created_by, updated_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['description'], $userId, $userId]);
    }

    if (isset($_POST['create_task'])) {
        $stmt = $pdo->prepare("INSERT INTO tasks (project_id, title, status, created_by, updated_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['project_id'], $_POST['task_title'], $_POST['status'], $userId, $userId]);
    }

    if (isset($_POST['update_task'])) {
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, status = ?, updated_by = ? WHERE id = ?");
        $stmt->execute([$_POST['task_title'], $_POST['status'], $userId, $_POST['task_id']]);
    }

    if (isset($_POST['delete_task'])) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$_POST['task_id']]);
    }

    if (isset($_POST['update_project'])) {
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, updated_by = ? WHERE id = ?");
        $stmt->execute([$_POST['title'], $_POST['description'], $userId, $_POST['project_id']]);
    }

    if (isset($_POST['delete_project'])) {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$_POST['project_id']]);
    }

    header("Location: index.php");
    exit();
}
?>

<h2>Welcome, <?= $_SESSION['username'] ?>! <a href="logout.php">Logout</a></h2>

<h3>Create New Project</h3>
<form method="post">
    <input name="title" placeholder="Project title" required>
    <textarea name="description" placeholder="Description"></textarea>
    <button name="create_project">Create Project</button>
</form>

<hr>

<?php
$projects = $pdo->query("SELECT * FROM projects")->fetchAll();
foreach ($projects as $project) {
    echo "<div style='border:1px solid gray;padding:10px;margin:10px 0;'>";
    echo "<h3>{$project['title']}</h3>";
    echo "<p>{$project['description']}</p>";
    echo "<p>Created by: " . getUsername($pdo, $project['created_by']) . ", Updated by: " . getUsername($pdo, $project['updated_by']) . "</p>";

    echo "<form method='post'>
            <input type='hidden' name='project_id' value='{$project['id']}'>
            <input name='title' value='{$project['title']}' required>
            <textarea name='description'>{$project['description']}</textarea>
            <button name='update_project'>Update</button>
            <button name='delete_project'>Delete</button>
          </form>";

    echo "<h4>Add Task</h4>
          <form method='post'>
              <input type='hidden' name='project_id' value='{$project['id']}'>
              <input name='task_title' placeholder='Task Title' required>
              <select name='status'>
                  <option>Pending</option>
                  <option>In Progress</option>
                  <option>Completed</option>
              </select>
              <button name='create_task'>Add Task</button>
          </form>";

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ?");
    $stmt->execute([$project['id']]);
    $tasks = $stmt->fetchAll();

    echo "<ul>";
    foreach ($tasks as $task) {
        echo "<li>
                <form method='post'>
                    <input type='hidden' name='task_id' value='{$task['id']}'>
                    <input name='task_title' value='{$task['title']}' required>
                    <select name='status'>
                        <option " . ($task['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                        <option " . ($task['status'] == 'In Progress' ? 'selected' : '') . ">In Progress</option>
                        <option " . ($task['status'] == 'Completed' ? 'selected' : '') . ">Completed</option>
                    </select>
                    <button name='update_task'>Update</button>
                    <button name='delete_task'>Delete</button>
                </form>
                <small>Created by: " . getUsername($pdo, $task['created_by']) . ", Updated by: " . getUsername($pdo, $task['updated_by']) . "</small>
              </li>";
    }
    echo "</ul>";
    echo "</div>";
}
?>
