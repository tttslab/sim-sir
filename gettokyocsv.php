<?php
/*
    MIT License

    Copyright (c) 2020 Takahiro Shinozaki

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.
*/

// Get Tokyo Data
      $url = "https://stopcovid19.metro.tokyo.lg.jp/data/130001_tokyo_covid19_patients.csv";

// Prepare YYYY-MM-DD to index table
$ymd2idx = [];
$idx2ymd = [];
$idx=0;
$mon=3; // Let's start from March 15
$day=15; 
foreach ( [31, 30, 31, 30, 31, 31, 30] as $nday ) { // Num of days in Mar, May, .., Sep, 2020
    for ( $i=$day; $i<=$nday; $i++ ) {
        $ymd = sprintf("2020-%02d-%02d", $mon, $i);
        $ymd2idx[$ymd] = $idx;
        $idx2ymd[$idx] = $ymd;
//        var_dump($ymd);
        $idx++;
    }
    $mon++;
    $day=1;
}

// Get CSV of Tokyo's data
// and count the number of people in a day
$file = new NoRewindIterator( new SplFileObject( $url));
$file->setFlags( SplFileObject::READ_CSV );
$tokyo_ni = [];
$maxidx = 0;
foreach ( $file as $line ) {
//    var_dump($line[4]);
    $key = $line[4];
    if (array_key_exists($key, $ymd2idx)) {
        $idx = $ymd2idx[$key];
        if (array_key_exists($idx, $tokyo_ni)) {
            $tokyo_ni[$idx]++;    
        } else {
            $tokyo_ni[$idx]=1;
        }
        if ($maxidx < $idx) {
            $maxidx = $idx;
        }
    } else {
//        var_dump("Not exist");
    }
}

for ( $idx=0; $idx<$maxidx; $idx++ ) {
    if (array_key_exists($idx, $tokyo_ni)) {
        $num = $tokyo_ni[$idx];
    } else {
        $num = 0;
    }
    $ymd = $idx2ymd[$idx];
    echo "$ymd,$idx,$num\n";
}
?>
