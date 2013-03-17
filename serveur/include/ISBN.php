<?php
/*
isbn.php - ISBN-Formatter
Copyright (C) 2007 by Nico Haase [nico.haase-at-gmx.de] 2007

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation.
http://www.gnu.org/copyleft/lesser.html 


Usage:
$isbn = addHyphens ( 'Your ISBN' );
   -> $isbn is the formatted isbn with hyphens
   -> works also with ISBN13
   
Convert ISBN10 to ISBN13:
$newISBN = convertToISBN13 ( $oldISBN );

and backwards:
$oldISBN = convertToISBN10 ( $newISBN );

last change: 11. April 2007 - now with ISBN13

Changelog:
April 11, 2007:
    Put all functions into a class
    Rechecked ISBN-Ranges (http://www.isbn-international.org/converter/ranges.htm)

January 2007: 
    ISBN 13 introduced

*/


function addHyphens13 ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->addHyphens13 ( $isbn );
 }
 
function addHyphens ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->addHyphens ( $isbn );
 }
 
function getCheckDigit13 ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->getCheckDigit13 ( $isbn );
 }
 
function isbntest13 ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->isbntest13 ( $isbn );
 } 
 
function getCheckDigit10 ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->getCheckDigit10 ( $isbn );
 } 
 
function isbntest ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->isbntest ( $isbn );
 }  
 
function convertToISBN13 ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->convertToISBN13 ( $isbn );
 } 
 
function convertToISBN10 ( $isbn )
 {
     $class = new ISBN_Checker;
     return $class->convertToISBN10 ( $isbn );
 }  

class ISBN_Checker
 {
     private $isbn = '';
     private $firsthyphen = '';
     private $secondhyphen = '';
     
    public function addHyphens13 ( $isbn )
     {
         $isbn = str_replace ( '-', '', $isbn );
         return substr ( $isbn, 0, 3 ) . '-' . $this->addHyphens ( substr ( $isbn, 3 ) );
     }
    
    public function addHyphens ( $isbn )
     {
         $isbn = str_replace ( '-', '', $isbn );
         if ( strlen ( $isbn ) == 13 ) return $this->addHyphens13 ( $isbn );
         if ( strlen ( $isbn ) <> 10 ) return false;
         if ( !is_numeric ( $isbn{9} ) && strtolower ( $isbn{9} ) != 'x' ) return false;
    
        $this->isbn = $isbn;
    
         if ( $this->between ( $isbn{0}, 0, 5 ) ) $firsthyphen = 1;
         elseif ( $isbn{0} == 6 ) $firsthyphen = 3;
         elseif ( $isbn{0} == 7 ) $firsthyphen = 1;
         elseif ( $isbn{0} == 8 ) $firsthyphen = 2;
         elseif ( $isbn{0} == 9 )
          {
              if ( $this->between ( $isbn{1}, 0, 3 ) ) $firsthyphen = 2;
              elseif ( $isbn{1} == 4 ) return false;
              elseif ( $this->between ( $isbn{1}, 5, 8 ) ) $firsthyphen = 3;
              elseif ( $isbn{1} == 9 )
               {
                   if ( $this->between ( $isbn{3}, 4, 8 ) ) $firsthyphen = 4;
                elseif ( $isbn{3} == 9 ) $firsthyphen = 5;
               }
          }
          
         $this->firsthyphen = $firsthyphen;
    
         // zweiten strich herausfinden, dabei nach ländern spalten
         if ( $this->inCountry ( '0' ) )
          {
              $this->inRange ( '00', 19 );
              $this->inRange ( 200, 699 );
              $this->inRange ( 7000, 8499 );
              $this->inRange ( 85000, 89999 );
              $this->inRange ( 900000, 949999 );
              $this->inRange ( 9500000, 9999999 );
          }
         elseif ( $this->inCountry ( '1' ) )
          {
            $this->inRange ( '00', '09' );
              $this->inRange ( 100, 399 );
              $this->inRange ( 4000, 5499 );
              $this->inRange ( 55000, 86979 );
              $this->inRange ( 869800, 998999 );
          }
         elseif ( $this->inCountry ( '2' ) )
          {    
              $this->inRange ( '00', 19 );
              $this->inRange ( 200, 349 );
              $this->inRange ( 35000, 39999 );
              $this->inRange ( 400, 699 );
              $this->inRange ( 7000, 8399 );
              $this->inRange ( 84000, 89999 );
              $this->inRange ( 900000, 949999 );
              $this->inRange ( 9500000, 9999999 );
          }
         elseif ( $this->inCountry ( '3' ) )
          {
            $this->inRange ( '00', '02' );
              $this->inRange ( '030', '033' );
              $this->inRange ( '0340', '0369' );
              $this->inRange ( '03700', '03999' );
              $this->inRange ( '04', 19 );
              $this->inRange ( 200, 699 );
              $this->inRange ( 7000, 8499 );
              $this->inRange ( 85000, 89999 );
              $this->inRange ( 900000, 949999 );
              $this->inRange ( 9500000, 9999999 );
          }
         elseif ( $this->inCountry ( '4' ) )
          {
             $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 900000, 949999 );
            $this->inRange ( 9500000, 9999999 );
          }
         elseif ( $this->inCountry ( '5' ) )
          {
              $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 900000, 909999 );
            $this->inRange ( 91000, 91999 );
            $this->inRange ( 9200, 9299 );
            $this->inRange ( 93000, 94999 );
            $this->inRange ( 9500, 9799 );
            $this->inRange ( 98000, 98999 );
            $this->inRange ( 9900000, 9909999 );
            $this->inRange ( 9910, 9999 );
          }
         elseif ( $this->inCountry ( '600' ) )
          {
               // Iran
            $this->inRange ( '00', '09' );
            $this->inRange ( 100, 499 );
            $this->inRange ( 5000, 8999 );
            $this->inRange ( 90000, 99999 );
          }
         elseif ( $this->inCountry ( '601' ) )
          {
              // Kazakhstan
              $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 7999 );
            $this->inRange ( 80000, 84999 );
            $this->inRange ( 85, 99 );
          }
         elseif ( $this->inCountry ( '602' ) )
          {
              // Indonesia
              if ( $this->between ( substr ( $isbn, 3, 2 ), 0, 19 ) ) $secondhyphen = 2;
              elseif ( $this->between ( substr ( $isbn, 3, 3 ), 200, 799 ) ) $secondhyphen = 3;
              elseif ( $this->between ( substr ( $isbn, 3, 4 ), 8000, 9499 ) ) $secondhyphen = 4;
              elseif ( $this->between ( substr ( $isbn, 3, 5 ), 95000, 99999 ) ) $secondhyphen = 5;
          }
         elseif ( $this->inCountry ( '7' ) )
          {
             // China, People's Republic
              $this->inRange ( '00', '09' );
            $this->inRange ( 100, 499 );
            $this->inRange ( 5000, 7999 );
            $this->inRange ( 80000, 89999 );
            $this->inRange ( 900000, 999999 );        
          }
         elseif ( $this->inCountry ( '80' ) )
          {
             // Czech Republic, Slovakia
            $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 900000, 999999 );
          }
         elseif ( $this->inCountry ( '81' ) )
          {
            $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 900000, 999999 );
          }
         elseif ( $this->inCountry ( '82' ) )
          {
            $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 8999 );
            $this->inRange ( 90000, 98999 );
            $this->inRange ( 990000, 999999 );
          }
         elseif ( $this->inCountry ( '83' ) )
          {
            $this->inRange ( '00', 19 );
            $this->inRange ( 200, 599 );
            $this->inRange ( 60000, 69999 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 900000, 999999 );
          }
         elseif ( $this->inCountry ( '84' ) )
          {
            $this->inRange ( '00', 19 );
            $this->inRange ( 200, 699 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 9000, 9199 );
            $this->inRange ( 920000, 923999 );
            $this->inRange ( 92400, 92999 );
            $this->inRange ( 930000, 949999 );
            $this->inRange ( 95000, 96999 );
            $this->inRange ( 9700, 9999 );
          }
         elseif ( $this->inCountry ( '85' ) )
          {
            $this->inRange ( '00', 19 );
            $this->inRange ( 200, 599 );
            $this->inRange ( 60000, 69999 );
            $this->inRange ( 7000, 8499 );
            $this->inRange ( 85000, 89999 );
            $this->inRange ( 900000, 979999 );
            $this->inRange ( 98000, 99999 );
          }
         elseif ( $this->inCountry ( '86' ) )
          {
            $this->inRange ( '00', 29 );
            $this->inRange ( 300, 599 );
            $this->inRange ( 6000, 7999 );
            $this->inRange ( 80000, 89999 );
            $this->inRange ( 900000, 999999 );
          }
         elseif ( $this->inCountry ( '87' ) )
          {
                        // Denmark
                        if ( $isbn{2} >= 0 && $isbn{2} <= 2 ) $secondhyphen = 2;
                        elseif ( substr ( $isbn, 2, 3 ) >= 400 && substr ( $isbn, 2, 3 ) <= 649 ) $secondhyphen = 3;
                        elseif ( $isbn{2} == 7 ) $secondhyphen = 4;
                        elseif ( substr ( $isbn, 2, 5 ) >= 85000 && substr ( $isbn, 2, 5 ) <= 94999 ) $secondhyphen = 5;
                        elseif ( substr ( $isbn, 2, 6 ) >= 970000 && substr ( $isbn, 2, 6 ) <= 979999 ) $secondhyphen = 6;
          }
         elseif ( $this->inCountry ( '88' ) )
          {
                        // Italian speaking area
                        if ( $isbn{2} >= 0 && $isbn{2} <= 1 ) $secondhyphen = 2;
                        elseif ( $isbn{2} >= 2 && $isbn{2} <= 5 ) $secondhyphen = 3;
                        elseif ( substr ( $isbn, 2, 4 ) >= 6000 && substr ( $isbn, 2, 4 ) <= 8499 ) $secondhyphen = 4;
                        elseif ( substr ( $isbn, 2, 5 ) >= 85000 && substr ( $isbn, 2, 5 ) <= 89999 ) $secondhyphen = 5;
                        elseif ( substr ( $isbn, 2, 6 ) >= 900000 && substr ( $isbn, 2, 6 ) <= 949999 ) $secondhyphen = 6;
                        elseif ( substr ( $isbn, 2, 5 ) >= 95000 && substr ( $isbn, 2, 5 ) <= 99999 ) $secondhyphen = 5;
          }
         elseif ( $this->inCountry ( '89' ) )
          {
                        // Korea
                        if ( substr ( $isbn, 2, 2 ) <= 24 ) $secondhyphen = 2;
                        elseif ( $this->between ( substr ( $isbn, 2, 3 ), 250, 549 ) ) $secondhyphen = 3;
                        elseif ( $this->between ( substr ( $isbn, 2, 4 ), 5500, 8499 ) ) $secondhyphen = 4;
                        elseif ( $this->between ( substr ( $isbn, 2, 5 ), 85000, 94999 ) ) $secondhyphen = 5;
                        elseif ( $this->between ( substr ( $isbn, 2, 6 ), 950000, 999999 ) ) $secondhyphen = 6;
                        break;
          }
         elseif ( $this->inCountry ( '90' ) )
          {
                        // Netherlands, Belgium ( Flemish)
                        if ( $this->between ( $isbn{2}, 0, 1 ) ) $secondhyphen = 2;
                        elseif ( $this->between ( $isbn{2}, 2, 4 ) ) $secondhyphen = 3;
                        elseif ( $this->between ( $isbn{2}, 5, 6 ) ) $secondhyphen = 4;
                        elseif ( $isbn{2} == 7 ) $secondhyphen = 5;
                        elseif ( $this->between ( substr ( $isbn, 2, 6 ), 800000, 849999 ) ) $secondhyphen = 6;
                        elseif ( $this->between ( substr ( $isbn, 2, 4 ), 8500, 8999 ) ) $secondhyphen = 4;
                        elseif ( $this->between ( substr ( $isbn, 2, 6 ), 900000, 909999 ) ) $secondhyphen = 6;
                        elseif ( $this->between ( substr ( $isbn, 2, 6 ), 940000, 949999 ) ) $secondhyphen = 6;
          }
         elseif ( $this->inCountry ( '91' ) )
          {
                        // Sweden
                        if ( $this->between ( $isbn{2}, 0, 1 ) ) $secondhyphen = 1;
                        elseif ( $this->between ( $isbn{2}, 2, 4 ) ) $secondhyphen = 2;
                        elseif ( $this->between ( substr ( $isbn, 2, 3 ), 500, 649 ) ) $secondhyphen = 3;
                        elseif ( $isbn{2} == 7 ) $secondhyphen = 4;
                        elseif ( $this->between ( substr ( $isbn, 2, 5 ), 85000, 94999 ) ) $secondhyphen = 5;
                        elseif ( $this->between ( substr ( $isbn, 2, 6 ), 970000, 999999 ) ) $secondhyphen = 6;
         }

        if ( $this->secondhyphen != '' ) $secondhyphen = $this->secondhyphen;    

        if ( !isset ( $firsthyphen ) || !isset ( $secondhyphen ) )
         return $isbn;
        
         $returnisbn = substr ( $isbn, 0, $firsthyphen )
          . "-"
          . substr ( $isbn, $firsthyphen, $secondhyphen )
          . "-"
          . substr ( $isbn, ( $secondhyphen + $firsthyphen ), ( strlen ( $isbn ) - $secondhyphen - $firsthyphen - 1 ) )
          //. substr ( $isbn, ( $secondhyphen + $firsthyphen - 1 ), ( strlen ( $isbn ) - $secondhyphen - $firsthyphen ) )
          . "-"
          . substr ( $isbn, -1 );
         return $returnisbn;
     }
    
    private function between ( $wert, $unten, $oben )
     {
         if ( $unten > $oben )
          {
              $zwischen = $unten;
              $unten = $oben;
               $oben = $zwischen;
          }
        return ( $wert >= $unten && $wert <= $oben );
     }
     
    private function inCountry ( $prefix )
     {
         $laenge = strlen ( $prefix );
         if ( substr ( $this->isbn, 0, $laenge ) == $prefix )
          {
              $this->firsthyphen = $laenge;
              return true;
          }
          
     } 
     
    private function inRange ( $from, $to )
     {
        $laenge = strlen ( $from );
        $zuPruefen = substr ( $this->isbn, $this->firsthyphen, $laenge );
        if ( $this->between ( $zuPruefen, (int)$from, (int)$to ) )
         {
             $this->secondhyphen = $laenge;
         }
     }
         
    
    public function getCheckDigit13 ( $isbn )
     {
         $isbntest = preg_replace('/[^0-9|x|X]/i', '', $isbn);
         if ( strlen ( $isbntest ) <> 13 ) return false;
         $checkdigit = 0;
         for ( $zaehler = 1; $zaehler < 13; $zaehler++ )
          {
              if ( $zaehler % 2 == 0 ) 
               {
                   $checkdigit += $isbntest{$zaehler-1} * 3;
               }
              else
               {
                   $checkdigit += $isbntest{$zaehler-1} * 1;
               }
          }
         $return = 10 - ( $checkdigit % 10 );
         if ( $return == 10 ) $return = 0;
         return $return;
     }
    
    public function isbntest13 ( $isbn )
     {
         $isbntest = preg_replace('/[^0-9|x|X]/i', '', $isbn);
         if ( strlen ( $isbntest ) <> 13 ) return false;
         if ( $isbntest{12} == $this->getCheckDigit13 ( $isbntest ) ) return true;
          else return false;
         
     }    
     
    public function getCheckDigit10 ( $isbn )
     {
         $nummer = 0;
    
         for ($i = 0; $i < 9; $i++)
          {
              $nummer = $nummer + ((substr($isbn,$i,1)) * (10 - $i));
          }
    
         $rest = $nummer % 11;
         if ($rest == 0) $rest = 11;
         $ergebnis = 11 - $rest;
         if ($ergebnis == 10) $ergebnis = 'X';
         return $ergebnis;
     }
    
    public function isbntest ($isbn)
    {
        // ISBN auf Syntax checken
        $isbntest = preg_replace('/[^0-9|x|X]/i', '', $isbn);
    
        if ( strlen ( $isbntest) == 13 ) return $this->isbntest13 ( $isbn );
         if ( strlen ( $isbntest ) <> 10) return false;
    
         if (substr($isbntest, -1) != $this->getCheckDigit10($isbntest)) return FALSE;
          else return TRUE;
     }
     
    public function convertToISBN13 ( $isbn )
     {
        $neueISBN = '978' . substr ( $isbn, 0, -1 );
         $neueISBN .= $this->getCheckDigit13 ( $neueISBN . '0' );
         return $neueISBN;                      
     }
     
    public function convertToISBN10 ( $isbn )
     {
        $alteISBN = substr ( $isbn, 3, 9 );
        $alteISBN .= $this->getCheckDigit10 ( $alteISBN );
        return $alteISBN;
     }
 }


if ( isset ( $_GET['testaisbn'] ) )
 {
     echo 'die isbn ' . strip_tags($_GET['testaisbn']) . ' sieht formatiert so aus: <b>' . addHyphens ( strip_tags($_GET['testaisbn']) ) . '</b> ';
    echo 'und ist eine ' . ( isbnTest ( $_GET['testaisbn'] ) ? '' : 'un' ) . 'g&uuml;ltige ISBN<br />'
      . '<form action = "isbn.php" method = "get">zum testen einfach hier eine isbn eingeben:'
      . ' <input type = "text" name = "testaisbn" value = "' .  strip_tags($_GET['testaisbn']) . '"> <input type = "submit" value = "los!"></form>';
        exit();
 }
?>