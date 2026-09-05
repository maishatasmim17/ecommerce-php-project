<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('Location: login.php');
   exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all cart items with current stock information
$cart_items = [];
$grand_total = 0;

$stmt = $conn->prepare("
    SELECT c.*, p.stock AS current_stock 
    FROM cart c
    JOIN product p ON c.pid = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cart_items as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $grand_total += $subtotal;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Your Cart</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <style>
        :root {
            --primary: #ff6b6b;
            --secondary: #4ecdc4;
            --dark: #292f36;
            --light: #f7f7f7;
            --gray: #e0e0e0;
            --success: #27ae60;
            --danger: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header Styles */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 15px 0;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin: 0 15px;
        }
        
        nav ul li a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: var(--primary);
        }
        
        .icons {
            display: flex;
            align-items: center;
        }
        
        .icons a {
            margin-left: 15px;
            color: var(--dark);
            font-size: 18px;
            position: relative;
        }
        
        .icons a span {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary);
            color: white;
            font-size: 12px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Cart Section */
        .cart-container {
            padding: 50px 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 36px;
            color: var(--dark);
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }
        
        .cart-items {
            margin-bottom: 40px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            position: relative;
        }
        
        .remove-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            color: var(--danger);
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .remove-btn:hover {
            transform: scale(1.2);
        }
        
        .cart-item-image {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-details {
            flex: 1;
        }
        
        .cart-item-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .cart-item-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .stock-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }
        
        .stock-label {
            margin-right: 10px;
            font-weight: 600;
        }
        
        .stock-value {
            font-weight: 700;
            color: var(--success);
        }
        
        .out-of-stock {
            color: var(--danger);
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            background: var(--gray);
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .quantity-input {
            width: 50px;
            height: 35px;
            text-align: center;
            margin: 0 10px;
            border: 1px solid var(--gray);
            border-radius: 5px;
            font-size: 16px;
        }
        
        .subtotal {
            font-size: 18px;
            font-weight: 600;
            margin-left: 20px;
        }
        
        .cart-summary {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray);
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 600;
        }
        
        .summary-value {
            font-weight: 700;
        }
        
        .grand-total {
            font-size: 22px;
            color: var(--primary);
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        
        .action-btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .delete-all {
            background: var(--danger);
            color: white;
        }
        
        .delete-all:hover {
            background: #c0392b;
            transform: translateY(-3px);
        }
        
        .continue-shopping {
            background: var(--gray);
            color: var(--dark);
        }
        
        .continue-shopping:hover {
            background: #d0d0d0;
            transform: translateY(-3px);
        }
        
        .checkout {
            background: var(--success);
            color: white;
        }
        
        .checkout:hover {
            background: #219653;
            transform: translateY(-3px);
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 80px;
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .empty-cart h3 {
            font-size: 28px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .empty-cart p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .cart-item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .quantity-controls {
                width: 100%;
                justify-content: space-between;
            }
            
            .cart-actions {
                flex-direction: column;
            }
        }
   </style>
</head>
<body>

<header>
    <div class="container">
        <div class="header-content">
            <a href="home.php" class="logo">Crafty's Shelf</a>
            
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="orders.php">Orders</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            
            <div class="icons">
                <a href="cart.php"><i class="fas fa-shopping-cart"></i><span id="cart-count"><?= count($cart_items) ?></span></a>
            </div>
        </div>
    </div>
</header>

<section class="cart-container">
    <div class="container">
        <h1 class="section-title">Your Shopping Cart</h1>
        
        <?php if (!empty($cart_items)): ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-id="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>">
                        <div class="remove-btn" onclick="removeItem(<?= $item['id'] ?>)">
                            <i class="fas fa-times"></i>
                        </div>
                        
                        <div class="cart-item-image">
                            <img src="uploaded_img/<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        
                        <div class="cart-item-details">
                            <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="cart-item-price"><?= number_format($item['price'], 2) ?> tk</div>
                            
                            <div class="stock-info">
                                <span class="stock-label">Available Stock:</span>
                                <span class="stock-value <?= $item['current_stock'] <= 0 ? 'out-of-stock' : '' ?>">
                                    <?= $item['current_stock'] ?>
                                </span>
                            </div>
                            
                            <div class="quantity-controls">
                                <button class="quantity-btn minus" onclick="updateQuantity(<?= $item['id'] ?>, 'decrease')" 
                                    <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>-</button>
                                
                                <input type="number" class="quantity-input" id="qty-<?= $item['id'] ?>" 
                                    value="<?= $item['quantity'] ?>" min="1" max="<?= $item['current_stock'] ?>"
                                    onchange="updateQuantityInput(<?= $item['id'] ?>, this.value)">
                                
                                <button class="quantity-btn plus" onclick="updateQuantity(<?= $item['id'] ?>, 'increase')"
                                    <?= $item['quantity'] >= $item['current_stock'] ? 'disabled' : '' ?>>+</button>
                                
                                <div class="subtotal">Subtotal: <span id="subtotal-<?= $item['id'] ?>"><?= number_format($item['price'] * $item['quantity'], 2) ?></span> tk</div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <div class="summary-label">Subtotal</div>
                    <div class="summary-value" id="subtotal-sum"><?= number_format($grand_total, 2) ?> tk</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Shipping</div>
                    <div class="summary-value">60.00 tk</div>
                </div>
                <div class="summary-row grand-total">
                    <div class="summary-label">Grand Total</div>
                    <div class="summary-value" id="grand-total"><?= number_format($grand_total + 60, 2) ?> tk</div>
                </div>
            </div>
            
            <div class="cart-actions">
                <a href="home.php" class="action-btn continue-shopping">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
                <button class="action-btn delete-all" onclick="deleteAll()">
                    <i class="fas fa-trash"></i> Delete All
                </button>
                <a href="checkout.php" class="action-btn checkout">
                    Proceed to Checkout <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty!</h3>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="home.php" class="action-btn continue-shopping">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Update quantity with buttons
function updateQuantity(itemId, action) {
    const input = document.getElementById(`qty-${itemId}`);
    let quantity = parseInt(input.value);
    const max = parseInt(input.max);
    
    if (action === 'increase' && quantity < max) {
        quantity++;
    } else if (action === 'decrease' && quantity > 1) {
        quantity--;
    }
    
    input.value = quantity;
    updateQuantityInput(itemId, quantity);
}

// Update quantity with input field
function updateQuantityInput(itemId, quantity) {
    quantity = parseInt(quantity);
    const max = parseInt(document.getElementById(`qty-${itemId}`).max);
    
    // Validate quantity
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
    } else if (quantity > max) {
        quantity = max;
        alert(`Only ${max} items available in stock!`);
    }
    
    document.getElementById(`qty-${itemId}`).value = quantity;
    
    // Update UI immediately
    updateItemSubtotal(itemId, quantity);
    
    // Send update to server
    fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            itemId: itemId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update stock if needed
            if (data.newStock !== undefined) {
                const stockElement = document.querySelector(`.cart-item[data-id="${itemId}"] .stock-value`);
                if (stockElement) {
                    stockElement.textContent = data.newStock;
                    
                    // Update max attribute
                    document.getElementById(`qty-${itemId}`).max = data.newStock;
                    
                    // Disable plus button if needed
                    const plusBtn = document.querySelector(`.cart-item[data-id="${itemId}"] .plus`);
                    if (plusBtn) {
                        plusBtn.disabled = quantity >= data.newStock;
                    }
                }
            }
            
            // Update cart count
            if (data.cartCount !== undefined) {
                document.getElementById('cart-count').textContent = data.cartCount;
            }
        } else {
            alert('Error updating cart: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the cart.');
    });
}

// Update item subtotal and grand total
function updateItemSubtotal(itemId, quantity) {
    const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
    const price = parseFloat(itemElement.getAttribute('data-price'));
    const subtotal = price * quantity;
    
    // Update subtotal display
    document.getElementById(`subtotal-${itemId}`).textContent = subtotal.toFixed(2);
    
    // Update grand total
    updateGrandTotal();
}

// Calculate and update grand total
function updateGrandTotal() {
    let subtotalSum = 0;
    const subtotalElements = document.querySelectorAll('.subtotal span');
    
    subtotalElements.forEach(element => {
        subtotalSum += parseFloat(element.textContent);
    });
    
    document.getElementById('subtotal-sum').textContent = subtotalSum.toFixed(2);
    document.getElementById('grand-total').textContent = (subtotalSum + 60).toFixed(2);
}

// Remove item from cart
function removeItem(itemId) {
    if (confirm('Remove this item from your cart?')) {
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ itemId: itemId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove item from UI
                const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
                if (itemElement) {
                    itemElement.remove();
                }
                
                // Update cart count
                document.getElementById('cart-count').textContent = data.cartCount;
                
                // Update grand total
                updateGrandTotal();
                
                // Show message if cart is empty
                if (data.cartCount == 0) {
                    location.reload(); // Reload to show empty cart message
                }
            } else {
                alert('Error removing item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the item.');
        });
    }
}

// Delete all items from cart
function deleteAll() {
    if (confirm('Remove all items from your cart?')) {
        fetch('delete_all_from_cart.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove all items from UI
                document.querySelector('.cart-items').innerHTML = '';
                
                // Update cart count
                document.getElementById('cart-count').textContent = '0';
                
                // Update grand total
                document.getElementById('subtotal-sum').textContent = '0.00';
                document.getElementById('grand-total').textContent = '60.00';
                
                // Show empty cart message
                location.reload(); // Reload to show empty cart message
            } else {
                alert('Error removing items: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing items.');
        });
    }
}
</script>

</body>
</html>