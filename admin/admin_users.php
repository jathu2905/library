<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent deleting self
    if ($id == $_SESSION['user_id']) {
        $message = "You cannot delete yourself!";
    } else {
        try {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
            $message = "User deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch Users
$users = $pdo->query("SELECT * FROM users")->fetchAll();

include '../includes/header.php';
?>

<div class="admin-container">
    <h2>Manage Users</h2>
    <p style="color: <?php echo strpos($message, 'Error') !== false || strpos($message, 'cannot') !== false ? 'red' : 'green'; ?>;"><?php echo $message; ?></p>

    <!-- User List -->
    <div class="table-container">
        <h3>Registered Users</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; background: #f4f4f4;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Username</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Role</th>
                    <th style="padding: 10px;">Joined Date</th>
                    <th style="padding: 10px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo $user['id']; ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 10px;">
                            <span style="padding: 5px 10px; border-radius: 15px; background: <?php echo $user['role'] === 'admin' ? '#e67e22' : '#eee'; ?>; color: <?php echo $user['role'] === 'admin' ? 'white' : 'black'; ?>;">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td style="padding: 10px;">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="admin_users.php?delete=<?php echo $user['id']; ?>" style="color: red;" onclick="return confirm('Are you sure? This will delete the user permanently.')">Delete</a>
                            <?php else: ?>
                                <span style="color: #999;">(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
