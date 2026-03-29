<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Laravel does not natively parse multipart/form-data on PUT/PATCH requests.
 * This middleware manually merges the raw input into the request so that
 * $request->input() works correctly on PUT, exactly like POST.
 */
class PutBodyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['PUT', 'PATCH'], true)) {
            $contentType = $request->header('Content-Type', '');

            // multipart/form-data — PHP/Laravel won't auto-parse this on PUT
            if (str_contains($contentType, 'multipart/form-data')) {
                // $_POST is empty on PUT multipart — parse the raw stream manually
                [$fields, $files] = $this->parseMultipart(
                    file_get_contents('php://input'),
                    $this->extractBoundary($contentType)
                );

                // Merge parsed fields into the request input bag
                $request->merge($fields);

                // Inject parsed files into the request files bag
                if (!empty($files)) {
                    $request->files->add($files);
                }

            } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                parse_str(file_get_contents('php://input'), $data);
                $request->merge($data);

            } elseif (str_contains($contentType, 'application/json')) {
                $json = json_decode(file_get_contents('php://input'), true);
                if (is_array($json)) {
                    $request->merge($json);
                }
            }
        }

        return $next($request);
    }

    private function extractBoundary(string $contentType): ?string
    {
        preg_match('/boundary=(.+)$/i', $contentType, $matches);
        return $matches[1] ?? null;
    }

    private function parseMultipart(string $body, ?string $boundary): array
    {
        $fields = [];
        $files  = [];

        if (!$boundary) {
            return [$fields, $files];
        }

        $parts = explode('--' . $boundary, $body);
        array_shift($parts); // drop preamble
        array_pop($parts);   // drop epilogue (--)

        foreach ($parts as $part) {
            if (!str_contains($part, "\r\n\r\n")) {
                continue;
            }

            [$headerSection, $content] = explode("\r\n\r\n", $part, 2);
            $content = rtrim($content, "\r\n");

            // Parse headers
            $headers = [];
            foreach (explode("\r\n", ltrim($headerSection)) as $line) {
                if (str_contains($line, ':')) {
                    [$key, $val] = explode(':', $line, 2);
                    $headers[strtolower(trim($key))] = trim($val);
                }
            }

            $disposition = $headers['content-disposition'] ?? '';
            preg_match('/name="([^"]+)"/', $disposition, $nameMatch);
            $name = $nameMatch[1] ?? null;
            if ($name === null) {
                continue;
            }

            preg_match('/filename="([^"]*)"/', $disposition, $filenameMatch);
            $filename = $filenameMatch[1] ?? null;

            if ($filename !== null) {
                // File field
                $mimeType = $headers['content-type'] ?? 'application/octet-stream';
                $tmpFile  = tempnam(sys_get_temp_dir(), 'laravel_put_');
                file_put_contents($tmpFile, $content);

                $files[$name] = new \Illuminate\Http\UploadedFile(
                    $tmpFile,
                    $filename,
                    $mimeType,
                    $filename === '' ? UPLOAD_ERR_NO_FILE : UPLOAD_ERR_OK,
                    true // test mode — allows moving temp files not from actual upload
                );
            } else {
                // Regular field
                $fields[$name] = $content;
            }
        }

        return [$fields, $files];
    }
}