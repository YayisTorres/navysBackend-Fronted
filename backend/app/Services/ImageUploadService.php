<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Storage;

class ImageUploadService
{
    protected $storage;
    protected $firebaseStorageBucket;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;

        // 🔥 INICIO DEL CÓDIGO DE DIAGNÓSTICO 🔥
        try {
            // Intentamos obtener el bucket de Firebase.
            // Si esto falla, las credenciales o la configuración son incorrectas.
            $this->firebaseStorageBucket = $storage->getBucket();

            // Si llegamos aquí, ¡todo bien!
            Log::info('✅ ¡ÉXITO! Credenciales de Firebase cargadas y bucket de Storage verificado correctamente.');

        } catch (Exception $e) {
            // Si algo falla, lo registramos en los logs con todo el detalle.
            Log::error('❌ ¡ERROR! No se pudieron verificar las credenciales de Firebase o el bucket de Storage.');
            Log::error('Detalle del error de Firebase: ' . $e->getMessage());

            // Es importante relanzar la excepción para que la aplicación falle y no continúe
            // con un servicio que no funciona.
            throw $e;
        }
        // 🔥 FIN DEL CÓDIGO DE DIAGNÓSTICO 🔥
    }

    /**
     * Sube un archivo, intentando primero localmente y luego a Firebase como fallback.
     *
     * @param UploadedFile $file El archivo a subir.
     * @param string $basePath La ruta base donde guardar (ej. 'img/imgProducts').
     * @param string|null $customFilename Nombre personalizado para el archivo.
     * @return string La URL o ruta del archivo guardado.
     */
    public function upload(UploadedFile $file, string $basePath, ?string $customFilename = null): string
    {
        $filename = $customFilename ?: (time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension());

        // --- Intento 1: Guardar en el sistema de archivos local ---
        try {
            $publicPath = public_path($basePath);

            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true, true);
            }

            if (!is_writable($publicPath)) {
                throw new Exception("El directorio local no tiene permisos de escritura: " . $publicPath);
            }

            $file->move($publicPath, $filename);
            $localPath = $basePath . '/' . $filename;

            Log::info("✅ Imagen guardada localmente: " . $localPath);
            return $localPath;

        } catch (Exception $e) {
            Log::warning("⚠️ Fallo al guardar localmente. Intentando con Firebase Storage. Error: " . $e->getMessage());

            // --- Intento 2: Fallback a Firebase Storage ---
            try {
                $firebasePath = $basePath . '/' . $filename;

                $this->firebaseStorageBucket->upload(
                    file_get_contents($file->getRealPath()),
                    ['name' => $firebasePath]
                );

                $url = $this->getPublicUrl($firebasePath);
                Log::info("🔥 Imagen guardada en Firebase: " . $url);
                return $url;

            } catch (Exception $firebaseError) {
                Log::error("❌ Error al subir a Firebase: " . $firebaseError->getMessage());
                throw new Exception("No se pudo guardar el archivo ni localmente ni en Firebase. Local: " . $e->getMessage() . " | Firebase: " . $firebaseError->getMessage());
            }
        }
    }

    /**
     * Elimina un archivo, detectando si es una URL de Firebase o una ruta local.
     *
     * @param string|null $pathOrUrl La ruta local o URL de Firebase.
     * @return bool
     */
    public function delete(?string $pathOrUrl): bool
    {
        if (empty($pathOrUrl)) {
            return false;
        }

        try {
            if (strpos($pathOrUrl, 'storage.googleapis.com') !== false || strpos($pathOrUrl, 'firebasestorage.googleapis.com') !== false) {
                $objectPath = $this->getObjectPathFromUrl($pathOrUrl);
                $object = $this->firebaseStorageBucket->object($objectPath);
                if ($object->exists()) {
                    $object->delete();
                    Log::info("🔥 Imagen eliminada de Firebase: " . $pathOrUrl);
                }
            } else {
                $localPath = public_path($pathOrUrl);
                if (File::exists($localPath)) {
                    File::delete($localPath);
                    Log::info("✅ Imagen eliminada localmente: " . $localPath);
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error("❌ Error al eliminar imagen '{$pathOrUrl}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la URL pública de un objeto en Firebase Storage.
     */
    private function getPublicUrl(string $path): string
    {
        $object = $this->firebaseStorageBucket->object($path);
        // La URL expira en 1 año. Puedes ajustarlo según tus necesidades.
        $expiration = new \DateTime('+1 year');
        return $object->signedUrl($expiration);
    }

    /**
     * Extrae la ruta del objeto desde una URL de Firebase.
     */
    private function getObjectPathFromUrl(string $url): string
    {
        // Parsear la URL para extraer el path del objeto
        $parsedUrl = parse_url($url);
        $path = ltrim($parsedUrl['path'], '/');

        // Para URLs de Firebase Storage, el formato suele ser:
        // https://storage.googleapis.com/bucket-name/path/to/file
        // o https://firebasestorage.googleapis.com/v0/b/bucket-name/o/path%2Fto%2Ffile

        $bucketName = $this->firebaseStorageBucket->name();

        if (strpos($url, 'firebasestorage.googleapis.com') !== false) {
            // Formato Firebase Storage API
            preg_match('/\/o\/(.+?)(\?|$)/', $url, $matches);
            return isset($matches[1]) ? urldecode($matches[1]) : $path;
        } else {
            // Formato Google Cloud Storage
            $prefix = $bucketName . '/';
            if (strpos($path, $prefix) === 0) {
                return substr($path, strlen($prefix));
            }
        }

        return $path;
    }
}
