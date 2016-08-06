<form method="post" action="" enctype="multipart/form-data">
    <input type="file" name="file[x][y]">
    <input type="submit" value="submit">
</form>
<?php if (isset($_FILES['file']['error']['x']['y']) && is_int($_FILES['file']['error']['x']['y'])): ?>
<?php if ($_FILES['file']['error']['x']['y'] === UPLOAD_ERR_OK): ?>
<div id="success">SUCCESS</div>
<?php else: ?>
<div id="error">ERROR:<?=$_FILES['file']['error']['x']['y']?></div>
<?php endif; ?>
<?php endif; ?>
