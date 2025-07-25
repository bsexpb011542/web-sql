<?php
// search_sup.php

session_start();
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit();
}

$member_id = $_SESSION['member_id'];

// 資料庫連接
$dbConnection = mysqli_connect("localhost", "root", "", "fruit_company");

// 檢查連接是否成功
if (!$dbConnection) {
    die("資料庫連接失敗: " . mysqli_connect_error());
}

// 處理查詢供應商資料
$searchTerm = isset($_POST['search_term']) ? mysqli_real_escape_string($dbConnection, $_POST['search_term']) : '';
$search_result = null;

if (!empty($searchTerm)) {
    if (preg_match('/^\d{8}$/', $searchTerm) || mb_strlen($searchTerm, 'UTF-8') <= 12) {
        $search_query = "SELECT * FROM 供應商資料表 
                         WHERE (供應商統一編號 = '$searchTerm' OR 水果供應商名稱 = '$searchTerm') 
                         AND 供應商隱藏 = 0";
        $search_result = mysqli_query($dbConnection, $search_query);
    } else {
        $error_message = "請輸入正確的供應商統一編號（八碼數字）或水果供應商名稱（不超過12個字元）";
    }
}

// 查詢所有供應商資料
$all_query = "SELECT * FROM 供應商資料表 WHERE 供應商隱藏 = 0";
$all_result = mysqli_query($dbConnection, $all_query);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>供應商資料查詢</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: url('https://source.unsplash.com/1920x1080/?supplier') no-repeat center center/cover;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        .hero-section h1 {
            color: black !important;
        }
    </style>
</head>
<body>

<!-- 導覽列 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="#">🍎 水果訂貨平台 - 查詢供應商</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="member_dashboard.php">會員中心</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">登出</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero 區塊 -->
<section class="hero-section text-center py-5">
    <div class="container">
        <h1>查詢供應商資料</h1>
        <p class="lead">輸入供應商統一編號或名稱以進行查詢。</p>
    </div>
</section>

<!-- 查詢表單 -->
<section class="container my-5">
    <h3 class="text-center mb-4">📋 查詢供應商</h3>
    <form method="post" class="d-flex justify-content-center mb-4">
        <input type="text" name="search_term" class="form-control w-50 me-2" placeholder="輸入供應商統一編號或名稱" value="<?php echo htmlspecialchars($searchTerm); ?>" required>
        <button type="submit" name="search" class="btn btn-primary">查詢</button>
    </form>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- 查詢結果 -->
    <?php if (isset($_POST['search']) && $search_result && mysqli_num_rows($search_result) > 0): ?>
        <h4 class="text-center">🔍 查詢結果：</h4>
        <table class="table table-bordered table-hover text-center align-middle mt-3">
            <thead class="table-success">
                <tr>
                    <th>供應商統一編號</th>
                    <th>水果供應商名稱</th>
                    <th>電話</th>
                    <th>Email</th>
                    <th>住址</th>
                    <th>負責人姓名</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($search_result)): ?>
                    <tr>
                        <td><?php echo $row['供應商統一編號']; ?></td>
                        <td><?php echo $row['水果供應商名稱']; ?></td>
                        <td><?php echo $row['電話']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td><?php echo $row['住址']; ?></td>
                        <td><?php echo $row['負責人姓名']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- 所有供應商資料 -->
    <h4 class="text-center mt-5">📋 所有供應商資料：</h4>
    <table class="table table-bordered table-hover text-center align-middle mt-3">
        <thead class="table-success">
            <tr>
                <th>供應商統一編號</th>
                <th>水果供應商名稱</th>
                <th>電話</th>
                <th>Email</th>
                <th>住址</th>
                <th>負責人姓名</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($all_result)): ?>
                <tr>
                    <td><?php echo $row['供應商統一編號']; ?></td>
                    <td><?php echo $row['水果供應商名稱']; ?></td>
                    <td><?php echo $row['電話']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['住址']; ?></td>
                    <td><?php echo $row['負責人姓名']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<!-- 頁腳 -->
<?php include_once('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
