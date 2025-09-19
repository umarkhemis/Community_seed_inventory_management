<?php
require_once '../src/Seed.php';

$message = '';
$error = '';

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
        $seedModel = new Seed();
        
        if ($seedModel->create($name, $variety, $category, $donor_name, $donor_email, $quantity, $season, $description)) {
            $message = 'Seed added successfully!';
            // Clear form
            $name = $variety = $category = $donor_name = $donor_email = $season = $description = '';
            $quantity = 0;
        } else {
            $error = 'Failed to add seed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Seed - Community Seed Bank</title>
    <link rel="stylesheet" href="../assets/create_seed.css">
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
            <h2>Add New Seed</h2>

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
                           value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                           placeholder="e.g., Tomato" required>
                </div>

                <div class="form-group">
                    <label for="variety">Variety *</label>
                    <input type="text" id="variety" name="variety" 
                           value="<?php echo htmlspecialchars($variety ?? ''); ?>" 
                           placeholder="e.g., Cherokee Purple" required>
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        <option value="Vegetables" <?php echo (isset($category) && $category === 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                        <option value="Fruits" <?php echo (isset($category) && $category === 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                        <option value="Flowers" <?php echo (isset($category) && $category === 'Flowers') ? 'selected' : ''; ?>>Flowers</option>
                        <option value="Herbs" <?php echo (isset($category) && $category === 'Herbs') ? 'selected' : ''; ?>>Herbs</option>
                        <option value="Grains" <?php echo (isset($category) && $category === 'Grains') ? 'selected' : ''; ?>>Grains</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="donor_name">Donor Name *</label>
                    <input type="text" id="donor_name" name="donor_name" 
                           value="<?php echo htmlspecialchars($donor_name ?? ''); ?>" 
                           placeholder="Full name" required>
                </div>

                <div class="form-group">
                    <label for="donor_email">Donor Email *</label>
                    <input type="email" id="donor_email" name="donor_email" 
                           value="<?php echo htmlspecialchars($donor_email ?? ''); ?>" 
                           placeholder="email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Initial Quantity</label>
                    <input type="number" id="quantity" name="quantity" 
                           value="<?php echo htmlspecialchars($quantity ?? '0'); ?>" 
                           min="0" placeholder="Number of seeds">
                </div>

                <div class="form-group">
                    <label for="season">Planting Season</label>
                    <select id="season" name="season">
                        <option value="">Select season</option>
                        <option value="Spring" <?php echo (isset($season) && $season === 'Spring') ? 'selected' : ''; ?>>Spring</option>
                        <option value="Summer" <?php echo (isset($season) && $season === 'Summer') ? 'selected' : ''; ?>>Summer</option>
                        <option value="Fall" <?php echo (isset($season) && $season === 'Fall') ? 'selected' : ''; ?>>Fall</option>
                        <option value="Winter" <?php echo (isset($season) && $season === 'Winter') ? 'selected' : ''; ?>>Winter</option>
                        <option value="Spring/Fall" <?php echo (isset($season) && $season === 'Spring/Fall') ? 'selected' : ''; ?>>Spring/Fall</option>
                        <option value="Year-round" <?php echo (isset($season) && $season === 'Year-round') ? 'selected' : ''; ?>>Year-round</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Any additional notes about this seed variety..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Add Seed</button>
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