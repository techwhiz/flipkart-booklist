<?
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful

require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';
//
// // Read in a page
$html = scraperwiki::scrape("http://www.flipkart.com/books/fiction-non-fiction/~bestsellers/pr?p%5B%5D=facets.language%255B%255D%3DEnglish&p%5B%5D=facets.binding%255B%255D%3DPaperback&p%5B%5D=facets.availability%255B%255D%3DExclude%2BOut%2Bof%2BStock&p%5B%5D=sort%3Dprice_asc&sid=bks%2Cfnf&filterNone=true");
echo $html;
//
// // Find something on the page using css selectors
// $dom = new simple_html_dom();
// $dom->load($html);
// print_r($dom->find("table.list"));
//
// // Write out to the sqlite database using scraperwiki library
// scraperwiki::save_sqlite(array('name'), array('name' => 'susan', 'occupation' => 'software developer'));
//
// // An arbitrary query against the database
// scraperwiki::select("* from data where 'name'='peter'")

// You don't have to do things with the ScraperWiki library.
// You can use whatever libraries you want: https://morph.io/documentation/php
// All that matters is that your final data is written to an SQLite database
// called "data.sqlite" in the current working directory which has at least a table
// called "data".

$doc = new DOMDocument();
$doc->recover = true;
$doc->strictErrorChecking = false;
$doc->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXpath($doc);

$elements = $xpath->query("//*[@id='products']//*[contains(@class,'product-unit')]");

$products_data=array();
//*[contains(@class,'lu-title-wrapper')]//a
$products_count=1;
if (!is_null($elements)) {
  foreach ($elements as $element) {
    
    echo "[".$products_count."]";
    //file_put_contents('f'.$products_count.'.txt', var_dump($element->childNodes));
    $products_data[$products_count]['isbn']=$element->getElementsByTagName('a')->item(1)->getAttribute('data-pid');
    $products_data[$products_count]['url']=$element->getElementsByTagName('a')->item(0)->getAttribute('href');
    $products_data[$products_count]['title']=$element->getElementsByTagName('a')->item(0)->getElementsByTagName('img')->item(0)->getAttribute('alt'); 
    $products_data[$products_count]['img']=$element->getElementsByTagName('a')->item(0)->getElementsByTagName('img')->item(0)->getAttribute('src'); 
    
    $newDom = new DOMDocument;
    $newDom->appendChild($newDom->importNode($element,1));
    $newDomXpath=new DOMXPath($newDom);
    $priceElements=$newDomXpath->query("//*[contains(@class,'pu-price')]//*[contains(@class,'pu-final')]//span");
    if(!is_null($priceElements))
    {
        foreach($priceElements as $priceElement)
        {
            $products_data[$products_count]['price']=$priceElement->nodeValue;   
        }  
    }        
      echo "\n";
      $products_count++;
    }
    print_r($products_data);
  }
?>
