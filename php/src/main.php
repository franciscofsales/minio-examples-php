<?php
// Include the SDK using the composer autoloader
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;
use Aws\Exception\AwsException;

$bucket_name = 'test-bucket';

// --- initialize client

$s3 = new S3Client([
    'version' => 'latest',
	'region'  => 'eu-west-1',
    'endpoint' => 'http://localhost:9000',
    'use_path_style_endpoint' => true,
	'credentials' => [
	    'key'    => 'JEwmfuoT4Qe0nOHe',
	    'secret' => 'kb4e97cEW9dCgznWEx77PxYmOsvtj6Nw',
	]
]);


// ---- create bucket

// function createBucket($s3Client, $bucketName)
// {
//     try {
//         $result = $s3Client->createBucket([
//             'Bucket' => $bucketName,
//         ]);
//         return 'The bucket\'s location is: ' .
//             $result['Location'] . '. ' .
//             'The bucket\'s effective URI is: ' .
//             $result['@metadata']['effectiveUri'];
//     } catch (AwsException $e) {
//         return 'Error: ' . $e->getAwsErrorMessage();
//     }
// }

// echo createBucket($s3, $bucket_name);

//  ---- list buckets

$buckets = $s3->listBuckets();
echo '---LIST BUCKETS---'.PHP_EOL;
foreach ($buckets['Buckets'] as $bucket) {
    echo 'Bucket Name:  '.$bucket['Name'].PHP_EOL;
}

echo PHP_EOL;

// ---- send a file
$key = 'test';

$result = $s3->putObject([
	'Bucket' => $bucket_name,
	'Key'    => $key.'_created.txt',
	'Body'   => 'this is the body!',
]);

//  ---- upload a file

$result = $s3->putObject([
	'Bucket' => $bucket_name,
	'Key'    => $key.'_uploaded.txt',
    'SourceFile' => realpath(dirname(__FILE__)).'/sample.txt'

]);

// ---- list files in bucket

echo '---LIST FILES---'.PHP_EOL;

$contents = $s3->listObjects([
    'Bucket' => $bucket_name,
]);

foreach ($contents['Contents'] as $content) {
    echo $content['Key'] . PHP_EOL;
}

echo PHP_EOL.'---READ FILE---'.PHP_EOL;
//  ---- read a file from bucket

$file = $s3->getObject([
    'Bucket' => $bucket_name,
    'Key'    => $key.'_uploaded.txt',
    'SaveAs' => $key.'_local'
]);

// Print the body of the result

echo  $key.'_uploaded.txt:    '.$file['Body'].PHP_EOL.PHP_EOL;


// ------ pre-signed url for reading private files

$cmd = $s3->getCommand('GetObject', [
    'Bucket' => $bucket_name,
    'Key' => $key.'_uploaded.txt'
]);

// get signed url valid for 20 minutes
$request = $s3->createPresignedRequest($cmd, '+20 minutes');
$presignedUrl = (string)$request->getUri();

echo '---SIGNED URL---'.PHP_EOL;

echo $presignedUrl.PHP_EOL.PHP_EOL;


// ---- pre-signed url for users to upload directly to bucket

echo '---PRESIGNED UPLOAD---'.PHP_EOL;

$formInputs = ['acl' => 'private'];
$options = [
    ['acl' => 'private'],
    ['bucket' => $bucket_name],
    ['starts-with', '$key', 'user/fsales/'],
];

// Optional: configure expiration time string
$expires = '+2 hours';

$postObject = new PostObjectV4(
    $s3,
    $bucket,
    $formInputs,
    $options,
    $expires
);


echo PHP_EOL.PHP_EOL;

$formAttributes = $postObject->getFormAttributes();
var_dump($formAttributes);

echo PHP_EOL.PHP_EOL;

$formInputs = $postObject->getFormInputs();
var_dump($formInputs);


?>
