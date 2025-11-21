<?php
require 'config.php';
$stmt = $pdo->query("SELECT * FROM caterer ORDER BY first_name");
$caterers = $stmt->fetchAll();
?>
<html>
<head><title>Caterers</title></head>
<body>
<h1>Caterers</h1>
<a href="caterer_create.php">Add Caterer</a>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Actions</th></tr>
<?php foreach($caterers as $c): ?>
<tr>
<td><?=htmlspecialchars($c['caterer_id'])?></td>
<td><?=htmlspecialchars($c['first_name'].' '.$c['last_name'])?></td>
<td><?=htmlspecialchars($c['phone'])?></td>
<td><?=htmlspecialchars($c['email'])?></td>
<td>
<a href="caterer_edit.php?id=<?=urlencode($c['caterer_id'])?>">Edit</a> |
<a href="caterer_delete.php?id=<?=urlencode($c['caterer_id'])?>" onclick="return confirm('Delete?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
