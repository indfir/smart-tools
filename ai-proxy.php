<?php
/**
 * Server-side AI proxy for PDF Tools.
 *
 * Upload this file to the same folder as index.html.
 *
 * Example:
 * public_html/smarttools/index.html
 * public_html/smarttools/ai-proxy.php
 */

$allowed = [
    "https://coding-intl.dashscope.aliyuncs.com",
    "https://dashscope-intl.aliyuncs.com",
    "https://api.anthropic.com",
    "https://api.openai.com",
    "https://openrouter.ai",
    "https://generativelanguage.googleapis.com",
    "https://api.groq.com",
];

header("X-Content-Type-Options: nosniff");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, x-api-key, anthropic-version, anthropic-dangerous-direct-browser-access, x-upstream-origin, x-proxy-authorization");

function fail(int $code, string $message): void {
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode([
        "error" => [
            "message" => $message
        ]
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    header("Content-Type: application/json");
    echo json_encode([
        "ok" => true,
        "message" => "ai-proxy.php is reachable"
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    fail(405, "POST only.");
}

$origin = rtrim(trim($_SERVER["HTTP_X_UPSTREAM_ORIGIN"] ?? ""), "/");

if (!in_array($origin, $allowed, true)) {
    fail(400, "Upstream not allowed by ai-proxy.php: " . $origin);
}

/**
 * Supports both formats:
 *
 * ai-proxy.php?path=/v1/chat/completions
 * ai-proxy.php/v1/chat/completions
 */
$path = $_GET["path"] ?? "";

if ($path === "") {
    $requestUri = $_SERVER["REQUEST_URI"] ?? "";
    $scriptName = $_SERVER["SCRIPT_NAME"] ?? "";

    $pos = strpos($requestUri, basename($scriptName));

    if ($pos !== false) {
        $path = substr($requestUri, $pos + strlen(basename($scriptName)));
    }

    $qPos = strpos($path, "?");

    if ($qPos !== false) {
        $path = substr($path, 0, $qPos);
    }
}

if ($path === "") {
    fail(400, "Missing path.");
}

if ($path[0] !== "/") {
    $path = "/" . $path;
}

if (!preg_match('#^/[A-Za-z0-9/._~:-]{1,500}$#', $path)) {
    fail(400, "Bad path: " . $path);
}

$auth = $_SERVER["HTTP_AUTHORIZATION"] ?? $_SERVER["HTTP_X_PROXY_AUTHORIZATION"] ?? "";

$headers = [
    "Content-Type: application/json"
];

if ($auth !== "") {
    $headers[] = "Authorization: " . $auth;
}

$forwardHeaders = [
    "HTTP_X_API_KEY" => "x-api-key",
    "HTTP_ANTHROPIC_VERSION" => "anthropic-version",
    "HTTP_ANTHROPIC_DANGEROUS_DIRECT_BROWSER_ACCESS" => "anthropic-dangerous-direct-browser-access",
];

foreach ($forwardHeaders as $serverKey => $headerName) {
    if (!empty($_SERVER[$serverKey])) {
        $headers[] = $headerName . ": " . $_SERVER[$serverKey];
    }
}

$payload = file_get_contents("php://input");

function post_upstream(string $url, array $headers, string $payload): array {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array_merge($headers, ["Accept: application/json"]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_FOLLOWLOCATION => false,
        // Many API gateways reject requests without a User-Agent (PHP cURL sends none by default)
        CURLOPT_USERAGENT => "pdf-tools-proxy/1.0 (+smarttools.indfir.com)",
    ]);

    $body = curl_exec($ch);

    if ($body === false) {
        $error = curl_error($ch);
        curl_close($ch);
        fail(502, "Proxy could not reach the provider: " . $error);
    }

    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: "application/json";
    curl_close($ch);

    return [
        "status" => $status,
        "content_type" => $contentType,
        "body" => $body,
        "url" => $url,
    ];
}

$candidates = [$origin . $path];

if ($origin === "https://coding-intl.dashscope.aliyuncs.com" && $path === "/v1/chat/completions") {
    $candidates[] = $origin . "/chat/completions";
    $candidates[] = $origin . "/compatible-mode/v1/chat/completions";
}

$result = null;
$tried = [];

foreach ($candidates as $url) {
    $result = post_upstream($url, $headers, $payload);
    $tried[] = $url . " => HTTP " . $result["status"];

    if (!in_array($result["status"], [404, 405], true)) {
        break;
    }
}

if ($result !== null && in_array($result["status"], [404, 405], true) && count($candidates) > 1) {
    http_response_code($result["status"]);
    header("Content-Type: application/json");
    echo json_encode([
        "error" => [
            "message" => "Provider endpoint rejected the request. Tried: " . implode("; ", $tried)
        ]
    ]);
    exit;
}

http_response_code($result["status"] ?: 200);
header("Content-Type: " . $result["content_type"]);

echo $result["body"];
