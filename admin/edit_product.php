<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
check_login();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php?view=products");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: dashboard.php?view=products");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $name = sanitize($_POST['name']);
    $category = sanitize($_POST['category']);
    $price = sanitize($_POST['price']);
    $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;
    $image_path = $product['image_path'];

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $file_ext = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                // Delete old image if exists
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $image_path = 'uploads/' . $new_filename;
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, image_path = ?, is_best_seller = ? WHERE id = ?");
    $stmt->execute([$name, $category, $price, $image_path, $is_best_seller, $id]);
    header("Location: dashboard.php?view=products&msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refine Product | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@800&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="aura-portal-bg"></div>

    <nav class="aura-nav">
        <div class="glass-pill">
            <a href="dashboard.php?view=overview">Portal</a>
            <a href="dashboard.php?view=products" class="active">Boutique</a>
            <a href="logout.php" style="color: var(--danger-color); border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px; margin-left: -20px;">Exit</a>
        </div>
    </nav>

    <main class="portal-container" style="max-width: 800px;">
        <header style="margin-bottom: 60px; text-align: center;">
            <h1 style="font-family: 'Cinzel', serif; font-size: 3rem; letter-spacing: 0.1em; text-transform: uppercase; margin: 0;">
                Refine <span style="color: var(--primary-color);">Product</span>
            </h1>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px;">Perfecting the essence of your collection.</p>
        </header>

        <section class="aura-card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div>
                        <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div>
                        <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Category / Subtitle</label>
                        <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div>
                        <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Price</label>
                        <input type="text" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    <div style="display: flex; align-items: flex-end; padding-bottom: 15px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 0.8rem; letter-spacing: 1px;">
                            <input type="checkbox" name="is_best_seller" <?php echo $product['is_best_seller'] ? 'checked' : ''; ?>>
                            BEST SELLER STATUS
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Visual Asset</label>

                    <?php if ($product['image_path']): ?>
                        <div style="margin-bottom: 25px; position: relative; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); max-width: 300px;">
                            <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="Current" style="width: 100%; display: block; opacity: 0.8;">
                        </div>
                    <?php endif; ?>

                    <input type="file" name="product_image" class="form-control" id="imageInput" accept="image/*">

                    <div id="imagePreview" style="margin-top: 25px; display: none;">
                        <div style="border-radius: 20px; overflow: hidden; border: 2px solid var(--primary-color); max-width: 300px;">
                            <img src="" alt="Preview" style="width: 100%; display: block;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 30px; margin-top: 50px;">
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 20px; font-size: 1rem; letter-spacing: 4px;">SYNCHRONIZE</button>
                    <a href="dashboard.php?view=products" style="color: #666; text-decoration: none; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#666'">Discard changes</a>
                </div>
            </form>
        </section>
    </main>

    <footer style="text-align: center; padding: 60px; color: #444; font-size: 0.8rem; letter-spacing: 1px;">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA PORTAL v2.0
    </footer>

    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('imagePreview');
            const previewImage = previewContainer.querySelector('img');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>
