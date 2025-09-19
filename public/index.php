<?php
    require_once '../src/Seed.php';


    $seedModel = new Seed();
    $seeds = $seedModel->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Seed Bank</title>
    <link rel="stylesheet" href="../assets/index.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Community Seed Bank Management</h1>
            <nav>
                <a href="index.php" class="btn btn-primary">All Seeds</a>
                <a href="create_seed.php" class="btn btn-success">Add New Seed</a>
                <a href="transactions.php" class="btn btn-info">Transactions</a>
            </nav>
        </header>

        <main>
            <h2>Seed Inventory</h2>
            
            <?php if (empty($seeds)): ?>
                <div class="alert alert-info">
                    <p>No seeds in the database yet. <a href="create_seed.php">Add the first seed</a>!</p>
                </div>
            <?php else: ?>
                <div class="seeds-grid">
                    <?php foreach ($seeds as $seed): ?>
                        <div class="seed-card">
                            <h3><?php echo htmlspecialchars($seed['name']); ?></h3>
                            <p class="variety"><?php echo htmlspecialchars($seed['variety']); ?></p>
                            <div class="seed-details">
                                <span class="category"><?php echo htmlspecialchars($seed['category']); ?></span>
                                <span class="stock <?php echo $seed['quantity_in_stock'] < 10 ? 'low-stock' : ''; ?>">
                                    Stock: <?php echo $seed['quantity_in_stock']; ?>
                                </span>
                            </div>
                            <p class="donor">Donated by: <?php echo htmlspecialchars($seed['donor_name']); ?></p>
                            <p class="season">Plant in: <?php echo htmlspecialchars($seed['planting_season']); ?></p>
                            
                            <div class="actions">
                                <a href="view_seed.php?id=<?php echo $seed['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                <a href="edit_seed.php?id=<?php echo $seed['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_seed.php?id=<?php echo $seed['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this seed?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; 2025 Community Seed Bank Management System</p>
        </footer>
    </div>
</body>
</html>