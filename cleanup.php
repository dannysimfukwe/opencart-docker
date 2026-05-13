<?php
/**
 * OpenCart Post-Install Cleanup
 * Usage: Visit https://your-site.com/cleanup.php in your browser.
 */

$installDir = '/var/www/html/install';
$configFile = '/var/www/html/config.php';
$adminConfigFile = '/var/www/html/admin/config.php';

$configExists = file_exists($configFile) && filesize($configFile) > 0;
$adminConfigExists = file_exists($adminConfigFile) && filesize($adminConfigFile) > 0;

if (!$configExists || !$adminConfigExists) {
    die('Installation not complete yet. Please finish the install wizard first, then revisit this page.');
}

$CONFIRM = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if (!$CONFIRM) {
    clearstatcache();
    $installExists = is_dir($installDir);
    $installStat = $installExists 
        ? '<span style="color:#856404;font-weight:bold">⚠ Present — will be removed</span>'
        : '<span style="color:green;font-weight:bold">✔ Already removed</span>';

    echo '<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>OpenCart Cleanup</title>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;max-width:600px;margin:60px auto;padding:24px;background:#fff3cd;border:2px solid #ffc107;border-radius:12px;color:#333}
h1{color:#856404;margin-top:0} a{color:#004085;text-decoration:underline}
.row{margin-top:12px;line-height:2} table{width:100%;border-collapse:collapse}
td{padding:6px 10px;border-bottom:1px solid #ffeaa7;font-size:14px}
.btn{display:inline-block;font-size:18px;padding:12px 24px;background:#004085;color:#fff;border-radius:6px;text-decoration:none;margin-top:12px}
</style></head><body>
<h1>⚡ OpenCart Post-Install Cleanup</h1>
<table>
<tr><td><strong>config.php</strong></td><td><span style="color:green">✔ Exists</span></td></tr>
<tr><td><strong>admin/config.php</strong></td><td><span style="color:green">✔ Exists</span></td></tr>
<tr><td><strong>Install folder</strong></td><td>' . $installStat . '</td></tr>
</table>
<p>This will delete the <code>/install</code> folder to secure your installation.</p>
<div class="row"><strong>Ready? <a href="?confirm=yes" class="btn">Run Cleanup →</a></strong></div>
</body></html>';
    exit;
}

clearstatcache();

exec("rm -rf " . escapeshellarg($installDir) . " 2>&1", $out, $ret);

clearstatcache();
$stillExists = is_dir($installDir);

$siteUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Cleanup Complete</title>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;max-width:600px;margin:60px auto;padding:24px;background:#d4edda;border:2px solid #28a745;border-radius:12px;color:#155724}
h1{color:#155724}.item{font-size:16px}.ok{color:green}.fail{color:red}a{color:#004085}
</style></head><body>
<h1>✅ Cleanup Complete</h1>
<p>Results:</p>
<p class="item"><span class="<?= $stillExists ? 'fail' : 'ok' ?>"><?= $stillExists ? '✘' : '✔' ?> /install folder <?= $stillExists ? 'FAILED to remove' : 'removed' ?></span></p>
<p style="margin-top:24px"><a href="<?= $siteUrl ?>">← Back to your site</a></p>
</body></html>
<?php
exit;