<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireLogin_root();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM posts WHERE id = $id");
    redirect('/cms-educational-system/admin/posts/list.php');
}

// Handle pin toggle
if (isset($_GET['pin'])) {
    $id = (int)$_GET['pin'];
    mysqli_query($conn, "UPDATE posts SET is_pinned = !is_pinned WHERE id = $id");
    redirect('/cms-educational-system/admin/posts/list.php');
}

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE posts.title LIKE '%$search%'" : '';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts $where"))['count'];
$total_pages = ceil($total / $limit);

$posts = mysqli_query($conn, "
    SELECT posts.*, categories.name as category_name 
    FROM posts 
    LEFT JOIN categories ON posts.category_id = categories.id 
    $where
    ORDER BY posts.is_pinned DESC, posts.created_at DESC
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts — EduCMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }
        .sidebar {
            width: 240px; background: #1e293b; color: #fff;
            padding: 24px 0; position: fixed; height: 100vh;
            display: flex; flex-direction: column;
        }
        .sidebar-brand { font-size: 18px; font-weight: 700; color: #fff; padding: 0 24px 24px; border-bottom: 1px solid #334155; }
        .sidebar-brand span { color: #3b82f6; }
        .sidebar-menu { margin-top: 16px; flex: 1; }
        .sidebar-menu a { display: flex; align-items: center; gap: 10px; padding: 11px 24px; color: #94a3b8; text-decoration: none; font-size: 14px; transition: all .2s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #334155; color: #fff; }
        .sidebar-footer { padding: 16px 24px; border-top: 1px solid #334155; font-size: 13px; color: #64748b; }
        .sidebar-footer a { color: #ef4444; text-decoration: none; font-size: 13px; }
        .main { margin-left: 240px; padding: 32px; flex: 1; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-title { font-size: 22px; font-weight: 600; color: #1e293b; }
        .btn-primary { background: #3b82f6; color: #fff; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; }
        .btn-primary:hover { background: #2563eb; }
        .search-bar { margin-bottom: 20px; }
        .search-bar input { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 300px; outline: none; }
        .search-bar button { padding: 10px 16px; background: #3b82f6; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; margin-left: 6px; }
        .section-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 20px; font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        td { padding: 14px 20px; font-size: 14px; color: #374151; border-bottom: 1px solid #f1f5f9; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-published { background: #dcfce7; color: #16a34a; }
        .badge-draft { background: #fef9c3; color: #ca8a04; }
        .badge-pinned { background: #dbeafe; color: #2563eb; margin-left: 4px; }
        .actions a { font-size: 13px; margin-right: 10px; text-decoration: none; }
        .actions .edit { color: #3b82f6; }
        .actions .pin { color: #8b5cf6; }
        .actions .delete { color: #ef4444; }
        .pagination { margin-top: 20px; display: flex; gap: 8px; }
        .pagination a { padding: 7px 13px; border-radius: 6px; border: 1px solid #e2e8f0; text-decoration: none; font-size: 14px; color: #374151; }
        .pagination a.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }
        .empty { text-align: center; padding: 40px; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">Edu<span>CMS</span></div>
    <nav class="sidebar-menu">
        <a href="../index.php">Dashboard</a>
        <a href="list.php" class="active">All Posts</a>
        <a href="add.php">Add Post</a>
        <a href="../categories/list.php">Categories</a>
        <a href="../pages/list.php">Pages</a>
    </nav>
    <div class="sidebar-footer">
        Logged in as <strong><?= $_SESSION['admin_name'] ?></strong><br><br>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="main">
    <div class="page-header">
        <div class="page-title">All Posts</div>
        <a href="add.php" class="btn-primary">+ Add New Post</a>
    </div>

    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search posts..." value="<?= $search ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="section-card">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($posts) === 0): ?>
                    <tr><td colspan="5" class="empty">No posts found.</td></tr>
                <?php else: ?>
                <?php while ($row = mysqli_fetch_assoc($posts)): ?>
                <tr>
                    <td>
                        <?= sanitize($row['title']) ?>
                        <?php if ($row['is_pinned']): ?>
                            <span class="badge badge-pinned">Pinned</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['category_name'] ?? '—' ?></td>
                    <td>
                        <span class="badge badge-<?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                        <a href="list.php?pin=<?= $row['id'] ?>" class="pin"><?= $row['is_pinned'] ? 'Unpin' : 'Pin' ?></a>
                        <a href="list.php?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this post?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= $search ? '&search='.$search : '' ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>