<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Get statistics
$totalBooks = $conn->query("SELECT COUNT(*) as count FROM books")->fetch()['count'];
$totalMembers = $conn->query("SELECT COUNT(*) as count FROM members")->fetch()['count'];
$activeLoans = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE return_date IS NULL")->fetch()['count'];
$totalTransactions = $conn->query("SELECT COUNT(*) as count FROM transactions")->fetch()['count'];

// Get recent transactions
$recentTransactions = $conn->query("
    SELECT t.*, b.title as book_title, m.name as member_name 
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    JOIN members m ON t.member_id = m.id
    ORDER BY t.borrow_date DESC
    LIMIT 5
")->fetchAll();
?>

<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Books -->
        <div class="card-hover bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Buku</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo $totalBooks; ?></h3>
                </div>
                <div class="text-5xl opacity-80">📚</div>
            </div>
        </div>

        <!-- Total Members -->
        <div class="card-hover bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Anggota</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo $totalMembers; ?></h3>
                </div>
                <div class="text-5xl opacity-80">👥</div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="card-hover bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Sedang Dipinjam</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo $activeLoans; ?></h3>
                </div>
                <div class="text-5xl opacity-80">📖</div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="card-hover bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Transaksi</p>
                    <h3 class="text-3xl font-bold mt-2"><?php echo $totalTransactions; ?></h3>
                </div>
                <div class="text-5xl opacity-80">📝</div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
            <h3 class="text-lg font-semibold text-slate-800">Transaksi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Tanggal Kembali</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($recentTransactions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada transaksi
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $transaction): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-800"><?php echo htmlspecialchars($transaction['member_name']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-800"><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo date('d/m/Y', strtotime($transaction['borrow_date'])); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?php echo $transaction['return_date'] ? date('d/m/Y', strtotime($transaction['return_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($transaction['return_date']): ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Dikembalikan</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Dipinjam</span>
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>