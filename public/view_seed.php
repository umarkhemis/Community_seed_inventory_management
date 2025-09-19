
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

// Get transactions for this seed
$transactions = $seedModel->getTransactions($id);

// Handle stock transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_type = trim($_POST['transaction_type']);
    $quantity = (int)$_POST['quantity'];
    $member_name = trim($_POST['member_name']);
    $notes = trim($_POST['notes']);

    if (empty($member_name) || $quantity <= 0) {
        $error = 'Please provide member name and valid quantity.';
    } elseif ($transaction_type === 'OUT' && $quantity > $seed['quantity_in_stock']) {
        $error = 'Cannot take out more seeds than available in stock.';
    } else {
        // Add transaction
        if ($seedModel->addTransaction($id, $transaction_type, $quantity, $member_name, $notes)) {
            // Update stock
            $new_quantity = $transaction_type === 'IN' 
                ? $seed['quantity_in_stock'] + $quantity 
                : $seed['quantity_in_stock'] - $quantity;
            
            if ($seedModel->updateStock($id, $new_quantity)) {
                $message = 'Transaction recorded successfully!';
                // Refresh data
                $seed = $seedModel->getById($id);
                $transactions = $seedModel->getTransactions($id);
            } else {
                $error = 'Transaction recorded but failed to update stock.';
            }
        } else {
            $error = 'Failed to record transaction. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Seed - Community Seed Bank</title>
    <link rel="stylesheet" href="../assets/view_seed.css">
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
            <h2>Seed Details</h2>

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

            <!-- Seed Information -->
            <div class="seed-details-view">
                <div class="seed-card large">
                    <h3><?php echo htmlspecialchars($seed['name']); ?></h3>
                    <p class="variety"><?php echo htmlspecialchars($seed['variety']); ?></p>
                    
                    <div class="seed-info">
                        <div class="info-row">
                            <strong>Category:</strong> 
                            <span class="category"><?php echo htmlspecialchars($seed['category']); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <strong>Quantity in Stock:</strong> 
                            <span class="stock <?php echo $seed['quantity_in_stock'] < 10 ? 'low-stock' : ''; ?>">
                                <?php echo $seed['quantity_in_stock']; ?> seeds
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <strong>Donor:</strong> <?php echo htmlspecialchars($seed['donor_name']); ?>
                        </div>
                        
                        <div class="info-row">
                            <strong>Donor Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($seed['donor_email']); ?>">
                                <?php echo htmlspecialchars($seed['donor_email']); ?>
                            </a>
                        </div>
                        
                        <?php if ($seed['planting_season']): ?>
                        <div class="info-row">
                            <strong>Planting Season:</strong> <?php echo htmlspecialchars($seed['planting_season']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-row">
                            <strong>Date Added:</strong> <?php echo date('M j, Y', strtotime($seed['date_added'])); ?>
                        </div>
                        
                        <?php if ($seed['description']): ?>
                        <div class="info-row">
                            <strong>Description:</strong>
                            <p class="description"><?php echo nl2br(htmlspecialchars($seed['description'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="actions">
                        <a href="edit_seed.php?id=<?php echo $seed['id']; ?>" class="btn btn-warning">Edit Seed</a>
                        <a href="delete_seed.php?id=<?php echo $seed['id']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this seed?')">Delete</a>
                        <a href="index.php" class="btn btn-secondary">Back to All Seeds</a>
                    </div>
                </div>
            </div>

            <!-- Stock Transaction Form -->
            <div class="transaction-form-section">
                <h3>Record Transaction</h3>
                
                <form method="POST" class="transaction-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="transaction_type">Transaction Type *</label>
                            <select id="transaction_type" name="transaction_type" required>
                                <option value="">Select type</option>
                                <option value="IN">Seeds In (Donation/Return)</option>
                                <option value="OUT">Seeds Out (Distribution)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity *</label>
                            <input type="number" id="quantity" name="quantity" min="1" 
                                   placeholder="Number of seeds" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="member_name">Member Name *</label>
                        <input type="text" id="member_name" name="member_name" 
                               placeholder="Full name" required>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="2" 
                                  placeholder="Optional notes about this transaction..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Record Transaction</button>
                    </div>
                </form>
            </div>

            <!-- Transaction History -->
            <div class="transaction-history">
                <h3>Transaction History</h3>
                
                <?php if (empty($transactions)): ?>
                    <div class="alert alert-info">
                        <p>No transactions recorded for this seed yet.</p>
                    </div>
                <?php else: ?>
                    <div class="transactions-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Member</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y g:i A', strtotime($transaction['transaction_date'])); ?></td>
                                        <td>
                                            <span class="transaction-type <?php echo strtolower($transaction['transaction_type']); ?>">
                                                <?php echo $transaction['transaction_type'] === 'IN' ? 'Seeds In' : 'Seeds Out'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $transaction['quantity']; ?></td>
                                        <td><?php echo htmlspecialchars($transaction['member_name']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['notes']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Community Seed Bank Management System</p>
        </footer>
    </div>
</body>
</html>