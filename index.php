<?php
session_start();

$days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

if (!isset($_SESSION['tasks'])) {
  $_SESSION['tasks'] = [];
  foreach ($days as $day) {
    $_SESSION['tasks'][$day] = [];
  }
} else {
  // pastikan semua hari ada
  foreach ($days as $day) {
    if (!isset($_SESSION['tasks'][$day])) {
      $_SESSION['tasks'][$day] = [];
    }
  }
}

function tambahTugas($day, $task) {
  $task = trim($task);
  if ($task !== '' && isset($_SESSION['tasks'][$day])) {
    $_SESSION['tasks'][$day][] = ['task' => $task, 'is_done' => false];
  }
}

function updateStatusTugas($day, $id, $status) {
  if (isset($_SESSION['tasks'][$day][$id])) {
    $_SESSION['tasks'][$day][$id]['is_done'] = (bool)$status;
  }
}

function hapusTugas($day, $id) {
  if (isset($_SESSION['tasks'][$day][$id])) {
    array_splice($_SESSION['tasks'][$day], $id, 1);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $day = $_POST['day'] ?? '';
  $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
  $task = $_POST['task'] ?? '';

  switch ($action) {
    case 'tambah':
      tambahTugas($day, $task);
      break;
    case 'update_status':
      $status = isset($_POST['is_done']) && $_POST['is_done'] === '1' ? true : false;
      updateStatusTugas($day, $id, $status);
      break;
    case 'hapus':
      hapusTugas($day, $id);
      break;
  }
  header("Location: ".$_SERVER['PHP_SELF']);
  exit();
}

function tampilkanDaftarHari($day, $tasks) {
  if (empty($tasks)) {
    echo '<div class="text-muted fst-italic">Belum ada tugas</div>';
    return;
  }
  echo '<ul class="list-group">';
  foreach ($tasks as $id => $task) {
    $checked = $task['is_done'] ? 'checked' : '';
    $classDone = $task['is_done'] ? 'text-decoration-line-through text-muted' : '';
    echo '<li class="list-group-item d-flex justify-content-between align-items-center p-2">';
    
    echo '<form method="post" class="d-flex align-items-center gap-2 mb-0 flex-grow-1">';
    echo '<input type="hidden" name="action" value="update_status">';
    echo '<input type="hidden" name="day" value="'.htmlspecialchars($day).'">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<div class="form-check mb-0 w-100">';
    echo '<input class="form-check-input" type="checkbox" id="task_'.$day.'_'.$id.'" name="is_done" value="1" onchange="this.form.submit()" '.$checked.'>';
    echo '<label class="form-check-label '.$classDone.'" for="task_'.$day.'_'.$id.'">';
    echo htmlspecialchars($task['task']);
    echo '</label>';
    echo '</div>';
    echo '</form>';

    echo '<form method="post" onsubmit="return confirm(\'Hapus tugas ini?\');" class="mb-0 ms-2">';
    echo '<input type="hidden" name="action" value="hapus">';
    echo '<input type="hidden" name="day" value="'.htmlspecialchars($day).'">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>';
    echo '</form>';

    echo '</li>';
  }
  echo '</ul>';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>To-Do List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background: #f0f2f5;
      padding: 20px;
    }
    .day-column {
      background: white;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 0 8px rgb(0 0 0 / 0.1);
      display: flex;
      flex-direction: column;
      height: 100%;
      max-height: 70vh;
      overflow-y: auto;
      margin-bottom: 20px;
    }
    .day-title {
      font-weight: 700;
      margin-bottom: 10px;
      text-align: center;
      font-size: 1.3rem;
      color: #2c3e50;
      border-bottom: 2px solid #28a745;
      padding-bottom: 5px;
    }
    form.add-task-form {
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h1 class="text-center mb-4">To-Do List</h1>
  <div class="row g-3">
  <?php 
  // Baris pertama: Senin-Kamis (4 kolom)
  for ($i = 0; $i < 4; $i++): 
    $day = $days[$i];
  ?>
    <div class="col-md-3">
      <div class="day-column">
        <div class="day-title"><?= htmlspecialchars($day) ?></div>
        <form method="post" class="add-task-form d-flex" autocomplete="off">
          <input type="hidden" name="action" value="tambah" />
          <input type="hidden" name="day" value="<?= htmlspecialchars($day) ?>" />
          <input type="text" name="task" class="form-control form-control-sm me-2" placeholder="Tambah tugas..." required />
          <button type="submit" class="btn btn-sm btn-primary">+</button>
        </form>
        <?php tampilkanDaftarHari($day, $_SESSION['tasks'][$day]); ?>
      </div>
    </div>
  <?php endfor; ?>
</div>

<!-- Baris kedua tengah-tengah -->
<div class="d-flex justify-content-center gap-3 mt-3">
  <?php 
  for ($i = 4; $i < 7; $i++): 
    $day = $days[$i];
  ?>
    <div class="col-md-3" style="flex: 0 0 25%; max-width: 25%;">
      <div class="day-column">
        <div class="day-title"><?= htmlspecialchars($day) ?></div>
        <form method="post" class="add-task-form d-flex" autocomplete="off">
          <input type="hidden" name="action" value="tambah" />
          <input type="hidden" name="day" value="<?= htmlspecialchars($day) ?>" />
          <input type="text" name="task" class="form-control form-control-sm me-2" placeholder="Tambah tugas..." required />
          <button type="submit" class="btn btn-sm btn-primary">+</button>
        </form>
        <?php tampilkanDaftarHari($day, $_SESSION['tasks'][$day]); ?>
      </div>
    </div>
  <?php endfor; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>