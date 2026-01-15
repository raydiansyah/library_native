<?php
$pageTitle = 'Manajemen Buku';
require_once __DIR__ . '/../../includes/header.php';

$db = new Database();
$conn = $db->connect();

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = $_POST['title'];
                $author = $_POST['author'];
                $isbn = $_POST['isbn'];
                $stock = $_POST['stock'];

                $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, stock) VALUES (:title, :author, :isbn, :stock)");
                if ($stmt->execute(['title' => $title, 'author' => $author, 'isbn' => $isbn, 'stock' => $stock])) {
                    $success = 'Buku berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan buku!';
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $title = $_POST['title'];
                $author = $_POST['author'];
                $isbn = $_POST['isbn'];
                $stock = $_POST['stock'];

                $stmt = $conn->prepare("UPDATE books SET title = :title, author = :author, isbn = :isbn, stock = :stock WHERE id = :id");
                if ($stmt->execute(['id' => $id, 'title' => $title, 'author' => $author, 'isbn' => $isbn, 'stock' => $stock])) {
                    $success = 'Buku berhasil diupdate!';
                } else {
                    $error = 'Gagal mengupdate buku!';
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM books WHERE id = :id");
                if ($stmt->execute(['id' => $id])) {
                    $success = 'Buku berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus buku!';
                }
                break;
        }
    }
}

// Get all books
$books = $conn->query("SELECT * FROM books ORDER BY created_at DESC")->fetchAll();
?>

<div class="space-y-6">
    <?php if ($success): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
            <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <!-- Add Book Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-xl font-semibold text-slate-800">Daftar Buku</h3>
        <button onclick="openAddModal()" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
            ➕ Tambah Buku
        </button>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Judul</th>
                        <th class="hidden sm:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Penulis</th>
                        <th class="hidden md:table-cell px-3 md:px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">ISBN</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Stok</th>
                        <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data buku
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 md:px-6 py-4">
                                    <div class="text-sm font-medium text-slate-800"><?php echo htmlspecialchars($book['title']); ?></div>
                                    <div class="sm:hidden text-xs text-slate-600 mt-1"><?php echo htmlspecialchars($book['author']); ?></div>
                                </td>
                                <td class="hidden sm:table-cell px-3 md:px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($book['author']); ?></td>
                                <td class="hidden md:table-cell px-3 md:px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td class="px-3 md:px-6 py-4">
                                    <span class="px-2 md:px-3 py-1 text-xs font-semibold rounded-full <?php echo $book['stock'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $book['stock']; ?>
                                    </span>
                                </td>
                                <td class="px-3 md:px-6 py-4 text-sm">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <button onclick='openEditModal(<?php echo json_encode($book); ?>)' class="text-blue-600 hover:text-blue-800 font-medium text-xs md:text-sm">✏️ Edit</button>
                                        <button onclick="deleteBook(<?php echo $book['id']; ?>)" class="text-red-600 hover:text-red-800 font-medium text-xs md:text-sm">🗑️ Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 w-full max-w-md transform transition-all">
        <h3 class="text-xl md:text-2xl font-bold text-slate-800 mb-6">Tambah Buku Baru</h3>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="add">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Judul Buku</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Penulis</label>
                <input type="text" name="author" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ISBN</label>
                <input type="text" name="isbn" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Stok</label>
                <input type="number" name="stock" required min="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">
                    Simpan
                </button>
                <button type="button" onclick="closeAddModal()" class="flex-1 bg-slate-200 text-slate-700 py-2 rounded-lg hover:bg-slate-300 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl p-6 md:p-8 w-full max-w-md transform transition-all">
        <h3 class="text-xl md:text-2xl font-bold text-slate-800 mb-6">Edit Buku</h3>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Judul Buku</label>
                <input type="text" name="title" id="edit_title" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Penulis</label>
                <input type="text" name="author" id="edit_author" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ISBN</label>
                <input type="text" name="isbn" id="edit_isbn" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Stok</label>
                <input type="number" name="stock" id="edit_stock" required min="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">
                    Update
                </button>
                <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-200 text-slate-700 py-2 rounded-lg hover:bg-slate-300 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(book) {
        document.getElementById('edit_id').value = book.id;
        document.getElementById('edit_title').value = book.title;
        document.getElementById('edit_author').value = book.author;
        document.getElementById('edit_isbn').value = book.isbn;
        document.getElementById('edit_stock').value = book.stock;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function deleteBook(id) {
        if (confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>