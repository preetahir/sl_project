<?php
namespace App\Http\Controllers;

use App\Models\Book;
use DOMDocument;
use Illuminate\Http\Response;

class XmlFeedController extends Controller {
    public function rss() {
        $books = Book::with('author')->latest()->take(20)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>LibrisCMS Book Catalog Feed</title>';
        $xml .= '<link>' . url('/') . '</link>';
        $xml .= '<description>Latest books published in LibrisCMS</description>';
        $xml .= '<language>en-us</language>';
        $xml .= '<atom:link href="' . url('/feed.xml') . '" rel="self" type="application/rss+xml" />';

        foreach ($books as $b) {
            $xml .= '<item>';
            $xml .= '<title>' . htmlspecialchars($b->title) . '</title>';
            $xml .= '<link>' . route('books.show', $b->slug) . '</link>';
            $xml .= '<guid>' . route('books.show', $b->slug) . '</guid>';
            $xml .= '<author>' . htmlspecialchars($b->author->name ?? 'Author') . '</author>';
            $xml .= '<description>' . htmlspecialchars($b->summary) . '</description>';
            $xml .= '<pubDate>' . $b->created_at->toRssString() . '</pubDate>';
            $xml .= '</item>';
        }

        $xml .= '</channel></rss>';

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    public function sitemap() {
        $books = Book::all();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $xml .= '<url><loc>' . url('/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';

        foreach ($books as $b) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('books.show', $b->slug) . '</loc>';
            $xml .= '<lastmod>' . ($b->updated_at ? $b->updated_at->toAtomString() : date('c')) . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function export() {
        $books = Book::with(['category', 'author', 'chapters'])->get();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $catalog = $dom->createElement('catalog');
        $catalog->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $catalog->setAttribute('xsi:noNamespaceSchemaLocation', 'schema.xsd');
        $catalog->setAttribute('generated_at', date('c'));
        $dom->appendChild($catalog);

        foreach ($books as $b) {
            $bookElem = $dom->createElement('book');
            $bookElem->setAttribute('id', 'BK' . str_pad($b->id, 3, '0', STR_PAD_LEFT));
            $bookElem->setAttribute('category', $b->category->slug ?? 'general');

            $title = $dom->createElement('title');
            $title->appendChild($dom->createTextNode($b->title));
            $bookElem->appendChild($title);

            $author = $dom->createElement('author');
            $author->appendChild($dom->createTextNode($b->author->name ?? 'Unknown'));
            $bookElem->appendChild($author);

            $isbn = $dom->createElement('isbn', $b->isbn);
            $bookElem->appendChild($isbn);

            $genre = $dom->createElement('genre', htmlspecialchars($b->category->name ?? 'General'));
            $bookElem->appendChild($genre);

            $year = $dom->createElement('year', (string)$b->published_year);
            $bookElem->appendChild($year);

            $price = $dom->createElement('price', number_format((float)$b->price, 2, '.', ''));
            $bookElem->appendChild($price);

            $rating = $dom->createElement('rating', number_format((float)$b->rating, 1, '.', ''));
            $bookElem->appendChild($rating);

            $summary = $dom->createElement('summary');
            $summary->appendChild($dom->createTextNode($b->summary));
            $bookElem->appendChild($summary);

            if ($b->chapters->isNotEmpty()) {
                $chaptersElem = $dom->createElement('chapters');
                foreach ($b->chapters as $ch) {
                    $chElem = $dom->createElement('chapter');
                    $chElem->setAttribute('number', (string)$ch->chapter_number);
                    $chElem->setAttribute('title', $ch->title);
                    $chElem->appendChild($dom->createCDATASection($ch->content));
                    $chaptersElem->appendChild($chElem);
                }
                $bookElem->appendChild($chaptersElem);
            }

            $catalog->appendChild($bookElem);
        }

        $xmlOutput = $dom->saveXML();

        return response($xmlOutput, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="books-catalog-' . date('Y-m-d') . '.xml"'
        ]);
    }
}
