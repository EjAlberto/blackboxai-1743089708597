<div class="w-64 bg-blue-800 text-white flex flex-col">
    <div class="p-4 border-b border-blue-700">
        <h2 class="text-xl font-semibold">Navigation</h2>
    </div>
    
    <nav class="flex-1 overflow-y-auto">
        <div class="p-4">
            <h3 class="text-sm uppercase text-blue-300 mb-2">Dashboard</h3>
            <ul>
                <li class="mb-1">
                    <a href="index.php" class="block px-3 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-tachometer-alt mr-2"></i> Overview
                    </a>
                </li>
            </ul>
        </div>

        <div class="p-4 border-t border-blue-700">
            <h3 class="text-sm uppercase text-blue-300 mb-2">Students</h3>
            <ul>
                <li class="mb-1">
                    <a href="students.php?grade=11" class="block px-3 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-users mr-2"></i> Grade 11 Students
                    </a>
                </li>
                <li class="mb-1">
                    <a href="students.php?grade=12" class="block px-3 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-users mr-2"></i> Grade 12 Students
                    </a>
                </li>
                <li class="mb-1">
                    <a href="student_form.php" class="block px-3 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-user-plus mr-2"></i> Add New Student
                    </a>
                </li>
            </ul>
        </div>

        <div class="p-4 border-t border-blue-700">
            <h3 class="text-sm uppercase text-blue-300 mb-2">Management</h3>
            <ul>
                <li class="mb-1">
                    <a href="documents.php" class="block px-3 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-file-upload mr-2"></i> Document Upload
                    </a>
                </li>
                <li class="mb-1">
                    <a href="sections.php" class="block px-3 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-layer-group mr-2"></i> Manage Sections
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>