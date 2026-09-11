<?php

$controllers = [
    'app/Http/Controllers/Cs/TaskController.php'          => ['Cs', 'cs'],
    'app/Http/Controllers/Ob/TaskController.php'          => ['Ob', 'ob'],
    'app/Http/Controllers/Programmer/TaskController.php'  => ['Programmer', 'programmer'],
    'app/Http/Controllers/DG/TaskController.php'          => ['DG', 'dg'],
    'app/Http/Controllers/VG/TaskController.php'          => ['VG', 'vg'],
    'app/Http/Controllers/PM/TaskController.php'          => ['PM', 'pm'],
    'app/Http/Controllers/Member/TaskController.php'      => ['Member', 'member'],
    'app/Http/Controllers/Sosmed/TaskController.php'      => ['Sosmed', 'sosmed'],
    'app/Http/Controllers/Assistant/TaskController.php'   => ['Assistant', 'assistant'],
];

foreach ($controllers as $file => [$ns, $prefix]) {
    $content = file_get_contents($file);

    // 1. Add trait import after Controller import (only if not already added)
    if (strpos($content, 'LogsActivity') === false) {
        $content = str_replace(
            'use App\\Http\\Controllers\\Controller;',
            "use App\\Http\\Controllers\\Controller;\nuse App\\Http\\Traits\\LogsActivity;",
            $content
        );

        // 2. Add 'use LogsActivity;' inside class body
        $content = preg_replace(
            '/(class TaskController extends Controller\s*\{)/s',
            "$1\n    use LogsActivity;\n",
            $content,
            1
        );
    }

    // 3. Patch createSelfTask — add log after service call
    $content = preg_replace(
        '/\$this->taskService->createSelfTask\(\$validated\);(\s*return redirect\(\)->route\(\'' . $prefix . '\.tasks\.index\'\))/s',
        "\$this->taskService->createSelfTask(\$validated);\n            \$this->logActivity('task.created', 'Tugas', \"Membuat tugas mandiri '{\$validated['title']}'\");\$1",
        $content
    );

    // 4. Patch updateSelfTask
    $content = preg_replace(
        '/\$this->taskService->updateSelfTask\(\$task, \$validated\);(\s*return redirect\(\)->route\(\'' . $prefix . '\.tasks\.index\'\))/s',
        "\$this->taskService->updateSelfTask(\$task, \$validated);\n            \$this->logActivity('task.updated', 'Tugas', \"Memperbarui tugas '{\$task->title}'\", \$task);\$1",
        $content
    );

    // 5. Patch deleteSelfTask — capture title before deletion
    $content = preg_replace(
        '/\$this->taskService->deleteSelfTask\(\$task\);(\s*return redirect\(\)->route\(\'' . $prefix . '\.tasks\.index\'\))/s',
        "\$title = \$task->title;\n            \$this->taskService->deleteSelfTask(\$task);\n            \$this->logActivity('task.deleted', 'Tugas', \"Menghapus tugas '{\$title}'\");\$1",
        $content
    );

    // 6. Patch completeTask (all instances in this file)
    $content = preg_replace(
        '/\$this->taskService->completeTask\(\$task, \$request->note\);(\s*return redirect\(\)->route\(\'' . $prefix . '\.)/s',
        "\$this->taskService->completeTask(\$task, \$request->note);\n            \$this->logActivity('task.completed', 'Tugas', \"Menyelesaikan tugas '{\$task->title}'\", \$task);\$1",
        $content
    );

    file_put_contents($file, $content);
    echo "Patched: $file\n";
}

echo "Done!\n";
