<?php
session_start();

// 1. Check login status
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

include 'config.php';

// 2. Fetch User Info
$user_id = $_SESSION['id'];
$username = $_SESSION['username'];

// 3. Fetch Available Services (For the "Order Now" form)
try {
    $stmt = $pdo->query("SELECT * FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = []; // Handle error gracefully if table doesn't exist
}

// 4. Fetch Order History
$sql = "SELECT o.id, s.name AS service_name, s.price, o.quantity, o.status, o.order_date 
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        WHERE o.user_id = :user_id 
        ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-4 rounded-3 shadow-sm text-white" style="background: linear-gradient(to right, var(--primary-color), var(--primary-dark));">
                <h2 class="fw-bold mb-0">Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h2>
                <p class="mb-0 opacity-75">Here is your laundry overview.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 ps-4">
                    <h5 class="fw-bold text-primary"><i class="fas fa-plus-circle me-2"></i>Place New Order</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (count($services) > 0): ?>
                        <form method="post" action="place_order.php">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Select Service</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="" selected disabled>Choose a service...</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo $service['id']; ?>">
                                            <?php echo htmlspecialchars($service['name']); ?> - $<?php echo htmlspecialchars($service['price']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Quantity (Items)</label>
                                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                            </div>

                            <button type="submit" class="btn btn-nav-cta w-100 py-2">
                                Book Service Now <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            No services found in database. Please ask Admin to add services.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 ps-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-primary"><i class="fas fa-history me-2"></i>Your Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Order ID</th>
                                    <th class="py-3">Service Info</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-end pe-4">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($orders) > 0): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold">#<?php echo htmlspecialchars($order['id']); ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($order['service_name']); ?></span>
                                                    <span class="small text-muted"><?php echo htmlspecialchars($order['quantity']); ?> items</span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                    // Color coding for status
                                                    $badgeClass = 'bg-secondary';
                                                    if ($order['status'] === 'Completed') $badgeClass = 'bg-success';
                                                    if ($order['status'] === 'Pending') $badgeClass = 'bg-warning text-dark';
                                                    if ($order['status'] === 'Processing') $badgeClass = 'bg-info text-dark';
                                                ?>
                                                <span class="badge rounded-pill <?php echo $badgeClass; ?> px-3 py-2">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 text-muted small">
                                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i><br>
                                            No orders placed yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>