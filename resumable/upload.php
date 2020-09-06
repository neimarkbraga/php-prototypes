<?php
require_once '../vendor/autoload.php';

function ensureDirectory ($pathname) {
  if (!file_exists($pathname)) {
    mkdir($pathname);
  }
}

use Dilab\Network\SimpleRequest;
use Dilab\Network\SimpleResponse;
use Dilab\Resumable;

ensureDirectory('tmps');
ensureDirectory('uploads');

$request = new SimpleRequest();
$response = new SimpleResponse();

$resumable = new Resumable($request, $response);
$resumable->tempFolder = 'tmps';
$resumable->uploadFolder = 'uploads';
$resumable->process();

if ($resumable->isUploadComplete()) {
  $extension = $resumable->getExtension();
  $filename = $resumable->getFilename();

  echo 'Upload Success! <br />';
  echo 'extension: ' . $extension . '<br />';
  echo 'filename: ' . $filename . '<br />';
}