<?php
namespace Libris\Controllers;

use Libris\Services\XmlService;
use Libris\Services\Auth;
use Libris\Models\Book;

class XmlController {
    public function studio(): void {
        $xmlCatalog = file_get_contents(__DIR__ . '/../../xml/catalog.xml');
        $xsdSchema = file_get_contents(__DIR__ . '/../../xml/schema.xsd');
        $xsltTransform = file_get_contents(__DIR__ . '/../../xml/transform.xsl');
        $sampleImport = file_get_contents(__DIR__ . '/../../xml/sample_import.xml');
        $sampleInvalid = file_get_contents(__DIR__ . '/../../xml/sample_invalid.xml');

        // Initial validation check
        $validationResult = XmlService::validateXml($xmlCatalog);
        $xsltHtml = XmlService::transformWithXslt($xmlCatalog);

        $pageTitle = "XML Engineering Studio - LibrisCMS";
        require __DIR__ . '/../../views/admin/xml-tools.php';
    }

    public function export(): void {
        $xml = XmlService::exportCatalogToXml();
        
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="libris_catalog_' . date('Y-m-d') . '.xml"');
        echo $xml;
        exit;
    }

    public function downloadXsd(): void {
        $path = __DIR__ . '/../../xml/schema.xsd';
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="libris_schema.xsd"');
        readfile($path);
        exit;
    }

    public function import(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /xml/studio");
            exit;
        }

        if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('danger', 'CSRF verification failed.');
            header("Location: /xml/studio");
            exit;
        }

        $xmlContent = '';

        // Check if file was uploaded
        if (isset($_FILES['xml_file']) && $_FILES['xml_file']['error'] === UPLOAD_ERR_OK) {
            $xmlContent = file_get_contents($_FILES['xml_file']['tmp_name']);
        } elseif (!empty($_POST['xml_text'])) {
            $xmlContent = trim($_POST['xml_text']);
        }

        if (empty($xmlContent)) {
            Auth::setFlash('danger', 'Please upload an XML file or paste XML markup.');
            header("Location: /xml/studio");
            exit;
        }

        $result = XmlService::importFromXml($xmlContent);

        if ($result['success']) {
            Auth::setFlash('success', "Success! Validated against XSD and imported {$result['imported_count']} book(s) into database.");
        } else {
            $msg = "XML Validation / Import Failed:<br>" . implode("<br>• ", array_map('htmlspecialchars', $result['errors']));
            Auth::setFlash('danger', $msg);
        }

        header("Location: /xml/studio");
        exit;
    }

    public function rss(): void {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $baseUrl = "{$protocol}://{$host}";

        $rss = XmlService::generateRssFeed($baseUrl);
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $rss;
        exit;
    }

    public function sitemap(): void {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $baseUrl = "{$protocol}://{$host}";

        $sitemap = XmlService::generateSitemap($baseUrl);
        header('Content-Type: application/xml; charset=utf-8');
        echo $sitemap;
        exit;
    }
}

