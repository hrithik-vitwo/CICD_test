<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Test</title>
</head>
<body>
    <?php
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    ?>
    <form action="" method="post">
        <input type="text" name="name" placeholder="Name"><br><br>
        <input type="text" name="email" placeholder="Email"><br><br>
        <input type="date" name="dateOfBirth" min="2000-01-01" placeholder="dd-mm-yyyy"><br><br>
        <input type="submit" value="Submit" name="submit">
    </form>
</body>
</html>