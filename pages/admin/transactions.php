<?php
$pageTitle = 'Transaksi Peminjaman';
require_once __DIR__ . '/../../includes/header.php';

$db = new Database();
$conn = $db->connect();

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'borrow':
                $member_id = $_POST['member_id'];
                $book_id = $_POST['book_id'];
                $borrow_date = $_POST['borrow_date'];

                // Check book stock
                $book = $conn->prepare("SELECT stock FROM books WHERE id = :id");
                $book->execute(['id' => $book_id]);
                $bookData = $book->fetch();

                if ($bookData && $bookData['stock'] > 0) {
                    // Create transaction
                    $stmt = $conn->prepare("INSERT INTO transactions (member_id, book_id, borrow_date) VALUES (:member_id, :book_id, :borrow_date)");
                    if ($stmt->execute(['member_id' => $member_id, 'book_id' => $book_id, 'borrow_date' => $borrow_date])) {
                        // Update book stock
                        $updateStock = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE id = :id");
                        $updateStock->execute(['id' => $book_id]);
                        $success = 'Transaksi peminjaman berhasil dicatat!';
                    } else {
                        $error = 'Gagal mencatat transaksi!';
                    }
                } else {
                    $error = 'Stok buku tidak tersedia!';
                }
                break;

            case 'return':
                $id = $_POST['id'];
                $return_date = $_POST['return_date'];

                // Get transaction details
                $trans = $conn->prepare("SELECT book_id FROM transactions WHERE id = :id");
                $trans->execute(['id' => $id]);
                $transData = $trans->fetch();

                if ($transData) {
                    // Update transaction
                    $stmt = $conn->prepare("UPDATE transactions SET return_date = :return_date WHERE id = :id");
                    if ($stmt->execute(['id' => $id, 'return_date' => $return_date])) {
                        // Update book stock
                        $updateStock = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = :id");
                        $updateStock->execute(['id' => $transData['book_id']]);
                        $success = 'Buku berhasil dikembalikan!';
                    } else {
                        $error = 'Gagal mencatat pengembalian!';
                    }
                }
                break;
        }
    }
}

// Get all transactions
$transactions = $conn->query("
    SELECT t.*, b.title as book_title, b.author, m.name as member_name, m.phone 
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    JOIN members m ON t.member_id = m.id
    ORDER BY t.borrow_date DESC
")->fetchAll();

// Get books for dropdown
$books = $conn->query("SELECT id, title, author, stock FROM books WHERE stock > 0 ORDER BY title")->fetchAll();

// Get members for dropdown
$members = $conn->query("SELECT id, name, email FROM members ORDER BY name")->fetchAll();
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

    <!-- Add Transaction Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-slate-800">Daftar Transaksi</h3>
        <button onclick="openBorrowModal()" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-2 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
            ➕ Pinjam Buku
        </button>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Tanggal Kembali</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                Belum ada transaksi
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-800"><?php echo htmlspecialchars($transaction['member_name']); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo htmlspecialchars($transaction['phone']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-800"><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($transaction['author']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo date('d/m/Y', strtotime($transaction['borrow_date'])); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?php echo $transaction['return_date'] ? date('d/m/Y', strtotime($transaction['return_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($transaction['return_date']): ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Dikembalikan</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">📖 Dipinjam</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if (!$transaction['return_date']): ?>
                                        <button onclick="openReturnModal(<?php echo $transaction['id']; ?>)" class="text-blue-600 hover:text-blue-800 font-medium">
                                            ↩️ Kembalikan
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Borrow Modal -->
<div id="borrowModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md transform transition-all">
        <h3 class="text-2xl font-bold text-slate-800 mb-6">Pinjam Buku</h3>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="borrow">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Anggota</label>
                <select name="member_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih Anggota</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?php echo $member['id']; ?>">
                            <?php echo htmlspecialchars($member['name']); ?> - <?php echo htmlspecialchars($member['email']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Buku</label>
                <select name="book_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih Buku</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?php echo $book['id']; ?>">
                            <?php echo htmlspecialchars($book['title']); ?> - <?php echo htmlspecialchars($book['author']); ?> (Stok: <?php echo $book['stock']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Pinjam</label>
                <input type="date" name="borrow_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white py-2 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all">
                    Simpan
                </button>
                <button type="button" onclick="closeBorrowModal()" class="flex-1 bg-slate-200 text-slate-700 py-2 rounded-lg hover:bg-slate-300 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md transform transition-all">
        <h3 class="text-2xl font-bold text-slate-800 mb-6">Kembalikan Buku</h3>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="return">
            <input type="hidden" name="id" id="return_id">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Kembali</label>
                <input type="date" name="return_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">
                    Kembalikan
                </button>
                <button type="button" onclick="closeReturnModal()" class="flex-1 bg-slate-200 text-slate-700 py-2 rounded-lg hover:bg-slate-300 transition-all">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBorrowModal() {
        document.getElementById('borrowModal').classList.remove('hidden');
    }

    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
    }

    function openReturnModal(id) {
        document.getElementById('return_id').value = id;
        document.getElementById('returnModal').classList.remove('hidden');
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>