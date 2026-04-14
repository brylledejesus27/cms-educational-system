<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireLogin_root();

// Get stats for dashboard
$total_posts     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts"))['count'];
$published_posts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE status='published'"))['count'];
$draft_posts     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE status='draft'"))['count'];
$total_categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM categories"))['count'];

// Get recent posts
$recent = mysqli_query($conn, "SELECT posts.*, categories.name as category_name FROM posts LEFT JOIN categories ON posts.category_id = categories.id ORDER BY posts.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — EduCMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #1e293b;
            color: #fff;
            padding: 24px 0;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            padding: 0 24px 24px;
            border-bottom: 1px solid #334155;
        }
        .sidebar-brand span { color: #3b82f6; }
        .sidebar-menu { margin-top: 16px; flex: 1; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            transition: all .2s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #334155;
            color: #fff;
        }
        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid #334155;
            font-size: 13px;
            color: #64748b;
        }
        .sidebar-footer a { color: #ef4444; text-decoration: none; font-size: 13px; }

        /* Main content */
        .main { margin-left: 240px; padding: 32px; flex: 1; }
        .page-title { font-size: 22px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
        .page-sub { font-size: 14px; color: #64748b; margin-bottom: 28px; }

        /* Stat cards */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 32px; }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        .stat-label { font-size: 13px; color: #64748b; margin-bottom: 8px; }
        .stat-number { font-size: 28px; font-weight: 700; color: #1e293b; }
        .stat-card.blue .stat-number { color: #3b82f6; }
        .stat-card.green .stat-number { color: #22c55e; }
        .stat-card.amber .stat-number { color: #f59e0b; }
        .stat-card.purple .stat-number { color: #8b5cf6; }

        /* Recent posts table */
        .section-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .section-head {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-head h2 { font-size: 15px; font-weight: 600; color: #1e293b; }
        .section-head a { font-size: 13px; color: #3b82f6; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 12px 24px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 14px 24px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f1f5f9;
        }
        tr:last-child td { border-bottom: none; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-published { background: #dcfce7; color: #16a34a; }
        .badge-draft { background: #fef9c3; color: #ca8a04; }
        .badge-pinned { background: #dbeafe; color: #2563eb; margin-left: 4px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">Edu<span>CMS</span></div>
    <nav class="sidebar-menu">
        <a href="index.php" class="active">Dashboard</a>
        <a href="posts/list.php">All Posts</a>
        <a href="posts/add.php">Add Post</a>
        <a href="categories/list.php">Categories</a>
        <a href="pages/list.php">Pages</a>
    </nav>
    <div class="sidebar-footer">
        Logged in as <strong><?= $_SESSION['admin_name'] ?></strong><br><br>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="main">
    <div class="page-title">Dashboard</div>
    <div class="page-sub">Welcome back, <?= $_SESSION['admin_name'] ?>!</div>

    <div class="stats">
        <div class="stat-card blue">
            <div class="stat-label">Total Posts</div>
            <div class="stat-number"><?= $total_posts ?></div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Published</div>
            <div class="stat-number"><?= $published_posts ?></div>
        </div>
        <div class="stat-card amber">
            <div class="stat-label">Drafts</div>
            <div class="stat-number"><?= $draft_posts ?></div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Categories</div>
            <div class="stat-number"><?= $total_categories ?></div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-head">
            <h2>Recent Posts</h2>
            <a href="posts/list.php">View all</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($recent)): ?>
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
                    <td><?= timeAgo($row['created_at']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>