<?php
// this path is relative to the ultimate calling file
// hence ../ not ../../
require "../vendor/autoload.php";
use Aws\S3\S3Client;
function upload_file_s3($bucket, $filepath, $filename){
	
	$s3Client = S3Client::factory(
					array(
						'credentials' => array(
							'key'    => 'AKIAIJS3MOJJH4ULUG7A',
							'secret' => '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',
						)
					)
				);
//	echo "Factory initialize done, Try uploading the file.."."\n";
//	echo "params are - bucket: $bucket, filepath: $filepath, filename: $filename", "\n";
	
	// remember that filepath is relative to the location from where utlimately this is called
	// so file paths would be ../images etc

	$result = $s3Client->putObject(array(
		'Bucket'     => $bucket,
		'Key'        => $filename,
		'SourceFile' => $filepath.$filename,
		'ACL'        => 'public-read',
		'Metadata'   => array()
	));

/*
	echo "Upload call done.."."\n";
	$s3Client->waitUntil('ObjectExists', array(
		'Bucket' => $bucket,
		'Key'    => $filename
		)
	);
	echo "Now the file: $filename should be availabe in S3 as well";
*/	
	return 1;
}
?>
