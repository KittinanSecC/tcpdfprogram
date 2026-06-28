<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>อัปโหลดภาพ</title>
<style>
body { font-family: Arial; text-align: center; margin-top: 50px; }
</style>
</head>
<body>

<h2>อัปโหลดภาพเพื่อจัดลง A4</h2>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple accept="image/*" required>
    <br><br>
    <button type="submit">อัปโหลดและจัดภาพ</button>
</form>

</body>
</html>