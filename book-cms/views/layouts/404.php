<?php
ob_start();
?>
<section class="error-section">
  <div class="error-container">
    <div class="error-code">404</div>
    <h1 class="error-title">Page or Book Not Found</h1>
    <p class="error-message">The resource you requested could not be located in the Libris CMS library or repository.</p>
    <div class="error-actions">
      <a href="/" class="btn btn-primary">Return to Catalog</a>
      <a href="/xml/studio" class="btn btn-outline">Go to XML Studio</a>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/main.php';

