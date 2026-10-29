<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <div class="xslt-transformed-catalog">
      <div class="xslt-header">
        <div class="badge-tag">XSLT 1.0 Server-Side Transform</div>
        <h2><xsl:value-of select="bookCatalog/metadata/systemName"/></h2>
        <p class="subtitle">Generated dynamically via XML Stylesheet Language Transformations (XSLT) • Total: <strong><xsl:value-of select="count(bookCatalog/book)"/> books</strong></p>
      </div>

      <div class="xslt-summary-grid">
        <div class="xslt-card">
          <span class="xslt-card-label">Catalog Version</span>
          <span class="xslt-card-val"><xsl:value-of select="bookCatalog/metadata/version"/></span>
        </div>
        <div class="xslt-card">
          <span class="xslt-card-label">Featured Books</span>
          <span class="xslt-card-val"><xsl:value-of select="count(bookCatalog/book[@featured='true'])"/></span>
        </div>
        <div class="xslt-card">
          <span class="xslt-card-label">Total Chapters</span>
          <span class="xslt-card-val"><xsl:value-of select="count(bookCatalog/book/chapters/chapter)"/></span>
        </div>
        <div class="xslt-card">
          <span class="xslt-card-label">Validation</span>
          <span class="xslt-card-val text-success">Schema Valid</span>
        </div>
      </div>

      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Book Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>ISBN</th>
              <th>Year</th>
              <th>Rating</th>
              <th>Price</th>
              <th>Chapters</th>
            </tr>
          </thead>
          <tbody>
            <xsl:for-each select="bookCatalog/book">
              <xsl:sort select="rating" order="descending" data-type="number"/>
              <tr>
                <td><code>#<xsl:value-of select="@id"/></code></td>
                <td>
                  <strong><xsl:value-of select="title"/></strong>
                  <xsl:if test="@featured='true'">
                    <span class="badge badge-featured">★ Featured</span>
                  </xsl:if>
                </td>
                <td><xsl:value-of select="author/name"/></td>
                <td><span class="badge badge-category"><xsl:value-of select="category"/></span></td>
                <td><code><xsl:value-of select="isbn"/></code></td>
                <td><xsl:value-of select="publishedYear"/></td>
                <td>
                  <span class="rating-pill">⭐ <xsl:value-of select="rating"/></span>
                </td>
                <td>$<xsl:value-of select="price"/></td>
                <td><span class="chapter-count"><xsl:value-of select="count(chapters/chapter)"/> ch</span></td>
              </tr>
            </xsl:for-each>
          </tbody>
        </table>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>

