<?php
// https://github.com/minio/cookbook/blob/master/docs/aws-sdk-for-php-with-minio.md

date_default_timezone_set('Europe/Madrid');
require 'vendor/autoload.php';

$messages = [];
$bucket = getenv('S3_BUCKET');

$client = new Aws\S3\S3Client([
  'version' => 'latest',
  'region' => getenv('S3_REGION'),
  'endpoint' => getenv('S3_SERVER'),
  'use_path_style_endpoint' => true,
  'credentials' => [
    'key' => getenv('S3_ACCESS_KEY'),
    'secret' => getenv('S3_SECRET_KEY'),
  ]
]);

if (! $client->doesBucketExist($bucket)) {
  $client->createBucket(['Bucket' => $bucket]);
  array_push($messages, ['Bucket created: ' . $bucket, 'success']);

  $policy = file_get_contents('bucket-public-policy.json');
  $policy = sprintf($policy, $bucket, $bucket);
  $client->putBucketPolicy([
    'Bucket' => $bucket,
    'Policy' => $policy,
  ]);
  array_push($messages, ['Bucket public policy created', 'success']);
}

// Upload a new file when a POST is received
if (isset($_FILES['newfile'])) {
  $objectKey = $_FILES['newfile']['name'];

  if (!$client->doesObjectExist($bucket, $objectKey)) {
    $response = $client->putObject([
      'Bucket' => $bucket,
      'Key' => $objectKey,
      'SourceFile' => $_FILES['newfile']['tmp_name'],
      'ACL' => 'public-read',
      'ContentType' => $_FILES['newfile']['type'],
    ]);

    $client->waitUntil('ObjectExists', [
      'Bucket' => $bucket,
      'Key' => $objectKey,
    ]);

    array_push($messages, ['File uploaded!', 'success']);
  } else {
    array_push($messages, ["Key {$objectKey} already in use!", 'danger']);
  }
}

$objects = $client->getIterator('ListObjects', [
  'Bucket' => $bucket
]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <h1>Minio playground</h1>

    <?php foreach ($messages as $message): ?>
      <div class="alert alert-<?php echo $message[1]; ?>">
        <?php echo $message[0]; ?>
      </div>
    <?php endforeach; ?>

    <h2>Upload a new file</h2>
    <form action="" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <input type="file" class="form-control-file" name="newfile" onchange="this.form.submit()" />
      </div>
    </form>

    <h2>Objects available</h2>
    <?php if (! empty($objects)): ?>
      <ul class="list-styled">
        <?php foreach ($objects as $object):
          $objectPublicUrl = getenv('S3_PUBLIC') . '/' . $bucket . '/' . $object['Key'];
        ?>
          <li><a href="<?php echo $objectPublicUrl; ?>"><?php echo $object['Key']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</body>
</html>
