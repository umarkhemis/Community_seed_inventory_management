
<?php
    require_once '../src/Seed.php';

    $seedModel = new Seed();

    // Get all seeds for dropdown
    $seeds = $seedModel->getAll();

    // Get filter parameters
    $filter_seed = isset($_GET['seed_id']) ? (int)$_GET['seed_id'] : 0;
    $filter_type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $filter_member = isset($_GET['member']) ? trim($_GET['member']) : '';

    // Build query for filtered transactions
    $sql = "SELECT st.*, s.name as seed_name, s.variety as seed_variety 
            FROM seed_transactions st 
            JOIN seeds s ON st.seed_id = s.id 
            WHERE 1=1";
    $params = [];
    $types = "";

    if ($filter_seed > 0) {
        $sql .= " AND st.seed_id = ?";
        $types .= "i";
        $params[] = $filter_seed;
    }

    if (!empty($filter_type)) {
        $sql .= " AND st.transaction_type = ?";
        $types .= "s";
        $params[] = $filter_type;
    }

    if (!empty($filter_member)) {
        $sql .= " AND st.member_name LIKE ?";
        $types .= "s";
        $params[] = "%" . $filter_member . "%";
    }

    $sql .= " ORDER BY st.transaction_date DESC LIMIT 100";

    // Execute query
    $db = new Database();
    $stmt = $db->executeQuery($sql, $types, $params);
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();

    // Get summary statistics
    $total_in = 0;
    $total_out = 0;
    foreach ($transactions as $transaction) {
        if ($transaction['transaction_type'] === 'IN') {
            $total_in += $transaction['quantity'];
        } else {
            $total_out += $transaction['quantity'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Community Seed Bank</title>
    <link rel="stylesheet" href="../assets/transactions.css">
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
            <h2>Transaction History</h2>

            <!-- Summary Statistics -->
            <div class="stats-summary">
                <div class="stat-card">
                    <h3>Total Seeds In</h3>
                    <div class="stat-number in"><?php echo $total_in; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Seeds Out</h3>
                    <div class="stat-number out"><?php echo $total_out; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Net Activity</h3>
                    <div class="stat-number <?php echo ($total_in - $total_out) >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo ($total_in - $total_out >= 0 ? '+' : '') . ($total_in - $total_out); ?>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="filter-section">
                <h3>Filter Transactions</h3>
                <form method="GET" class="filter-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="seed_id">Seed Type</label>
                            <select id="seed_id" name="seed_id">
                                <option value="">All Seeds</option>
                                <?php foreach ($seeds as $seed): ?>
                                    <option value="<?php echo $seed['id']; ?>" 
                                            <?php echo $filter_seed === $seed['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($seed['name'] . ' - ' . $seed['variety']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type">Transaction Type</label>
                            <select id="type" name="type">
                                <option value="">All Types</option>
                                <option value="IN" <?php echo $filter_type === 'IN' ? 'selected' : ''; ?>>Seeds In</option>
                                <option value="OUT" <?php echo $filter_type === 'OUT' ? 'selected' : ''; ?>>Seeds Out</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="member">Member Name</label>
                            <input type="text" id="member" name="member" 
                                   value="<?php echo htmlspecialchars($filter_member); ?>" 
                                   placeholder="Search member name...">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="transactions.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="transactions-section">
                <?php if (empty($transactions)): ?>
                    <div class="alert alert-info">
                        <p>No transactions found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="transactions-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Seed</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Member</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td class="date-cell">
                                            <?php echo date('M j, Y', strtotime($transaction['transaction_date'])); ?>
                                            <br>
                                            <small><?php echo date('g:i A', strtotime($transaction['transaction_date'])); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($transaction['seed_name']); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars($transaction['seed_variety']); ?></small>
                                        </td>
                                        <td>
                                            <span class="transaction-type <?php echo strtolower($transaction['transaction_type']); ?>">
                                                <?php echo $transaction['transaction_type'] === 'IN' ? 'Seeds In' : 'Seeds Out'; ?>
                                            </span>
                                        </td>
                                        <td class="quantity-cell">
                                            <span class="quantity <?php echo strtolower($transaction['transaction_type']); ?>">
                                                <?php echo $transaction['transaction_type'] === 'IN' ? '+' : '-'; ?><?php echo $transaction['quantity']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($transaction['member_name']); ?></td>
                                        <td class="notes-cell">
                                            <?php if (!empty($transaction['notes'])): ?>
                                                <?php echo htmlspecialchars($transaction['notes']); ?>
                                            <?php else: ?>
                                                <em>No notes</em>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="view_seed.php?id=<?php echo $transaction['seed_id']; ?>" 
                                               class="btn btn-primary btn-sm">View Seed</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (count($transactions) >= 100): ?>
                        <div class="alert alert-info">
                            <p>Showing the most recent 100 transactions. Use filters to narrow your search.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="index.php" class="btn btn-primary">View All Seeds</a>
                    <a href="create_seed.php" class="btn btn-success">Add New Seed</a>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Community Seed Bank Management System</p>
        </footer>
    </div>
</body>
</html>