<?php
// https://github.com/minio/cookbook/blob/master/docs/aws-sdk-for-php-with-minio.md

date_default_timezone_set('Europe/Madrid');
require 'vendor/autoload.php';

$messages = [];
$bucket = getenv('S3_BUCKET');

$client = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => getenv('S3_REGION'),
    'endpoint' => getenv('S3_SERVER'),
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key'    => getenv('S3_ACCESS_KEY'),
        'secret' => getenv('S3_SECRET_KEY'),
    ]
]);

if (! $client->doesBucketExist($bucket)) {
    $client->createBucket(['Bucket' => $bucket]);
    array_push($messages, 'Bucket created: ' . $bucket);

    $policyReadOnly = <<<JSON
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": [
        "s3:GetBucketLocation",
        "s3:ListBucket"
      ],
      "Effect": "Allow",
      "Principal": {
        "AWS": [
          "*"
        ]
      },
      "Resource": [
        "arn:aws:s3:::%s"
      ],
      "Sid": ""
    },
    {
      "Action": [
        "s3:GetObject"
      ],
      "Effect": "Allow",
      "Principal": {
        "AWS": [
          "*"
        ]
      },
      "Resource": [
        "arn:aws:s3:::%s/*"
      ],
      "Sid": ""
    }
  ]
}
JSON;

    $client->putBucketPolicy([
        'Bucket' => $bucket,
        'Policy' => sprintf($policyReadOnly, $bucket, $bucket),
    ]);
    array_push($messages, 'Bucket public policy created');
}

// @todo remove demo object creation!
$objectKey = 'hello-world.txt';
if (! $client->doesObjectExist($bucket, $objectKey)) {
    $result = $client->putObject(array(
        'Bucket' => $bucket,
        'Key'    => $objectKey,
        'Body'   => 'Hello World!',
        'ACL'    => 'public-read',
    ));
}

$objects = $client->getIterator('ListObjects', array(
    'Bucket' => $bucket
));

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

      <?php if (! empty($messages)): ?>
        <div class="alert alert-success" role="alert">
          <?php foreach ($messages as $message): ?>
            <?php echo $message; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (! empty($objects)): ?>
        <h2>Objects available</h2>
        <ul class="list-styled">
          <?php foreach ($objects as $object): ?>
            <li><a href="<?php echo getenv('S3_PUBLIC') . '/' . $bucket . '/' . $object['Key']; ?>"><?php echo $object['Key']; ?></a></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </body>
</html>