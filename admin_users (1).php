<?php
session_start();
include('config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['action'], $_GET['type'], $_GET['id'])) {
    $action = $_GET['action'];
    $type = $_GET['type'];
    $id = intval($_GET['id']);
    $message = '';
    
    // Determine the table based on type
    if ($type == 'creator') {
        $table = 'content_creators';
    } elseif ($type == 'brand') {
        $table = 'brands';
    } elseif ($type == 'applied') {
        $table = 'applied_tasks';
    } else {
        $table = '';
    }
    
    if (!empty($table)) {
        if ($action == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
            $stmt->execute([$id]);
            $message = ucfirst($type) . " deleted successfully.";
        } elseif ($action == 'block') {
            // Only applicable for creators and brands
            if ($type != 'applied') {
                $stmt = $pdo->prepare("UPDATE $table SET status = 0 WHERE id = ?");
                $stmt->execute([$id]);
                $message = ucfirst($type) . " blocked successfully.";
            }
        } elseif ($action == 'unblock') {
            if ($type != 'applied') {
                $stmt = $pdo->prepare("UPDATE $table SET status = 1 WHERE id = ?");
                $stmt->execute([$id]);
                $message = ucfirst($type) . " unblocked successfully.";
            }
        }
        header("Location: admin_users.php?msg=" . urlencode($message));
        exit();
    }
}

// Fetch Content Creators
$queryCreators = "SELECT * FROM content_creators ORDER BY created_at DESC";
$stmt = $pdo->prepare($queryCreators);
$stmt->execute();
$contentCreators = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Brands
$queryBrands = "SELECT * FROM brands ORDER BY created_at DESC";
$stmt2 = $pdo->prepare($queryBrands);
$stmt2->execute();
$brands = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Fetch Applied Tasks (joined with the content details)
$queryApplied = "SELECT at.*, c.title FROM applied_tasks at JOIN contents c ON at.task_id = c.id ORDER BY at.applied_date DESC";
$stmtApplied = $pdo->prepare($queryApplied);
$stmtApplied->execute();
$appliedTasks = $stmtApplied->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Admin Panel - Manage Users & Brands</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Admin Panel - Manage Content Creators, Brands & Applied Tasks</h2>
    <a href="admin.php" class="btn btn-secondary mb-3">Back to Admin Panel</a>
    
    <!-- Display Action Feedback Message -->
    <?php if (isset($_GET['msg']) && !empty($_GET['msg'])): ?>
       <div class="alert alert-success">
           <?php echo htmlspecialchars($_GET['msg']); ?>
       </div>
    <?php endif; ?>
    
    <!-- Content Creators Section -->
    <h3>Content Creators</h3>
    <table class="table table-bordered">
       <thead>
         <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Instagram</th>
            <th>Phone</th>
            <th>Followers</th>
            <th>Content Types</th>
            <th>Address</th>
            <th>Status</th>
            <th>Registered On</th>
            <th>Actions</th>
         </tr>
       </thead>
       <tbody>
         <?php foreach($contentCreators as $creator): ?>
               <tr>
                  <td><?php echo htmlspecialchars($creator['id']); ?></td>
                  <td><?php echo htmlspecialchars($creator['name']); ?></td>
                  <td><?php echo htmlspecialchars($creator['instagram']); ?></td>
                  <td><?php echo htmlspecialchars($creator['phone']); ?></td>
                  <td><?php echo htmlspecialchars($creator['followers']); ?></td>
                  <td><?php echo htmlspecialchars($creator['content_types']); ?></td>
                  <td><?php echo htmlspecialchars($creator['address']); ?></td>
                  <td><?php echo ($creator['status'] == 1) ? 'Active' : 'Blocked'; ?></td>
                  <td><?php echo htmlspecialchars($creator['created_at']); ?></td>
                  <td>
                     <a href="admin_edit_creator.php?id=<?php echo $creator['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                     <a href="admin_users.php?action=delete&type=creator&id=<?php echo $creator['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this creator?');">Delete</a>
                     <?php if($creator['status'] == 1): ?>
                        <a href="admin_users.php?action=block&type=creator&id=<?php echo $creator['id']; ?>" class="btn btn-sm btn-secondary">Block</a>
                     <?php else: ?>
                        <a href="admin_users.php?action=unblock&type=creator&id=<?php echo $creator['id']; ?>" class="btn btn-sm btn-success">Unblock</a>
                     <?php endif; ?>
                  </td>
               </tr>
         <?php endforeach; ?>
       </tbody>
    </table>
    
    <!-- Brands Section -->
    <h3>Brands</h3>
    <table class="table table-bordered">
       <thead>
         <tr>
            <th>ID</th>
            <th>Brand Name</th>
            <th>Instagram</th>
            <th>Phone</th>
            <th>Followers</th>
            <th>Content/Product Types</th>
            <th>Address</th>
            <th>Status</th>
            <th>Registered On</th>
            <th>Actions</th>
         </tr>
       </thead>
       <tbody>
         <?php foreach($brands as $brand): ?>
               <tr>
                  <td><?php echo htmlspecialchars($brand['id']); ?></td>
                  <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                  <td><?php echo htmlspecialchars($brand['instagram']); ?></td>
                  <td><?php echo htmlspecialchars($brand['phone']); ?></td>
                  <td><?php echo htmlspecialchars($brand['followers']); ?></td>
                  <td><?php echo htmlspecialchars($brand['content_types']); ?></td>
                  <td><?php echo htmlspecialchars($brand['address']); ?></td>
                  <td><?php echo ($brand['status'] == 1) ? 'Active' : 'Blocked'; ?></td>
                  <td><?php echo htmlspecialchars($brand['created_at']); ?></td>
                  <td>
                     <a href="admin_edit_brand.php?id=<?php echo $brand['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                     <a href="admin_users.php?action=delete&type=brand&id=<?php echo $brand['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this brand?');">Delete</a>
                  </td>
               </tr>
         <?php endforeach; ?>
       </tbody>
    </table>
    
    <!-- Applied Tasks Section -->
    <h3>Applied Tasks</h3>
    <table class="table table-bordered">
       <thead>
         <tr>
            <th>ID</th>
            <th>User Email</th>
            <th>Task Title</th>
            <th>Applied Date</th>
            <th>Actions</th>
         </tr>
       </thead>
       <tbody>
         <?php if(!empty($appliedTasks)): ?>
           <?php foreach($appliedTasks as $applied): ?>
             <tr>
                <td><?php echo htmlspecialchars($applied['id']); ?></td>
                <td><?php echo htmlspecialchars($applied['user_email']); ?></td>
                <td><?php echo htmlspecialchars($applied['title']); ?></td>
                <td><?php echo htmlspecialchars($applied['applied_date']); ?></td>
                <td>
                  <a href="admin_users.php?action=delete&type=applied&id=<?php echo $applied['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this applied task?');">Delete</a>
                </td>
             </tr>
           <?php endforeach; ?>
         <?php else: ?>
           <tr>
             <td colspan="5" class="text-center">No applied tasks found.</td>
           </tr>
         <?php endif; ?>
       </tbody>
    </table>
</div>
</body>
</html>
