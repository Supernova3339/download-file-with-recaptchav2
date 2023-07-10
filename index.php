<?php

$siteKey = 'YOUR_SITE_KEY_HERE';
$secretKey = 'YOUR_SECRET_KEY_HERE';

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify reCAPTCHA response
    if (isset($_POST['g-recaptcha-response'])) {
        $recaptchaResponse = $_POST['g-recaptcha-response'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $ip
        );

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseData = json_decode($response, true);

        if ($responseData['success']) {
            // Generate a random password
            $combination = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $generatedPassword = '';
            $combinationLength = strlen($combination) - 1;
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $combinationLength);
                $generatedPassword .= $combination[$n];
            }
            $password = $generatedPassword;

            // Download the zip file
            $zipUrl = 'https://file-examples.com/storage/fede3f30f864a1f979d2bf0/2017/02/zip_10MB.zip';
            $zipFileName = 'file.zip';
            $content = file_get_contents($zipUrl);

            // Save the downloaded file
            file_put_contents($zipFileName, $content);

            // Create a temporary directory to extract the files
            $tempDir = 'temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            // Extract the files
            $zip = new ZipArchive();
            if ($zip->open($zipFileName) === true) {
                $zip->extractTo($tempDir);
                $zip->close();
            } else {
                // Failed to extract the zip file
                $errors[] = "Failed to extract the zip file.";
            }

            // Password protect each extracted file
            $files = glob($tempDir . '*');
            foreach ($files as $file) {
                $zip = new ZipArchive();
                if ($zip->open($file) === true) {
                    $zip->setPassword($password);
                    $zip->close();
                } else {
                    // Failed to password protect the file
                    $errors[] = "Failed to password protect the file: " . $file;
                }
            }

            // Re-zip the files with the provided password
            $newZipFileName = 'file.zip';
            $newZip = new ZipArchive();
            if ($newZip->open($newZipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($files as $file) {
                    $newZip->addFile($file, basename($file));
                }
                $newZip->setPassword($password);
                $newZip->close();

                // Clean up the temporary directory
                array_map('unlink', $files);
                rmdir($tempDir);

                // Send the zip file to the browser for download
                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=" . $newZipFileName);
                header("Content-Length: " . filesize($newZipFileName));
                readfile($newZipFileName);

                // Delete the zip file after it's downloaded
                unlink($newZipFileName);

                // Exit to prevent further output
                exit;
            } else {
                // Failed to create the new zip file
                $errors[] = "Failed to create the new zip file.";
            }
        } else {
            // reCAPTCHA verification failed
            $errors[] = "reCAPTCHA verification failed.";
        }
    } else {
        // reCAPTCHA response not provided
        $errors[] = "reCAPTCHA response not provided.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Download SuperPad</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .center {
            text-align: center;
        }

        .error-details {
            text-align: left;
            margin-top: 1rem;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="center">
        <form action="index.php" method="POST">
            <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
            <button type="submit" class="btn btn-primary btn-lg btn-md" style="width:100%;">Download&nbsp;<i class="fas fa-download"></i></button>
        </form>
        <?php if (!empty($errors)) : ?>
            <div class="error-details mt-4">
                <button class="btn btn-secondary btn-lg btn-md" style="width:100%;" type="button" data-bs-toggle="collapse" data-bs-target="#errorDetails" aria-expanded="false" aria-controls="errorDetails">
                    Show Error Details
                </button>
                <div class="collapse mt-4" id="errorDetails">
                    <div class="card card-body">
                        <h5>Error Details:</h5>
                        <ul>
                            <?php foreach ($errors as $error) : ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
</body>
</html>