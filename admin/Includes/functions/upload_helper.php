<?php

/**
 * Global helper for safely uploading images.
 * Validates MIME type, extension, intercepts errors, and generates a unique secure filename.
 * 
 * @param array $fileData The $_FILES['input_name'] array.
 * @param string $tenantId The current tenant ID for unique hashing.
 * @param string $targetDir The directory to move the uploaded file.
 * @param string $dbPrefix The prefix to save in the database path.
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function handleImageUpload($fileData, $tenantId, $targetDir = "../img/uploads/", $dbPrefix = "img/uploads/")
{
    if (!isset($fileData) || $fileData['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error al subir la imagen o no se seleccionó ningún archivo válido.'];
    }

    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            return ['success' => false, 'error' => 'No se pudo crear el directorio de destino.'];
        }
    }

    $fileTmpPath = $fileData['tmp_name'];
    $fileName = $fileData['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Valid extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Valid MIME types using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($fileExtension, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
        return ['success' => false, 'error' => 'El formato del archivo no es válido. Solo se permiten imágenes (JPG, PNG, GIF, WEBP).'];
    }

    // Generate unique name
    $newFileName = md5(time() . uniqid() . $tenantId) . '.' . $fileExtension;
    $destPath = rtrim($targetDir, '/') . '/' . $newFileName;
    $dbPath = rtrim($dbPrefix, '/') . '/' . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        return ['success' => true, 'path' => $dbPath];
    }

    return ['success' => false, 'error' => 'Error al mover el archivo al directorio de destino. Verifica permisos.'];
}
