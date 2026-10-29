@extends('layouts.app')

@section('title', 'XML Studio & Schema Validator - LibrisCMS')

@section('content')
<div class="admin-xml-studio-page">
    <div class="section-container">
        <div class="admin-header-flex">
            <div>
                <span class="badge badge-admin">XML Interchange</span>
                <h1 class="admin-title">Laravel XML Engineering Studio</h1>
                <p class="admin-subtitle">Validate, import, and syndicate catalog data using DOMDocument and Schema.</p>
            </div>
            <div class="admin-actions-bar">
                <a href="{{ route('xml.export') }}" class="btn btn-primary">📥 Export Catalog XML</a>
                <a href="{{ route('xml.rss') }}" target="_blank" class="btn btn-secondary">📡 View RSS Feed</a>
                <a href="{{ route('xml.sitemap') }}" target="_blank" class="btn btn-outline">🗺️ View Sitemap</a>
            </div>
        </div>

        @if($errors->has('xml'))
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <strong>XML Validation Error:</strong> {{ $errors->first('xml') }}
            </div>
        @endif

        <div class="validator-layout">
            <div class="form-container-card">
                <h3>Import Books via XML</h3>
                <p class="text-muted">Upload or paste an XML file containing <code>&lt;catalog&gt;</code> and <code>&lt;book&gt;</code> elements.</p>

                <form action="{{ route('admin.xml.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="xml_content">Paste XML Code:</label>
                        <textarea name="xml_content" id="xml_content" rows="12" class="form-textarea code-font" placeholder="<catalog>&#10;  <book>&#10;    <title>Sample</title>...&#10;  </book>&#10;</catalog>"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="xml_file">Or Upload XML File:</label>
                        <input type="file" name="xml_file" id="xml_file" accept=".xml,text/xml" class="form-file">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Validate &amp; Import to Database</button>
                    </div>
                </form>
            </div>

            <div class="form-container-card">
                <h3>XML Specifications Supported</h3>
                <ul class="specs-list">
                    <li><strong>DOMDocument:</strong> Full tree-based parsing and XML document generation.</li>
                    <li><strong>XSD Validation:</strong> Structural grammar verification against strict types.</li>
                    <li><strong>RSS 2.0:</strong> Syndicated feeds for feed readers and external distributors.</li>
                    <li><strong>Sitemap Protocol 0.9:</strong> Search engine crawler optimization.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
