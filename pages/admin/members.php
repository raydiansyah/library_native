<?php
$pageTitle = 'Manajemen Anggota';
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
                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];

                $stmt = $conn->prepare("INSERT INTO members (name, email, phone, address) VALUES (:name, :email, :phone, :address)");
                if ($stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address])) {
                    $success = 'Anggota berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan anggota!';
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];

                $stmt = $conn->prepare("UPDATE members SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id");
                if ($stmt->execute(['id' => $id, 'name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address])) {
                    $success = 'Anggota berhasil diupdate!';
                } else {
                    $error = 'Gagal mengupdate anggota!';
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM members WHERE id = :id");
                if ($stmt->execute(['id' => $id])) {
                    $success = 'Anggota berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus anggota!';
                }
                break;
        }
    }
}

// Get all members
$members = $conn->query("SELECT * FROM members ORDER BY created_at DESC")->fetchAll();
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

    <!-- Add Member Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-slate-800">Daftar Anggota</h3>
        <button onclick="openAddModal()" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-2 rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
            ➕ Tambah Anggota
        </button>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data anggota
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $member): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-800"><?php echo htmlspecialchars($member['name']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($member['email']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($member['phone']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($member['address']); ?></td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <button onclick='openEditModal(<?php echo json_encode($member); ?>)' class="text-blue-600 hover:text-blue-800 font-medium">✏️ Edit</button>
                                    <button onclick="deleteMember(<?php echo $member['id']; ?>)" class="text-red-600 hover:text-red-800 font-medium">🗑️ Hapus</button>
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
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md transform transition-all">
        <h3 class="text-2xl font-bold text-slate-800 mb-6">Tambah Anggota Baru</h3>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="add">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Telepon</label>
                <input type="text" name="phone" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                <textarea name="address" required rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-2 rounded-lg hover:from-green-700 hover:to-green-800 transition-all">
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
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md transform transition-all">
        <h3 class="text-2xl font-bold text-slate-800 mb-6">Edit Anggota</h3>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" name="email" id="edit_email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Telepon</label>
                <input type="text" name="phone" id="edit_phone" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                <textarea name="address" id="edit_address" required rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-2 rounded-lg hover:from-green-700 hover:to-green-800 transition-all">
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

    function openEditModal(member) {
        document.getElementById('edit_id').value = member.id;
        document.getElementById('edit_name').value = member.name;
        document.getElementById('edit_email').value = member.email;
        document.getElementById('edit_phone').value = member.phone;
        document.getElementById('edit_address').value = member.address;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function deleteMember(id) {
        if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
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