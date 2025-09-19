
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
        $name = trim($_POST['name']);
        $variety = trim($_POST['variety']);
        $category = trim($_POST['category']);
        $donor_name = trim($_POST['donor_name']);
        $donor_email = trim($_POST['donor_email']);
        $quantity = (int)$_POST['quantity'];
        $season = trim($_POST['season']);
        $description = trim($_POST['description']);

        // Basic validation
        if (empty($name) || empty($variety) || empty($category) || empty($donor_name) || empty($donor_email)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($donor_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif ($quantity < 0) {
            $error = 'Quantity must be a positive number.';
        } else {
            if ($seedModel->update($id, $name, $variety, $category, $donor_name, $donor_email, $quantity, $season, $description)) {
                $message = 'Seed updated successfully!';
                // Refresh seed data
                $seed = $seedModel->getById($id);
            } else {
                $error = 'Failed to update seed. Please try again.';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Seed - Community Seed Bank</title>
    <link rel="stylesheet" href="../assets/edit_seed.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Community Seed Bank Management</h1>
            <nav>
                <a href="index.php" class="btn btn-primary">All Seeds</a>
                <a href="create_seed.php" class="btn btn-success">Add New Seed</a>
                <a href="transactions.php" class="btn btn-info">Transactions</a>
            </nav>
        </header>

        <main>
            <h2>Edit Seed</h2>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="seed-form">
                <div class="form-group">
                    <label for="name">Seed Name *</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($seed['name']); ?>" 
                           placeholder="e.g., Tomato" required>
                </div>

                <div class="form-group">
                    <label for="variety">Variety *</label>
                    <input type="text" id="variety" name="variety" 
                           value="<?php echo htmlspecialchars($seed['variety']); ?>" 
                           placeholder="e.g., Cherokee Purple" required>
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        <option value="Vegetables" <?php echo $seed['category'] === 'Vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                        <option value="Fruits" <?php echo $seed['category'] === 'Fruits' ? 'selected' : ''; ?>>Fruits</option>
                        <option value="Flowers" <?php echo $seed['category'] === 'Flowers' ? 'selected' : ''; ?>>Flowers</option>
                        <option value="Herbs" <?php echo $seed['category'] === 'Herbs' ? 'selected' : ''; ?>>Herbs</option>
                        <option value="Grains" <?php echo $seed['category'] === 'Grains' ? 'selected' : ''; ?>>Grains</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="donor_name">Donor Name *</label>
                    <input type="text" id="donor_name" name="donor_name" 
                           value="<?php echo htmlspecialchars($seed['donor_name']); ?>" 
                           placeholder="Full name" required>
                </div>

                <div class="form-group">
                    <label for="donor_email">Donor Email *</label>
                    <input type="email" id="donor_email" name="donor_email" 
                           value="<?php echo htmlspecialchars($seed['donor_email']); ?>" 
                           placeholder="email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity in Stock</label>
                    <input type="number" id="quantity" name="quantity" 
                           value="<?php echo $seed['quantity_in_stock']; ?>" 
                           min="0" placeholder="Number of seeds">
                </div>

                <div class="form-group">
                    <label for="season">Planting Season</label>
                    <select id="season" name="season">
                        <option value="">Select season</option>
                        <option value="Spring" <?php echo $seed['planting_season'] === 'Spring' ? 'selected' : ''; ?>>Spring</option>
                        <option value="Summer" <?php echo $seed['planting_season'] === 'Summer' ? 'selected' : ''; ?>>Summer</option>
                        <option value="Fall" <?php echo $seed['planting_season'] === 'Fall' ? 'selected' : ''; ?>>Fall</option>
                        <option value="Winter" <?php echo $seed['planting_season'] === 'Winter' ? 'selected' : ''; ?>>Winter</option>
                        <option value="Spring/Fall" <?php echo $seed['planting_season'] === 'Spring/Fall' ? 'selected' : ''; ?>>Spring/Fall</option>
                        <option value="Year-round" <?php echo $seed['planting_season'] === 'Year-round' ? 'selected' : ''; ?>>Year-round</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Any additional notes about this seed variety..."><?php echo htmlspecialchars($seed['description']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Update Seed</button>
                    <a href="view_seed.php?id=<?php echo $seed['id']; ?>" class="btn btn-primary">View Seed</a>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </main>

        <footer>
            <p>&copy; 2025 Community Seed Bank Management System</p>
        </footer>
    </div>
</body>
</html>