<?php

namespace App\Helpers;

class Storage
{
    protected static $disk = 'public'; 

    /**
     * Obtiene el camino completo del archivo en el disco.
     */
    protected static function path($filePath)
    {
        return __DIR__ . '/../../storage/' . self::$disk . '/' . $filePath;
    }

    /**
     * Devuelve la ruta pública del archivo.
     */
    public static function storagePath($filePath)
    {
        return __DIR__ . '/../../public/' . $filePath;
    }

    protected static function nopath($filePath)
    {
        return __DIR__ . '/../../' . self::$disk . '/' . $filePath;
    }

    /**
     * Guarda un archivo en el disco.
     */
    public static function put($filePath, $content)
    {
        $fullPath = self::path($filePath);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return file_put_contents($fullPath, $content);
    }

    /**
     * Obtiene el contenido de un archivo.
     */
    public static function get($filePath)
    {
        $fullPath = self::path($filePath);

        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }

        return false;
    }

    /**
     * Verifica si un archivo o directorio existe.
     */
    public static function exists($filePath)
    {
        return file_exists(self::path($filePath));
    }

    /**
     * Elimina un archivo.
     */
    public static function delete($filePath)
    {
        $fullPath = self::path($filePath);

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Crea un directorio.
     */
    public static function makeDirectory($directoryPath)
    {
        $fullPath = self::path($directoryPath);

        if (!is_dir($fullPath)) {
            return mkdir($fullPath, 0777, true);
        }

        return false;
    }

     /**
     * Guarda un archivo en el disco con un nombre específico.
     */
    public static function storeAs($directoryPath, $fileName, $content, $no_path = false)
    {
        $fullPath = $no_path == false ? self::nopath($directoryPath . '/' . $fileName) : self::path($directoryPath . '/' . $fileName);

        // Crea el directorio si no existe
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Guarda el archivo
        return file_put_contents($fullPath, $content);
    }
    public static function handleAttachments($files, $directoryPath = 'attachments')
    {
        $attachments = [];

        if (is_array($files['name'])) {
            foreach ($files['name'] as $key => $originalName) {
                $tmpName = $files['tmp_name'][$key];
                $attachments[] = self::processFile($tmpName, $originalName, $directoryPath);
            }
        } else {
            $tmpName = $files['tmp_name'];
            $originalName = $files['name'];
            $attachments[] = self::processFile($tmpName, $originalName, $directoryPath);
        }

        return $attachments;
    }

    private static function processFile($tmpName, $originalName, $directoryPath)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $fileContent = file_get_contents($tmpName);
        
        Storage::storeAs($directoryPath, $fileName, $fileContent);

        return Storage::storagePath($directoryPath . '/' . $fileName);
    }
}
