<?php
use Libris\Services\Auth;
ob_start();
?>
<section class="xml-studio-section">
  <div class="section-container">
    <div class="studio-header">
      <div>
        <span class="badge badge-category">Practical Topics: XML, XSD, XSLT, DOM &amp; SimpleXML</span>
        <h1 class="admin-title">XML Engineering &amp; Validation Studio</h1>
        <p class="admin-subtitle">Interactive playground to inspect, validate, transform, and exchange book data.</p>
      </div>
      <div class="studio-quick-actions">
        <a href="/xml/export" class="btn btn-primary">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export Master XML
        </a>
        <a href="/xml/schema.xsd" target="_blank" class="btn btn-outline">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          View XSD Schema
        </a>
      </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="studio-tabs" role="tablist">
      <button class="studio-tab-btn active" data-tab="tab-validator" role="tab" aria-selected="true">
        🛠️ XML Validator &amp; DB Import
      </button>
      <button class="studio-tab-btn" data-tab="tab-xslt" role="tab" aria-selected="false">
        🎨 Live XSLT Transformation
      </button>
      <button class="studio-tab-btn" data-tab="tab-catalog" role="tab" aria-selected="false">
        📄 Master Catalog (catalog.xml)
      </button>
      <button class="studio-tab-btn" data-tab="tab-schema" role="tab" aria-selected="false">
        📐 XML Schema (schema.xsd)
      </button>
    </div>

    <!-- Tab 1: XML Validator & Import Tool -->
    <div id="tab-validator" class="studio-tab-pane active" role="tabpanel">
      <div class="validator-layout">
        <div class="validator-form-card">
          <div class="card-header-flex">
            <h3 class="card-heading">XML Input &amp; Test Payloads</h3>
            <div class="sample-loaders">
              <button type="button" class="btn btn-xs btn-outline" onclick="loadSampleXml('valid')">Load Valid Sample</button>
              <button type="button" class="btn btn-xs btn-outline text-danger" onclick="loadSampleXml('invalid')">Load Invalid Sample</button>
            </div>
          </div>

          <form action="/xml/import" method="POST" enctype="multipart/form-data" id="xml-import-form">
            <?= Auth::csrfField() ?>

            <div class="form-group">
              <label for="xml-text-editor" class="form-label">XML Markup (Paste or edit below):</label>
              <textarea 
                id="xml-text-editor" 
                name="xml_text" 
                rows="14" 
                class="form-textarea code-font" 
                placeholder="Paste XML markup conforming to Libris schema.xsd..."
              ><?= htmlspecialchars($sampleImport) ?></textarea>
            </div>

            <div class="form-group file-upload-box">
              <label for="xml-file-input" class="form-label">Or Upload an XML File (.xml):</label>
              <input type="file" id="xml-file-input" name="xml_file" accept=".xml,text/xml" class="form-file">
            </div>

            <div class="validator-btn-toolbar">
              <button type="button" class="btn btn-secondary" id="btn-validate-ajax" onclick="validateXmlLive()">
                ⚡ Test Validate with XSD (AJAX)
              </button>
              <button type="submit" class="btn btn-primary">
                📥 Validate &amp; Import to Database
              </button>
            </div>
          </form>
        </div>

        <!-- Live Validation Results Sidebar -->
        <div class="validator-result-card">
          <h3 class="card-heading">XSD Schema Verification Diagnostics</h3>
          <p class="result-sub">Tests schema constraints (element sequence, regex pattern, data types).</p>

          <div id="validation-feedback-box" class="feedback-box">
            <?php if ($validationResult['valid']): ?>
              <div class="status-indicator valid">
                <div class="status-icon">✓</div>
                <div>
                  <strong>Master Catalog Status: Valid</strong>
                  <p class="status-detail">Conforms strictly to <code>schema.xsd</code> without errors.</p>
                </div>
              </div>
            <?php else: ?>
              <div class="status-indicator invalid">
                <div class="status-icon">✗</div>
                <div>
                  <strong>Validation Errors Detected</strong>
                  <ul class="error-list">
                    <?php foreach ($validationResult['errors'] as $err): ?>
                      <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="xsd-rules-recap">
            <h4 class="rules-heading">XSD Constraints Applied:</h4>
            <ul class="rules-list">
              <li><strong>Root:</strong> <code>&lt;bookCatalog&gt;</code> contains <code>&lt;metadata&gt;</code> and <code>&lt;book&gt;</code> items</li>
              <li><strong>ISBN Pattern:</strong> Validates ISBN-10 / ISBN-13 regex format</li>
              <li><strong>Published Year:</strong> <code>xs:minInclusive value="1800"</code> to <code>xs:maxInclusive value="2099"</code></li>
              <li><strong>Rating:</strong> Range <code>0.0</code> to <code>5.0</code> decimal</li>
              <li><strong>Mandatory:</strong> <code>&lt;title&gt;</code>, <code>&lt;isbn&gt;</code>, <code>&lt;author&gt;</code>, <code>&lt;category&gt;</code>, <code>&lt;summary&gt;</code></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 2: Live XSLT Transformation -->
    <div id="tab-xslt" class="studio-tab-pane" role="tabpanel">
      <div class="xslt-card-wrapper">
        <div class="xslt-toolbar">
          <p class="xslt-info-txt">
            <strong>PHP XSLTProcessor Engine:</strong> Transforms <code>xml/catalog.xml</code> using <code>xml/transform.xsl</code> directly into styled HTML.
          </p>
        </div>
        <div class="xslt-output-container">
          <?= $xsltHtml ?>
        </div>
      </div>
    </div>

    <!-- Tab 3: Master Catalog XML -->
    <div id="tab-catalog" class="studio-tab-pane" role="tabpanel">
      <div class="code-view-card">
        <div class="code-view-header">
          <span class="file-name">xml/catalog.xml</span>
          <button class="btn btn-xs btn-outline" onclick="copyCode('catalog-code-block')">Copy XML</button>
        </div>
        <pre class="code-display"><code id="catalog-code-block"><?= htmlspecialchars($xmlCatalog) ?></code></pre>
      </div>
    </div>

    <!-- Tab 4: XML Schema (schema.xsd) -->
    <div id="tab-schema" class="studio-tab-pane" role="tabpanel">
      <div class="code-view-card">
        <div class="code-view-header">
          <span class="file-name">xml/schema.xsd</span>
          <button class="btn btn-xs btn-outline" onclick="copyCode('schema-code-block')">Copy Schema</button>
        </div>
        <pre class="code-display"><code id="schema-code-block"><?= htmlspecialchars($xsdSchema) ?></code></pre>
      </div>
    </div>
  </div>
</section>

<!-- Samples hidden data store for JS -->
<div id="raw-valid-sample" style="display:none;"><?= htmlspecialchars($sampleImport) ?></div>
<div id="raw-invalid-sample" style="display:none;"><?= htmlspecialchars($sampleInvalid) ?></div>

<script>
// Studio Tab Switching
document.querySelectorAll('.studio-tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.studio-tab-btn').forEach(b => {
      b.classList.remove('active');
      b.setAttribute('aria-selected', 'false');
    });
    document.querySelectorAll('.studio-tab-pane').forEach(p => p.classList.remove('active'));

    btn.classList.add('active');
    btn.setAttribute('aria-selected', 'true');
    const target = document.getElementById(btn.getAttribute('data-tab'));
    if (target) target.classList.add('active');
  });
});

function loadSampleXml(type) {
  const editor = document.getElementById('xml-text-editor');
  if (type === 'valid') {
    editor.value = document.getElementById('raw-valid-sample').textContent;
  } else {
    editor.value = document.getElementById('raw-invalid-sample').textContent;
  }
}

async function validateXmlLive() {
  const xml = document.getElementById('xml-text-editor').value;
  const feedback = document.getElementById('validation-feedback-box');
  feedback.innerHTML = '<div class="loading-spinner"></div> <p>Validating against XSD Schema via REST API...</p>';

  try {
    const res = await fetch('/api/validate-xml', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ xml })
    });
    const data = await res.json();

    if (data.valid) {
      feedback.innerHTML = `
        <div class="status-indicator valid">
          <div class="status-icon">✓</div>
          <div>
            <strong>Validation Passed!</strong>
            <p class="status-detail">This XML markup conforms perfectly to <code>schema.xsd</code>. Ready for database import.</p>
          </div>
        </div>
      `;
    } else {
      let errHtml = '<ul class="error-list">';
      data.errors.forEach(e => { errHtml += '<li>' + escapeHtml(e) + '</li>'; });
      errHtml += '</ul>';

      feedback.innerHTML = `
        <div class="status-indicator invalid">
          <div class="status-icon">✗</div>
          <div>
            <strong>Validation Failed (${data.errors.length} error${data.errors.length === 1 ? '' : 's'})</strong>
            ${errHtml}
          </div>
        </div>
      `;
    }
  } catch (err) {
    feedback.innerHTML = '<div class="status-indicator invalid"><p>Network error: ' + err.message + '</p></div>';
  }
}

function copyCode(id) {
  const text = document.getElementById(id).textContent;
  navigator.clipboard.writeText(text).then(() => {
    alert('Code copied to clipboard!');
  });
}

function escapeHtml(str) {
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

