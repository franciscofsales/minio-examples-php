<?php
    require __DIR__ . '/vendor/autoload.php';
    use Aws\S3\S3Client;
	if(isset($_FILES['image'])){
		$file_name = $_FILES['image']['name'];
		$temp_file_location = $_FILES['image']['tmp_name'];
        $bucket_name = 'test-bucket';



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

		$result = $s3->putObject([
			'Bucket' => $bucket_name,
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location
		]);

		var_dump($result);
	}
?>


<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
	<input type="file" id="file" name="image" />
	<br/>
	<br/>
	<input type="submit"/>
</form>
