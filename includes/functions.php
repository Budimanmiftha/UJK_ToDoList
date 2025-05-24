<?php
session_start();

if (!isset($_SESSION['tasks'])) {
  $_SESSION['tasks'] = [];
}

function tambahTugas($task) {
  $task = trim($task);
  if ($task !== '') {
    $_SESSION['tasks'][] = ['task' => $task, 'is_done' => false];
  }
}

function updateStatusTugas($id, $status) {
  if (isset($_SESSION['tasks'][$id])) {
    $_SESSION['tasks'][$id]['is_done'] = (bool)$status;
  }
}

function hapusTugas($id) {
  if (isset($_SESSION['tasks'][$id])) {
    array_splice($_SESSION['tasks'], $id, 1);
  }
}

function tampilkanDaftar() {
  if (empty($_SESSION['tasks'])) {
    echo '<div class="alert alert-info">Tidak ada tugas.</div>';
    return;
  }
  echo '<ul class="list-group">';
  foreach ($_SESSION['tasks'] as $id => $task) {
    $checked = $task['is_done'] ? 'checked' : '';
    $classDone = $task['is_done'] ? 'text-decoration-line-through text-muted' : '';
    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
    
    // Form checkbox update status
    echo '<form method="post" class="d-flex align-items-center gap-2 mb-0">';
    echo '<input type="hidden" name="action" value="update_status">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<div class="form-check mb-0">';
    echo '<input class="form-check-input" type="checkbox" id="task' . $id . '" name="is_done" value="1" onchange="this.form.submit()" ' . $checked . '>';
    echo '<label class="form-check-label ' . $classDone . '" for="task' . $id . '">';
    echo htmlspecialchars($task['task']);
    echo '</label>';
    echo '</div>';
    echo '</form>';

    // Form hapus tugas
    echo '<form method="post" onsubmit="return confirm(\'Hapus tugas ini?\');" class="mb-0">';
    echo '<input type="hidden" name="action" value="hapus">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>';
    echo '</form>';

    echo '</li>';
  }
  echo '</ul>';
}