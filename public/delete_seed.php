
<?php
    require_once '../src/Seed.php';

    $seedModel = new Seed();
    $message = '';
    $error = '';

    // Get seed ID
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        header('Location: index.php');
        exit;
    }

    // Get seed data
    $seed = $seedModel->getById($id);

    if (!$seed) {
        header('Location: index.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirm_delete'])) {
            if ($seedModel->delete($id)) {
                header('Location: index.php?message=Seed deleted successfully');
                exit;
            } else {
                $error = 'Failed to delete seed. Please try again.';
            }
        } else {
            // User clicked cancel
            header('Location: view_seed.php?id=' . $id);
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Seed - Community Seed Bank</title>
    <link rel="stylesheet" href="../assets/delete_seed.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🌱 Community Seed Bank Management</h1>
            <nav>
                <a href="index.php" class="btn btn-primary">All Seeds</a>
                <a href="create_seed.php" class="btn btn-success">Add New Seed</a>
                <a href="transactions.php" class="btn btn-info">Transactions</a>
            </nav>
        </header>

        <main>
            <h2>Delete Seed</h2>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="delete-confirmation">
                <div class="alert alert-warning">
                    <h3>⚠️ Are you sure you want to delete this seed?</h3>
                    <p>This action cannot be undone. All associated transactions will also be deleted.</p>
                </div>

                <div class="seed-card">
                    <h3><?php echo htmlspecialchars($seed['name']); ?></h3>
                    <p class="variety"><?php echo htmlspecialchars($seed['variety']); ?></p>
                    <div class="seed-details">
                        <span class="category"><?php echo htmlspecialchars($seed['category']); ?></span>
                        <span class="stock">Stock: <?php echo $seed['quantity_in_stock']; ?></span>
                    </div>
                    <p class="donor">Donated by: <?php echo htmlspecialchars($seed['donor_name']); ?></p>
                    <?php if ($seed['planting_season']): ?>
                        <p class="season">Plant in: <?php echo htmlspecialchars($seed['planting_season']); ?></p>
                    <?php endif; ?>
                    <?php if ($seed['description']): ?>
                        <p class="description"><?php echo htmlspecialchars($seed['description']); ?></p>
                    <?php endif; ?>
                </div>

                <form method="POST" class="delete-form">
                    <div class="form-actions">
                        <button type="submit" name="confirm_delete" class="btn btn-danger">
                            Yes, Delete This Seed
                        </button>
                        <button type="submit" name="cancel" class="btn btn-secondary">
                            Cancel
                        </button>
                        <a href="view_seed.php?id=<?php echo $seed['id']; ?>" class="btn btn-primary">
                            Back to Seed Details
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Community Seed Bank Management System</p>
        </footer>
    </div>
</body>
</html>