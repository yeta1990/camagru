<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View user</title>
</head>
<body>
    <h1>Edited User</h1>

    Id:<?php echo htmlspecialchars($user["id"]); ?><br>
    Username: <?php echo htmlspecialchars($user["username"]); ?><br>
    Email:<?php echo htmlspecialchars($user["email"]); ?>

</body>
</html>